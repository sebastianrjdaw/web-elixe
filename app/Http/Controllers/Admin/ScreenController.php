<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Services\Xibo\SyncDisplays;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScreenController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Screen::with('tags')->latest('synced_at');

        if ($request->filled('status')) {
            $query->where('commercial_status', $request->string('status'));
        }

        return Inertia::render('Admin/Screens', [
            'screens' => $query->paginate(25)->through(fn (Screen $screen) => $this->payload($screen)),
            'filters' => $request->only(['status']),
        ]);
    }

    public function hide(Screen $screen): RedirectResponse
    {
        $old = ['local_visibility_override' => $screen->local_visibility_override];
        $screen->update(['local_visibility_override' => false]);

        AuditLogger::record('screen.hidden', $screen, $old, ['local_visibility_override' => false]);

        return back();
    }

    public function showPublicly(Screen $screen): RedirectResponse
    {
        $old = ['local_visibility_override' => $screen->local_visibility_override];
        $screen->update(['local_visibility_override' => null]);

        AuditLogger::record('screen.shown', $screen, $old, ['local_visibility_override' => null]);

        return back();
    }

    public function sync(Request $request, SyncDisplays $sync): RedirectResponse
    {
        $run = $sync->run($request->user()?->id);

        AuditLogger::record('screen.sync_manual', $run, [], ['status' => $run->status], $request);

        return back();
    }

    private function payload(Screen $screen): array
    {
        $missing = $screen->missingFields();
        $blockers = $screen->publicVisibilityBlockers();

        return [
            'id' => $screen->id,
            'internalName' => $screen->display_name,
            'publicName' => $screen->public_name ?: $screen->description,
            'municipality' => $screen->municipality,
            'province' => $screen->province,
            'locationType' => $screen->location_type,
            'locationSector' => $screen->location_sector,
            'webVisible' => $screen->web_visible_from_xibo,
            'commercialStatus' => $screen->commercial_status,
            'online' => $screen->logged_in,
            'syncedAt' => $screen->synced_at?->toDateTimeString(),
            'localVisibilityOverride' => $screen->local_visibility_override,
            'isVisiblePublicly' => $screen->newQuery()->whereKey($screen->id)->publiclyVisible()->exists(),
            'missingFields' => $missing,
            'visibilityBlockers' => $blockers,
            'warning' => $missing ? 'Corrige estos datos en Xibo y vuelve a sincronizar.' : null,
        ];
    }
}
