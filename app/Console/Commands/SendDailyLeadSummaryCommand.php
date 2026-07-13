<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;

class SendDailyLeadSummaryCommand extends Command
{
    protected $signature = 'leads:send-daily-summary';

    protected $description = 'Send a daily summary of new leads.';

    public function handle(): int
    {
        $count = Lead::whereDate('created_at', now()->subDay()->toDateString())->count();

        $this->info("Leads received yesterday: {$count}");

        return self::SUCCESS;
    }
}
