<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleDetail extends FormRequest
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
            'application_form_id' => 'required|integer|exists:application_forms,id',
            'marks_and_number' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'detailed_description_of_article' => 'required|string|max:500',
            'gross_weight' => 'nullable|string|max:255',
        ];
    }
}
