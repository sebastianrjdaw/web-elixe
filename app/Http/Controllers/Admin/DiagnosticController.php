<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticRun;
use App\Services\Diagnostics\SystemDiagnostics;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiagnosticController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Diagnostics', [
            'runs' => DiagnosticRun::latest('started_at')->limit(10)->get()->map(fn (DiagnosticRun $run) => $this->payload($run)),
            'latest' => ($latest = DiagnosticRun::latest('started_at')->first()) ? $this->payload($latest) : null,
        ]);
    }

    public function run(Request $request, SystemDiagnostics $diagnostics): RedirectResponse
    {
        $run = $diagnostics->run($request->user()?->id);
        AuditLogger::record('diagnostics.run', $run, [], ['status' => $run->status], $request);

        return back();
    }

    private function payload(DiagnosticRun $run): array
    {
        return [
            'id' => $run->id,
            'status' => $run->status,
            'checks' => $run->checks,
            'startedAt' => $run->started_at?->toDateTimeString(),
            'finishedAt' => $run->finished_at?->toDateTimeString(),
        ];
    }
}
