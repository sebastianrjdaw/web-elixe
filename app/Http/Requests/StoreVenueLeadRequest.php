<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVenueLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'location_type' => ['nullable', 'string', 'max:100'],
            'has_tv' => ['boolean'],
            'wants_elixe_screen' => ['boolean'],
            'wants_ad_revenue' => ['boolean'],
            'wants_ad_control' => ['boolean'],
            'message' => ['nullable', 'string', 'max:4000'],
            'privacy_accepted' => ['accepted'],
        ];
    }
}
