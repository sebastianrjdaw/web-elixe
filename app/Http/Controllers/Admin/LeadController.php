<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Support\AuditLogger;
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
        $query = Lead::query()->with('screens')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return Inertia::render('Admin/Leads', [
            'leads' => $query->paginate(25)->through(fn (Lead $lead) => $this->payload($lead)),
            'filters' => $request->only(['type', 'status']),
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Lead $lead): Response
    {
        return Inertia::render('Admin/LeadDetail', [
            'lead' => $this->payload($lead->load('screens')),
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $old = ['status' => $lead->status];
        $lead->update(['status' => $data['status']]);

        AuditLogger::record('lead.status_changed', $lead, $old, ['status' => $lead->status], $request);

        return back();
    }

    public function resend(Lead $lead): RedirectResponse
    {
        Mail::to(config('services.elixe.leads_email'))->queue(new NewLeadNotification($lead));
        AuditLogger::record('lead.email_resent', $lead);

        return back();
    }

    public function export(Request $request): StreamedResponse
    {
        AuditLogger::record('lead.csv_exported', null, [], $request->only(['type', 'status']), $request);

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'type', 'status', 'name', 'contact', 'email', 'phone', 'province', 'municipality', 'created_at']);

            Lead::query()
                ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
                ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
                ->latest()
                ->chunk(100, function ($leads) use ($handle) {
                    foreach ($leads as $lead) {
                        fputcsv($handle, [
                            $lead->id,
                            $lead->type,
                            $lead->status,
                            $lead->business_name ?: $lead->company_name,
                            $lead->contact_name,
                            $lead->email,
                            $lead->phone,
                            $lead->province,
                            $lead->municipality ?: $lead->city,
                            $lead->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($handle);
        }, 'leads-elixe.csv');
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
            'preferredContactMethod' => $lead->preferred_contact_method,
            'preferredCallTime' => $lead->preferred_call_time,
            'screens' => $lead->screens->map(fn ($screen) => $screen->public_name ?: $screen->display_name)->values(),
            'createdAt' => $lead->created_at?->toDateTimeString(),
        ];
    }
}
