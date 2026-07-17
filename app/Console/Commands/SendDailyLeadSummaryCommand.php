<?php

namespace App\Console\Commands;

use App\Mail\DailyLeadSummary;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyLeadSummaryCommand extends Command
{
    protected $signature = 'leads:send-daily-summary';

    protected $description = 'Send a daily summary of new leads.';

    public function handle(): int
    {
        $leads = Lead::query()
            ->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()])
            ->latest()
            ->get();

        Mail::to(Setting::getValue('leads_email', config('services.elixe.leads_email')))
            ->queue(new DailyLeadSummary($leads));

        $this->info("Daily lead summary queued: {$leads->count()} leads.");

        return self::SUCCESS;
    }
}
