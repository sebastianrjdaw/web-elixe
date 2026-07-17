<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResponseTemplate;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ResponseTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ResponseTemplates', [
            'templates' => ResponseTemplate::query()->orderBy('locale')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $template = ResponseTemplate::create($this->validated($request));
        AuditLogger::record('response_template.created', $template, [], $template->toArray(), $request);

        return back()->with('success', 'Plantilla creada.');
    }

    public function update(Request $request, ResponseTemplate $responseTemplate): RedirectResponse
    {
        $old = $responseTemplate->toArray();
        $data = $this->validated($request, $responseTemplate);
        $data['key'] = $responseTemplate->key;
        $responseTemplate->update($data);
        AuditLogger::record('response_template.updated', $responseTemplate, $old, $responseTemplate->fresh()->toArray(), $request);

        return back()->with('success', 'Plantilla actualizada.');
    }

    private function validated(Request $request, ?ResponseTemplate $template = null): array
    {
        return $request->validate([
            'key' => ['required', 'alpha_dash', 'max:100', Rule::unique('response_templates')->ignore($template)],
            'name' => ['required', 'string', 'max:150'],
            'lead_type' => ['nullable', Rule::in(['venue', 'advertiser', 'other'])],
            'locale' => ['required', Rule::in(['es', 'gl'])],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
