<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAtoApplicationRequest extends FormRequest
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
            'application_id' => 'required|string',
            'approver_group_id' => 'required|string',
            'application_form_number' => 'required|string',
            'Enterprise' => 'required|exists:Ato_business_enterprise_classifications,id',
        'Sector' => 'required|exists:Ato_business_sector_classifications,id',
            'applicationType' => 'required|string',
            'businessStructure' => 'required|string',
            'natureOfContract' => 'required|string',
            'businessProfile.businessName' => 'required|string',
            'businessProfile.parentCompany' => 'nullable|string',
            'businessProfile.taxpayerName' => 'required|string',
            'businessProfile.TIN' => 'required|string',
            'pcic.primaryLine' => 'nullable|string',
            'pcic.secondaryLine' => 'nullable|string',
            'pcic.PCICPrimary' => 'nullable|string',
            'pcic.PCICSecondary' => 'nullable|string',
            'pcic.emailPrimary' => 'nullable|email',
            'pcic.emailSecondary' => 'nullable|email',
            'pcic.location' => 'nullable|string',
            'pcic.officeAddress' => 'nullable|string',
            'pcic.contactPerson' => 'nullable|string',
            'pcic.contactNumber' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*.file' => 'nullable|file|max:10240', // 10MB
        ];
    }
}
