<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'application_forms_id' => 'required|integer',
        'form_type' => 'required|string',
        'form_number' => 'required|string',
        'form_id' => 'required|integer',
        'is_number' => 'nullable|integer',
        'amount' => 'nullable|numeric',
        ];
    }
}
