<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use Illuminate\Http\JsonResponse;

class PublicScreenController extends Controller
{
    public function summary(): JsonResponse
    {
        $visible = Screen::with('tags')->publiclyVisible()->get();

        return response()->json([
            'visibleScreens' => $visible->count(),
            'availableScreens' => $visible->where('commercial_status', 'disponible')->count(),
            'provinces' => $visible->pluck('province')->filter()->unique()->values(),
            'sectors' => $visible->pluck('location_sector')->filter()->unique()->values(),
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json(
            Screen::with('tags')->publiclyVisible()->get()->map->publicPayload()->values()
        );
    }

    public function filters(): JsonResponse
    {
        $screens = Screen::with('tags')->publiclyVisible()->get();

        return response()->json([
            'sectors' => $screens->pluck('location_sector')->filter()->unique()->values(),
            'locationTypes' => $screens->pluck('location_type')->filter()->unique()->values(),
            'commercialStatuses' => $screens->pluck('commercial_status')->filter()->unique()->values(),
        ]);
    }
}
