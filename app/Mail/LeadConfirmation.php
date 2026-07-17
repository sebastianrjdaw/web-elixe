<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\ResponseTemplate;
use App\Services\ResponseTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadConfirmation extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Lead $lead) {}

    public function build(): self
    {
        $template = ResponseTemplate::query()
            ->active()
            ->where('key', "automatic_{$this->lead->type}_{$this->lead->locale}")
            ->first();

        if ($template) {
            $renderer = app(ResponseTemplateRenderer::class);

            return $this->subject($renderer->subject($template, $this->lead))
                ->view('emails.leads.response', [
                    'lead' => $this->lead,
                    'title' => $renderer->subject($template, $this->lead),
                    'bodyHtml' => $renderer->html($template, $this->lead),
                ]);
        }

        return $this->subject('Hemos recibido tu solicitud')
            ->view('emails.leads.confirmation', ['lead' => $this->lead]);
    }
}
