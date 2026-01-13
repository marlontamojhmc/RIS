<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveLocatorProfileRequest extends FormRequest
{
    public function authorize()
    {
        // Set to true if any authenticated user can make this request
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|exists:users,id',
            'locator_name' => 'required|string|max:255',
            'owner_contact_number' => 'nullable|string|max:20',
            'representative_name' => 'nullable|string|max:255',
            'representative_email' => 'nullable|email|max:255',
            'applied_date' => 'nullable|date',
            'applicant_signature' => 'nullable|string|max:255',
            'address_within_jhze' => 'nullable|string|max:500',
            'company_email' => 'nullable|email|max:255',
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'representative_contact' => 'nullable|string|max:20',
            'official_email_gmail' => 'nullable|email|max:255',
            'applicant_name' => 'nullable|string|max:255',
            'type_of_industry' => 'nullable|string|max:255',
            'company_mobile_number' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:255',
        ];
    }
}
