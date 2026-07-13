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

    public function __construct(public readonly Lead $lead)
    {
    }

    public function build(): self
    {
        $name = $this->lead->business_name ?: $this->lead->company_name;
        $kind = $this->lead->type === 'venue' ? 'local interesado' : 'anunciante interesado';

        return $this->subject('Nuevo '.$kind.' - '.$name)
            ->markdown('emails.leads.new', ['lead' => $this->lead->load('screens')]);
    }
}
