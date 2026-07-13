<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => Setting::orderBy('key')->get(),
        ]);
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $rules = ['value' => ['nullable', 'string', 'max:4000']];

        if ($setting->type === 'email') {
            $rules['value'][] = 'email';
        }

        $data = $request->validate($rules);
        $old = ['value' => $setting->value];

        $setting->update([
            'value' => $data['value'],
            'updated_by' => $request->user()?->id,
        ]);

        AuditLogger::record($setting->key === 'leads_email' ? 'settings.leads_email_changed' : 'settings.updated', $setting, $old, ['value' => $setting->value], $request);

        return back();
    }
}
