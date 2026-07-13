<?php

namespace App\Services\Diagnostics;

use App\Models\DiagnosticRun;
use App\Models\Screen;
use App\Services\Xibo\XiboService;
use Throwable;

class SystemDiagnostics
{
    public function __construct(private readonly XiboService $xibo)
    {
    }

    public function run(?int $triggeredByUserId = null): DiagnosticRun
    {
        $startedAt = now();
        $checks = [
            'environment' => $this->environmentCheck(),
            'xibo' => $this->xiboCheck(),
            'public_map' => $this->publicMapCheck(),
        ];

        $status = collect($checks)->contains(fn (array $check) => $check['status'] === 'failed')
            ? 'failed'
            : (collect($checks)->contains(fn (array $check) => $check['status'] === 'warning') ? 'warning' : 'success');

        return DiagnosticRun::create([
            'status' => $status,
            'checks' => $checks,
            'triggered_by_user_id' => $triggeredByUserId,
            'started_at' => $startedAt,
            'finished_at' => now(),
        ]);
    }

    private function environmentCheck(): array
    {
        return [
            'status' => 'success',
            'message' => 'Entorno Laravel cargado.',
            'details' => [
                'app_url' => config('app.url'),
                'database' => config('database.connections.mysql.database'),
                'session_driver' => config('session.driver'),
                'queue_connection' => config('queue.default'),
            ],
        ];
    }

    private function xiboCheck(): array
    {
        try {
            $about = $this->xibo->about();
            $clock = $this->xibo->clock();

            return [
                'status' => 'success',
                'message' => 'Xibo responde correctamente.',
                'details' => [
                    'version' => $about['version'] ?? null,
                    'clock' => $clock['time'] ?? null,
                ],
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'failed',
                'message' => 'No se pudo conectar con Xibo.',
                'details' => [
                    'error' => $exception->getMessage(),
                ],
            ];
        }
    }

    private function publicMapCheck(): array
    {
        $screens = Screen::with('tags')->get();
        $visible = $screens->filter(fn (Screen $screen) => Screen::whereKey($screen->id)->publiclyVisible()->exists());
        $blocked = $screens
            ->reject(fn (Screen $screen) => $visible->contains('id', $screen->id))
            ->map(fn (Screen $screen) => [
                'id' => $screen->id,
                'name' => $screen->display_name,
                'public_name' => $screen->public_name,
                'blockers' => $screen->publicVisibilityBlockers(),
                'missing_fields' => $screen->missingFields(),
            ])
            ->values();

        return [
            'status' => $visible->isNotEmpty() ? 'success' : ($screens->isNotEmpty() ? 'warning' : 'failed'),
            'message' => $visible->isNotEmpty()
                ? 'Hay pantallas visibles en el mapa publico.'
                : 'No hay pantallas visibles en el mapa publico.',
            'details' => [
                'total_screens' => $screens->count(),
                'visible_screens' => $visible->count(),
                'blocked_screens' => $blocked,
            ],
        ];
    }
}
