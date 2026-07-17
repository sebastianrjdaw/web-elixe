<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\ResponseTemplate;
use Illuminate\Support\HtmlString;

class ResponseTemplateRenderer
{
    public function subject(ResponseTemplate $template, Lead $lead): string
    {
        return str_replace(["\r", "\n"], ' ', strtr($template->subject, $this->variables($lead)));
    }

    public function html(ResponseTemplate $template, Lead $lead): HtmlString
    {
        $body = strtr($template->body, $this->variables($lead));

        return new HtmlString(nl2br(e($body)));
    }

    private function variables(Lead $lead): array
    {
        return [
            '{{contact_name}}' => $lead->contact_name,
            '{{business_name}}' => $lead->business_name ?: $lead->company_name ?: 'tu proyecto',
            '{{email}}' => $lead->email,
            '{{phone}}' => $lead->phone ?: '',
            '{{lead_type}}' => match ($lead->type) {
                'venue' => $lead->locale === 'gl' ? 'local' : 'local',
                'advertiser' => $lead->locale === 'gl' ? 'anunciante' : 'anunciante',
                default => $lead->locale === 'gl' ? 'consulta' : 'consulta',
            },
        ];
    }
}
