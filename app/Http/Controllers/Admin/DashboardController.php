<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Screen;
use App\Models\ScreenOnboardingRequest;
use App\Models\SyncRun;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $screens = Screen::with('tags')->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => [
                'newLeads' => Lead::where('status', 'new')->orWhere('status', 'nuevo')->count(),
                'weekLeads' => Lead::where('created_at', '>=', now()->subWeek())->count(),
                'pendingLeads' => Lead::whereNotIn('status', ['ganado', 'perdido', 'descartado'])->count(),
                'visibleScreens' => Screen::publiclyVisible()->count(),
                'incompleteScreens' => $screens->filter->missingFields()->count(),
                'totalScreens' => $screens->count(),
                'availableScreens' => $screens->where('commercial_status', 'disponible')->count(),
                'pendingOnboarding' => ScreenOnboardingRequest::whereIn('status', ['borrador', 'pendiente_revision', 'aprobado', 'error_xibo'])->count(),
                'venueLeads' => Lead::where('type', 'venue')->count(),
                'advertiserLeads' => Lead::where('type', 'advertiser')->count(),
            ],
            'lastSync' => SyncRun::latest('started_at')->first(),
            'recentLeads' => Lead::query()->latest()->limit(5)->get()->map(fn (Lead $lead) => [
                'id' => $lead->id,
                'name' => $lead->business_name ?: $lead->company_name ?: 'Consulta',
                'type' => $lead->type,
                'status' => $lead->status,
                'createdAt' => $lead->created_at?->toDateTimeString(),
            ]),
        ]);
    }
}
