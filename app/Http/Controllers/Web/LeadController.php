<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertiserLeadRequest;
use App\Http\Requests\StoreAdviceLeadRequest;
use App\Http\Requests\StoreVenueLeadRequest;
use App\Mail\LeadConfirmation;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function storeAdvice(StoreAdviceLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $selectedScreenIds = $data['selected_screen_ids'] ?? [];
        unset($data['selected_screen_ids'], $data['privacy_accepted'], $data['cf_turnstile_response']);

        $lead = Lead::create(array_merge($data, [
            'status' => 'nuevo',
            'privacy_accepted_at' => now(),
            'captcha_verified_at' => config('services.turnstile.enabled') ? now() : null,
        ]));

        if ($lead->type === 'advertiser') {
            $lead->screens()->sync($selectedScreenIds);
        }

        $this->sendLeadEmails($lead);

        return redirect()->route('thanks');
    }

    public function storeVenue(StoreVenueLeadRequest $request): RedirectResponse
    {
        $lead = Lead::create(array_merge($request->validated(), [
            'type' => 'venue',
            'status' => 'nuevo',
            'privacy_accepted_at' => now(),
        ]));

        $this->sendLeadEmails($lead);

        return redirect()->route('thanks');
    }

    public function storeAdvertiser(StoreAdvertiserLeadRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $lead = Lead::create(array_merge($data, [
            'type' => 'advertiser',
            'status' => 'nuevo',
            'privacy_accepted_at' => now(),
        ]));

        $lead->screens()->sync($data['selected_screen_ids'] ?? []);

        $this->sendLeadEmails($lead);

        return redirect()->route('thanks');
    }

    private function sendLeadEmails(Lead $lead): void
    {
        Mail::to(Setting::getValue('leads_email', config('services.elixe.leads_email')))->queue(new NewLeadNotification($lead));
        Mail::to($lead->email)->queue(new LeadConfirmation($lead));
    }
}
