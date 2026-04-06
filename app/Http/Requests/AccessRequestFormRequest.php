<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccessRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'request_type' => ['nullable', 'array'],
            'request_type.*' => ['nullable', 'string', 'in:New,Update,Removal'],
            'resource_request_number' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'request_date' => ['required', 'date'],
            'mobile_no' => ['required', 'digits:11'],
            'coop_name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'systems' => ['nullable', 'array'],
            'systems.*' => ['nullable', 'string', 'max:255'],
            'access_type' => ['required', 'string', 'in:Permanent,Temporary'],
            'access_end_date' => ['nullable', 'date'],
            'job_title' => ['required', 'string', 'max:255'],
            'mvm_roles' => ['nullable', 'array'],
            'mvm_roles.*' => ['nullable', 'string', 'max:255'],
            'atm_access' => ['nullable', 'array'],
            'atm_access.*' => ['nullable', 'string', 'max:255'],
            'msp_coop_code' => ['nullable', 'string', 'max:255'],
            'msp_username' => ['nullable', 'string', 'max:255'],
            'msp_submission_type' => ['nullable', 'string', 'in:Test,Production'],
            'msp_end_date' => ['nullable', 'date'],
            'core_roles' => ['nullable', 'array'],
            'core_roles.*' => ['nullable', 'string', 'max:255'],
            'ftp_allowed' => ['nullable', 'string', 'in:Yes,No'],
            'ftp_provider_code' => ['nullable', 'string', 'max:255'],
            'ftp_password_cic' => ['nullable', 'string', 'max:255'],
            'ftp_roles' => ['nullable', 'array'],
            'ftp_roles.*' => ['nullable', 'string', 'max:255'],
            'pcdiss_provider_code' => ['nullable', 'string', 'max:255'],
            'pcdiss_username' => ['nullable', 'string', 'max:255'],
            'pcdiss_password_cic' => ['nullable', 'string', 'max:255'],
            'pcdiss_submission_type' => ['nullable', 'string', 'in:Test,Production'],
            'pcdiss_roles' => ['nullable', 'array'],
            'pcdiss_roles.*' => ['nullable', 'string', 'in:Viewer,Uploader,Approver'],
        ];
    }
}
