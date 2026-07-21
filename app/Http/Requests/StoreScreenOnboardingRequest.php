<?php

namespace App\Http\Requests;

use App\Models\ScreenOnboardingRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScreenOnboardingRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user()?->is_admin; }

    public function rules(): array
    {
        $record = $this->route('screenOnboarding');
        return [
            'internal_code' => ['required', 'string', 'max:255', Rule::unique('screen_onboarding_requests')->ignore($record)],
            'establishment_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'], 'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+().\s-]+$/'],
            'address' => ['nullable', 'string', 'max:255'], 'municipality' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'], 'postal_code' => ['nullable', 'string', 'max:12'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'location_type' => ['nullable', Rule::in(ScreenOnboardingRequest::LOCATION_TYPES)],
            'location_sector' => ['nullable', Rule::in(ScreenOnboardingRequest::LOCATION_SECTORS)],
            'web_visible' => ['required', 'boolean'], 'commercial_status' => ['required', Rule::in(ScreenOnboardingRequest::COMMERCIAL_STATUSES)],
            'has_existing_screen' => ['required', 'boolean'], 'requires_elixe_screen' => ['required', 'boolean'],
            'internet_available' => ['required', 'boolean'], 'physical_location' => ['nullable', 'string', 'max:255'],
            'installation_notes' => ['nullable', 'string', 'max:5000'], 'advertising_restrictions' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
