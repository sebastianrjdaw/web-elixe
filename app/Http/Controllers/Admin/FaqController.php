<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Faqs', [
            'faqs' => Faq::orderBy('category')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $faq = Faq::create($this->validated($request) + ['updated_by' => $request->user()?->id]);
        AuditLogger::record('faq.created', $faq, [], $faq->toArray(), $request);

        return back();
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $this->validated($request);
        $old = $faq->only(array_keys($data));
        $faq->update($data + ['updated_by' => $request->user()?->id]);

        AuditLogger::record('faq.updated', $faq, $old, $faq->only(array_keys($data)), $request);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', Rule::in(['general', 'locales', 'anunciantes'])],
            'question_es' => ['required', 'string', 'max:255'],
            'question_gl' => ['nullable', 'string', 'max:255'],
            'answer_es' => ['required', 'string', 'max:8000'],
            'answer_gl' => ['nullable', 'string', 'max:8000'],
            'active' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:9999'],
        ]);
    }
}
