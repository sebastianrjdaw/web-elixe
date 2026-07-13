<?php

namespace App\Console\Commands;

use App\Services\Diagnostics\SystemDiagnostics;
use Illuminate\Console\Command;

class RunDiagnosticsCommand extends Command
{
    protected $signature = 'elixe:diagnose {--json : Output JSON details}';

    protected $description = 'Run Elixe diagnostics for Xibo, public map visibility and local environment.';

    public function handle(SystemDiagnostics $diagnostics): int
    {
        $run = $diagnostics->run();

        if ($this->option('json')) {
            $this->line(json_encode($run->checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->info("Diagnostics {$run->status}");

            foreach ($run->checks as $name => $check) {
                $this->line("[{$check['status']}] {$name}: {$check['message']}");
            }
        }

        return $run->status === 'failed' ? self::FAILURE : self::SUCCESS;
    }
}
