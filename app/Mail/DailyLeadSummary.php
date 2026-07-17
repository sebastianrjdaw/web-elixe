<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyLeadSummary extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Collection $leads) {}

    public function build(): self
    {
        return $this->subject('Resumen diario de leads - '.$this->leads->count())
            ->view('emails.leads.daily-summary', ['leads' => $this->leads]);
    }
}
