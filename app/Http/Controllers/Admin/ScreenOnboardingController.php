<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScreenOnboardingRequest;
use App\Models\ScreenOnboardingRequest;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ScreenOnboardingController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ScreenOnboardingRequest::with(['creator:id,name', 'reviewer:id,name'])->latest();
        if ($request->filled('status')) $query->where('status', $request->string('status'));

        return Inertia::render('Admin/ScreenOnboardingIndex', [
            'requests' => $query->paginate(25)->withQueryString()->through(fn ($item) => $this->payload($item)),
            'statuses' => ScreenOnboardingRequest::STATUSES,
            'filters' => $request->only('status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ScreenOnboardingForm', $this->formPayload());
    }

    public function store(StoreScreenOnboardingRequest $request): RedirectResponse
    {
        $item = new ScreenOnboardingRequest($request->validated());
        $item->created_by = $request->user()->id;
        $item->save();
        AuditLogger::record('screen_onboarding.created', $item, [], ['status' => $item->status], $request);
        return redirect()->route('admin.screen-onboarding.edit', $item)->with('success', 'Borrador guardado.');
    }

    public function edit(ScreenOnboardingRequest $screenOnboarding): Response
    {
        return Inertia::render('Admin/ScreenOnboardingForm', $this->formPayload($screenOnboarding));
    }

    public function update(StoreScreenOnboardingRequest $request, ScreenOnboardingRequest $screenOnboarding): RedirectResponse
    {
        abort_unless(in_array($screenOnboarding->status, ['borrador', 'pendiente_revision'], true), 409, 'Esta solicitud ya no se puede editar.');
        $old = $screenOnboarding->only(array_keys($request->validated()));
        $screenOnboarding->update($request->validated());
        AuditLogger::record('screen_onboarding.updated', $screenOnboarding, $old, $screenOnboarding->only(array_keys($request->validated())), $request);
        return back()->with('success', 'Cambios guardados.');
    }

    public function submit(Request $request, ScreenOnboardingRequest $screenOnboarding): RedirectResponse
    {
        abort_unless($screenOnboarding->status === 'borrador', 409);
        if ($blockers = $screenOnboarding->preparationBlockers()) throw ValidationException::withMessages(['onboarding' => $blockers]);
        $screenOnboarding->update(['status' => 'pendiente_revision']);
        AuditLogger::record('screen_onboarding.submitted', $screenOnboarding, ['status' => 'borrador'], ['status' => 'pendiente_revision'], $request);
        return back()->with('success', 'Solicitud enviada a revisión.');
    }

    public function approve(Request $request, ScreenOnboardingRequest $screenOnboarding): RedirectResponse
    {
        abort_unless($screenOnboarding->status === 'pendiente_revision', 409);
        if ($blockers = $screenOnboarding->preparationBlockers()) throw ValidationException::withMessages(['onboarding' => $blockers]);
        $screenOnboarding->update(['status' => 'aprobado', 'reviewed_by' => $request->user()->id, 'approved_at' => now()]);
        AuditLogger::record('screen_onboarding.approved', $screenOnboarding, ['status' => 'pendiente_revision'], ['status' => 'aprobado'], $request);
        return back()->with('success', 'Alta aprobada y preparada. La escritura en Xibo sigue desactivada hasta verificar Swagger.');
    }

    public function discard(Request $request, ScreenOnboardingRequest $screenOnboarding): RedirectResponse
    {
        abort_if(in_array($screenOnboarding->status, ['enviado_a_xibo', 'activo'], true), 409);
        $old = $screenOnboarding->status;
        $screenOnboarding->update(['status' => 'descartado']);
        AuditLogger::record('screen_onboarding.discarded', $screenOnboarding, ['status' => $old], ['status' => 'descartado'], $request);
        return redirect()->route('admin.screen-onboarding.index')->with('success', 'Solicitud descartada.');
    }

    private function formPayload(?ScreenOnboardingRequest $item = null): array
    {
        return [
            'onboarding' => $item ? $this->payload($item) : null,
            'options' => ['locationTypes' => ScreenOnboardingRequest::LOCATION_TYPES, 'locationSectors' => ScreenOnboardingRequest::LOCATION_SECTORS, 'commercialStatuses' => ScreenOnboardingRequest::COMMERCIAL_STATUSES],
            'xiboWriteAvailable' => false,
        ];
    }

    private function payload(ScreenOnboardingRequest $item): array
    {
        return [...$item->toArray(), 'creatorName' => $item->creator?->name, 'reviewerName' => $item->reviewer?->name, 'blockers' => $item->preparationBlockers()];
    }
}
