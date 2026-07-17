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
            'submission_token' => ['nullable', 'uuid'],
            'type' => ['required', Rule::in(['venue', 'advertiser', 'other'])],
            'business_name' => ['nullable', 'required_if:type,venue', 'string', 'max:255'],
            'company_name' => ['nullable', 'required_if:type,advertiser', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^(\\+34|0034)?[6789]\\d{8}$/'],
            'province' => ['nullable', 'required_if:type,venue', 'string', 'max:255'],
            'municipality' => ['nullable', 'required_if:type,venue', 'string', 'max:255'],
            'location_type' => ['nullable', 'required_if:type,venue', 'string', 'max:100'],
            'has_screen' => ['nullable', 'boolean'],
            'wants_elixe_screen' => ['nullable', 'boolean'],
            'wants_ad_control' => ['nullable', 'boolean'],
            'activity_sector' => ['nullable', 'required_if:type,advertiser', 'string', 'max:120'],
            'interest_zone' => ['nullable', 'required_if:type,advertiser', 'string', 'max:255'],
            'budget_range' => ['nullable', 'required_if:type,advertiser', 'string', 'max:120'],
            'preferred_contact_method' => ['required', Rule::in(['llamada', 'email', 'whatsapp', 'indiferente'])],
            'preferred_call_time' => ['required', Rule::in(['manana', 'mediodia', 'tarde', 'indiferente'])],
            'selected_screen_ids' => ['array', 'max:50'],
            'selected_screen_ids.*' => [
                'string',
                'distinct',
                Rule::exists('screens', 'public_id')->where(fn ($query) => $query
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->where('web_visible_from_xibo', true)
                    ->where('commercial_status', 'disponible')
                    ->where(fn ($visibility) => $visibility
                        ->whereNull('local_visibility_override')
                        ->orWhere('local_visibility_override', true))),
            ],
            'message' => ['nullable', 'required_if:type,other', 'string', 'max:4000'],
            'privacy_accepted' => ['accepted'],
            'cf_turnstile_response' => [config('services.turnstile.enabled') ? 'required' : 'nullable', 'string', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'tipo de solicitud',
            'business_name' => 'nombre del local',
            'company_name' => 'nombre de empresa',
            'contact_name' => 'nombre de contacto',
            'email' => 'email',
            'phone' => 'telefono',
            'province' => 'provincia',
            'municipality' => 'municipio',
            'location_type' => 'tipo de local',
            'activity_sector' => 'sector de actividad',
            'interest_zone' => 'zona de interes',
            'budget_range' => 'presupuesto orientativo',
            'preferred_contact_method' => 'preferencia de contacto',
            'preferred_call_time' => 'horario preferido',
            'message' => 'mensaje',
            'privacy_accepted' => 'politica de privacidad',
            'cf_turnstile_response' => 'captcha',
        ];
    }

    public function messages(): array
    {
        return app()->getLocale() === 'gl'
            ? [
                'required' => 'O campo :attribute é obrigatorio.',
                'required_if' => 'O campo :attribute é obrigatorio para este tipo de solicitude.',
                'email' => 'Introduce un correo electrónico válido.',
                'phone.regex' => 'Introduce un teléfono español válido.',
                'accepted' => 'Debes aceptar a :attribute.',
                'in' => 'O valor seleccionado en :attribute non é válido.',
                'exists' => 'Unha das pantallas seleccionadas xa non está dispoñible.',
            ]
            : [
                'required' => 'El campo :attribute es obligatorio.',
                'required_if' => 'El campo :attribute es obligatorio para este tipo de solicitud.',
                'email' => 'Introduce un email valido.',
                'phone.regex' => 'Introduce un telefono espanol valido.',
                'accepted' => 'Debes aceptar la :attribute.',
                'in' => 'El valor seleccionado en :attribute no es valido.',
                'exists' => 'Una de las pantallas seleccionadas ya no esta disponible.',
            ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/[\s.-]+/', '', (string) $this->input('phone')),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! config('services.turnstile.enabled') || $validator->errors()->isNotEmpty()) {
                return;
            }

            $response = Http::asForm()
                ->retry(2, 200, throw: false)
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
