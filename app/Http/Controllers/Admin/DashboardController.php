<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Screen;
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
                'visibleScreens' => Screen::publiclyVisible()->count(),
                'incompleteScreens' => $screens->filter->missingFields()->count(),
                'venueLeads' => Lead::where('type', 'venue')->count(),
                'advertiserLeads' => Lead::where('type', 'advertiser')->count(),
            ],
            'lastSync' => SyncRun::latest('started_at')->first(),
        ]);
    }
}
