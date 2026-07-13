<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAdviceLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['venue', 'advertiser', 'other'])],
            'business_name' => ['nullable', 'required_if:type,venue', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:type,advertiser', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^(\\+34|0034)?[6789]\\d{8}$/'],
            'province' => ['nullable', 'string', 'max:255'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'location_type' => ['nullable', 'string', 'max:100'],
            'has_screen' => ['nullable', 'boolean'],
            'wants_elixe_screen' => ['nullable', 'boolean'],
            'wants_ad_control' => ['nullable', 'boolean'],
            'activity_sector' => ['nullable', 'string', 'max:120'],
            'interest_zone' => ['nullable', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'preferred_contact_method' => ['required', Rule::in(['llamada', 'email', 'whatsapp', 'indiferente'])],
            'preferred_call_time' => ['required', Rule::in(['manana', 'mediodia', 'tarde', 'indiferente'])],
            'selected_screen_ids' => ['array'],
            'selected_screen_ids.*' => ['integer', 'exists:screens,id'],
            'message' => ['nullable', 'string', 'max:4000'],
            'privacy_accepted' => ['accepted'],
            'cf_turnstile_response' => [config('services.turnstile.enabled') ? 'required' : 'nullable', 'string', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'privacy_accepted' => 'politica de privacidad',
            'cf_turnstile_response' => 'captcha',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! config('services.turnstile.enabled') || $validator->errors()->isNotEmpty()) {
                return;
            }

            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => $this->input('cf_turnstile_response'),
                    'remoteip' => $this->ip(),
                ]);

            if (! $response->ok() || ! ($response->json('success') === true)) {
                $validator->errors()->add('cf_turnstile_response', 'No se pudo verificar el captcha.');
            }
        });
    }
}
