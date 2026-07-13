<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadConfirmation extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Lead $lead)
    {
    }

    public function build(): self
    {
        return $this->subject('Hemos recibido tu solicitud')
            ->markdown('emails.leads.confirmation', ['lead' => $this->lead]);
    }
}
