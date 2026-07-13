<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertiserLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'sector' => ['nullable', 'string', 'max:120'],
            'campaign_message' => ['nullable', 'string', 'max:4000'],
            'preferred_dates' => ['nullable', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'selected_screen_ids' => ['array'],
            'selected_screen_ids.*' => ['integer', 'exists:screens,id'],
            'selected_zones' => ['array'],
            'selected_zones.*' => ['string', 'max:120'],
            'message' => ['nullable', 'string', 'max:4000'],
            'privacy_accepted' => ['accepted'],
        ];
    }
}
