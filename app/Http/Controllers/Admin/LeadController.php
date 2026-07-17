<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LeadResponse;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\ResponseTemplate;
use App\Models\Screen;
use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    private const STATUSES = ['new', 'nuevo', 'contactado', 'cita_agendada', 'en_estudio', 'ganado', 'perdido', 'descartado'];

    public function index(Request $request): Response
    {
        $request->validate([
            'type' => ['nullable', Rule::in(['venue', 'advertiser', 'other'])],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'q' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'province' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:120'],
            'contact_method' => ['nullable', Rule::in(['llamada', 'email', 'whatsapp', 'indiferente'])],
            'screen_id' => ['nullable', 'integer', 'exists:screens,id'],
        ]);

        $query = $this->filteredQuery($request)->with(['screens', 'latestActivity'])->latest();

        return Inertia::render('Admin/Leads', [
            'leads' => $query->paginate(25)->withQueryString()->through(fn (Lead $lead) => $this->payload($lead)),
            'filters' => $request->only(['type', 'status', 'q', 'from', 'to', 'province', 'municipality', 'budget', 'contact_method', 'screen_id']),
            'statuses' => self::STATUSES,
            'filterOptions' => [
                'provinces' => Lead::query()->whereNotNull('province')->distinct()->orderBy('province')->pluck('province'),
                'municipalities' => Lead::query()->whereNotNull('municipality')->distinct()->orderBy('municipality')->pluck('municipality'),
                'budgets' => Lead::query()->whereNotNull('budget_range')->distinct()->orderBy('budget_range')->pluck('budget_range'),
                'screens' => Screen::query()->orderBy('display_name')->get(['id', 'display_name']),
            ],
        ]);
    }

    public function show(Lead $lead): Response
    {
        return Inertia::render('Admin/LeadDetail', [
            'lead' => $this->payload($lead->load(['screens', 'activities.user'])),
            'statuses' => self::STATUSES,
            'templates' => ResponseTemplate::query()
                ->active()
                ->where('locale', $lead->locale)
                ->where(fn (Builder $query) => $query->whereNull('lead_type')->orWhere('lead_type', $lead->type))
                ->orderBy('name')
                ->get(['id', 'name', 'subject']),
        ]);
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $old = ['status' => $lead->status];
        $lead->update(['status' => $data['status']]);
        $lead->activities()->create([
            'user_id' => $request->user()?->id,
            'action' => 'status_changed',
            'description' => "Estado cambiado de {$old['status']} a {$lead->status}.",
        ]);

        AuditLogger::record('lead.status_changed', $lead, $old, ['status' => $lead->status], $request);

        return back();
    }

    public function resend(Request $request, Lead $lead): RedirectResponse
    {
        Mail::to(Setting::getValue('leads_email', config('services.elixe.leads_email')))->queue(new NewLeadNotification($lead));
        $lead->activities()->create([
            'user_id' => $request->user()?->id,
            'action' => 'internal_email_resent',
            'description' => 'Notificación interna reenviada.',
        ]);
        AuditLogger::record('lead.email_resent', $lead, [], [], $request);

        return back()->with('success', 'Notificación interna reenviada.');
    }

    public function sendResponse(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'response_template_id' => ['required', 'integer', Rule::exists('response_templates', 'id')->where('is_active', true)],
        ]);
        $template = ResponseTemplate::findOrFail($data['response_template_id']);

        abort_unless($template->locale === $lead->locale && ($template->lead_type === null || $template->lead_type === $lead->type), 422);

        Mail::to($lead->email)->queue(new LeadResponse($lead, $template));
        $lead->activities()->create([
            'user_id' => $request->user()?->id,
            'action' => 'response_sent',
            'description' => "Respuesta enviada con la plantilla «{$template->name}».",
            'metadata' => ['response_template_id' => $template->id],
        ]);
        AuditLogger::record('lead.response_sent', $lead, [], ['response_template_id' => $template->id], $request);

        return back()->with('success', 'Respuesta comercial encolada para envío.');
    }

    public function export(Request $request): StreamedResponse
    {
        AuditLogger::record('lead.csv_exported', null, [], $request->only([
            'type',
            'status',
            'q',
            'from',
            'to',
            'province',
            'municipality',
            'budget',
            'contact_method',
            'screen_id',
        ]), $request);

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['id', 'type', 'status', 'name', 'contact', 'email', 'phone', 'province', 'municipality', 'created_at'], ',', '"', '');

            $this->filteredQuery($request)
                ->latest()
                ->chunk(100, function ($leads) use ($handle) {
                    foreach ($leads as $lead) {
                        fputcsv($handle, [
                            $lead->id,
                            $this->csvCell($lead->type),
                            $this->csvCell($lead->status),
                            $this->csvCell($lead->business_name ?: $lead->company_name),
                            $this->csvCell($lead->contact_name),
                            $this->csvCell($lead->email),
                            $this->csvCell($lead->phone),
                            $this->csvCell($lead->province),
                            $this->csvCell($lead->municipality ?: $lead->city),
                            $lead->created_at?->toDateTimeString(),
                        ], ',', '"', '');
                    }
                });

            fclose($handle);
        }, 'leads-elixe-'.now()->format('Y-m-d').'.csv');
    }

    private function payload(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'type' => $lead->type,
            'status' => $lead->status,
            'name' => $lead->business_name ?: $lead->company_name ?: 'Consulta',
            'contactName' => $lead->contact_name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'province' => $lead->province,
            'municipality' => $lead->municipality ?: $lead->city,
            'message' => $lead->message,
            'activitySector' => $lead->activity_sector ?: $lead->sector,
            'interestZone' => $lead->interest_zone,
            'budgetRange' => $lead->budget_range,
            'locationType' => $lead->location_type,
            'hasScreen' => $lead->has_screen,
            'wantsElixeScreen' => $lead->wants_elixe_screen,
            'wantsAdControl' => $lead->wants_ad_control,
            'preferredContactMethod' => $lead->preferred_contact_method,
            'preferredCallTime' => $lead->preferred_call_time,
            'locale' => $lead->locale,
            'screens' => $lead->screens->map(fn ($screen) => $screen->public_name ?: $screen->display_name)->values(),
            'createdAt' => $lead->created_at?->toDateTimeString(),
            'lastAction' => $lead->latestActivity?->description,
            'activities' => $lead->relationLoaded('activities')
                ? $lead->activities->map(fn ($activity) => [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'user' => $activity->user?->name ?: 'Sistema',
                    'createdAt' => $activity->created_at?->toDateTimeString(),
                ])->values()
                : [],
        ];
    }

    private function filteredQuery(Request $request): Builder
    {
        return Lead::query()
            ->when($request->filled('type'), fn (Builder $query) => $query->where('type', $request->string('type')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn (Builder $search) => $search
                    ->where('business_name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('contact_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term));
            })
            ->when($request->filled('from'), fn (Builder $query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn (Builder $query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('province'), fn (Builder $query) => $query->where('province', $request->string('province')))
            ->when($request->filled('municipality'), fn (Builder $query) => $query->where('municipality', $request->string('municipality')))
            ->when($request->filled('budget'), fn (Builder $query) => $query->where('budget_range', $request->string('budget')))
            ->when($request->filled('contact_method'), fn (Builder $query) => $query->where('preferred_contact_method', $request->string('contact_method')))
            ->when($request->filled('screen_id'), fn (Builder $query) => $query->whereHas('screens', fn (Builder $screen) => $screen->whereKey($request->integer('screen_id'))));
    }

    private function csvCell(?string $value): ?string
    {
        return $value !== null && preg_match('/^[=+\-@]/', $value) ? "'{$value}" : $value;
    }
}
