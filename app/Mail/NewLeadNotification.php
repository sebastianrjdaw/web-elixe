<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLeadNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function build(): self
    {
        $name = $this->lead->business_name ?: $this->lead->company_name ?: 'Consulta';
        $kind = match ($this->lead->type) {
            'venue' => 'local interesado',
            'advertiser' => 'anunciante interesado',
            default => 'consulta comercial',
        };

        return $this->subject('Nuevo '.$kind.' - '.$name)
            ->view('emails.leads.new', ['lead' => $this->lead->load('screens')]);
    }
}
