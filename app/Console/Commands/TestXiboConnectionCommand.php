<?php

namespace App\Console\Commands;

use App\Services\Xibo\XiboService;
use Illuminate\Console\Command;
use Throwable;

class TestXiboConnectionCommand extends Command
{
    protected $signature = 'xibo:test-connection';

    protected $description = 'Check Xibo OAuth and basic health endpoints.';

    public function handle(XiboService $xibo): int
    {
        try {
            $about = $xibo->about();
            $clock = $xibo->clock();
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Xibo connection OK.');
        $this->line('About: '.json_encode($about, JSON_UNESCAPED_SLASHES));
        $this->line('Clock: '.json_encode($clock, JSON_UNESCAPED_SLASHES));

        return self::SUCCESS;
    }
}
