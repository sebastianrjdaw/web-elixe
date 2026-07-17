<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\ResponseTemplate;
use App\Services\ResponseTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadResponse extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly ResponseTemplate $template,
    ) {}

    public function build(): self
    {
        $renderer = app(ResponseTemplateRenderer::class);

        return $this->subject($renderer->subject($this->template, $this->lead))
            ->view('emails.leads.response', [
                'lead' => $this->lead,
                'title' => $renderer->subject($this->template, $this->lead),
                'bodyHtml' => $renderer->html($this->template, $this->lead),
            ]);
    }
}
