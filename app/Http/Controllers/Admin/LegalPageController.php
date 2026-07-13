<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LegalPageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/LegalPages', [
            'pages' => LegalPage::orderBy('slug')->get(),
        ]);
    }

    public function update(Request $request, LegalPage $legalPage): RedirectResponse
    {
        $data = $request->validate([
            'title_es' => ['required', 'string', 'max:255'],
            'title_gl' => ['nullable', 'string', 'max:255'],
            'content_es' => ['required', 'string', 'max:20000'],
            'content_gl' => ['nullable', 'string', 'max:20000'],
            'active' => ['boolean'],
        ]);

        $old = $legalPage->only(array_keys($data));
        $legalPage->update($data + ['updated_by' => $request->user()?->id]);

        AuditLogger::record('legal.updated', $legalPage, $old, $legalPage->only(array_keys($data)), $request);

        return back();
    }
}
