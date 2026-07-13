<?php

namespace App\Console\Commands;

use App\Services\Xibo\SyncDisplays;
use Illuminate\Console\Command;

class SyncXiboDisplaysCommand extends Command
{
    protected $signature = 'xibo:sync-displays';

    protected $description = 'Synchronize Xibo displays into the local screens table.';

    public function handle(SyncDisplays $sync): int
    {
        $run = $sync->run();

        $this->info("Xibo sync {$run->status}: {$run->records_found} found, {$run->records_created} created, {$run->records_updated} updated.");

        if ($run->status === 'failed') {
            $this->error((string) $run->error_message);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
