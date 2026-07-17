<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdviceLeadRequest;
use App\Mail\LeadConfirmation;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\Screen;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function redirectLegacyVenue(): RedirectResponse
    {
        return redirect()->route('advice', ['tipo' => 'venue'])
            ->with('error', 'El formulario se ha actualizado. Revisa y envía tu solicitud desde esta página.');
    }

    public function redirectLegacyAdvertiser(): RedirectResponse
    {
        return redirect()->route('advice', ['tipo' => 'advertiser'])
            ->with('error', 'El formulario se ha actualizado. Revisa y envía tu solicitud desde esta página.');
    }

    public function storeAdvice(StoreAdviceLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $selectedScreenIds = Screen::query()
            ->publiclyVisible()
            ->whereIn('public_id', $data['selected_screen_ids'] ?? [])
            ->pluck('id');
        unset($data['selected_screen_ids'], $data['privacy_accepted'], $data['cf_turnstile_response']);

        $submissionToken = $data['submission_token'] ?? (string) Str::uuid();
        $data['submission_token'] = $submissionToken;

        [$lead, $created] = DB::transaction(function () use ($data, $selectedScreenIds): array {
            $lead = Lead::firstOrCreate(
                ['submission_token' => $data['submission_token']],
                array_merge($data, [
                    'status' => 'nuevo',
                    'locale' => app()->getLocale(),
                    'privacy_accepted_at' => now(),
                    'captcha_verified_at' => config('services.turnstile.enabled') ? now() : null,
                ]),
            );

            if ($lead->wasRecentlyCreated) {
                if ($lead->type === 'advertiser') {
                    $lead->screens()->sync($selectedScreenIds);
                }

                $lead->activities()->create([
                    'action' => 'created',
                    'description' => 'Solicitud recibida desde la web.',
                ]);
            }

            return [$lead, $lead->wasRecentlyCreated];
        });

        if ($created) {
            DB::afterCommit(fn () => $this->sendLeadEmails($lead));
        }

        return redirect()->route(app()->getLocale() === 'gl' ? 'gl.thanks' : 'thanks');
    }

    private function sendLeadEmails(Lead $lead): void
    {
        Mail::to(Setting::getValue('leads_email', config('services.elixe.leads_email')))->queue(new NewLeadNotification($lead));
        Mail::to($lead->email)->queue(new LeadConfirmation($lead));
    }
}
