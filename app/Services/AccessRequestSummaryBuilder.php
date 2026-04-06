<?php

namespace App\Services;

use Illuminate\Http\Request;

class AccessRequestSummaryBuilder
{
    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, string>
     */
    public static function fromValidated(Request $request, array $validated): array
    {
        $requestType = is_array($request->request_type) ? implode(', ', $request->request_type) : '';
        $systemsRequested = is_array($request->systems) ? implode(', ', $request->systems) : '';
        $mvmRoles = is_array($request->mvm_roles) ? implode(', ', $request->mvm_roles) : '';
        $atmAccess = is_array($request->atm_access) ? implode(', ', $request->atm_access) : '';
        $coreRoles = is_array($request->core_roles) ? implode(', ', $request->core_roles) : '';
        $ftpRoles = is_array($request->ftp_roles) ? implode(', ', $request->ftp_roles) : '';
        $pcdissRoles = is_array($request->pcdiss_roles) ? implode(', ', $request->pcdiss_roles) : '';

        return [
            'Request Number' => (string) ($validated['request_number'] ?? ''),
            'Request Type' => $requestType !== '' ? $requestType : '-',
            'Full Name' => (string) ($validated['full_name'] ?? '-'),
            'Cooperative Name' => (string) ($validated['coop_name'] ?? '-'),
            'Branch' => (string) ($validated['branch'] ?? '-'),
            'Date of Request' => (string) ($validated['request_date'] ?? '-'),
            'Mobile No' => (string) ($validated['mobile_no'] ?? '-'),
            'Address' => (string) ($validated['address'] ?? '-'),
            'Postal Code' => (string) ($validated['postal_code'] ?? '-'),
            'Email Address' => (string) ($validated['email'] ?? '-'),
            'Place of Birth' => (string) ($validated['place_of_birth'] ?? '-'),
            'Gender' => (string) ($validated['gender'] ?? '-'),
            'Systems Requested' => $systemsRequested !== '' ? $systemsRequested : '-',
            'Access Type' => (string) ($validated['access_type'] ?? '-'),
            'Access End Date' => (string) ($validated['access_end_date'] ?? '-'),
            'Job Title / Designation' => (string) ($validated['job_title'] ?? '-'),
            'MVM Roles' => $mvmRoles !== '' ? $mvmRoles : '-',
            'ATM Access Level' => $atmAccess !== '' ? $atmAccess : '-',
            'MSP Coop Code (MBWIN)' => (string) ($validated['msp_coop_code'] ?? '-'),
            'MSP User Name (CIC)' => (string) ($validated['msp_username'] ?? '-'),
            'MSP Submission Type' => (string) ($validated['msp_submission_type'] ?? '-'),
            'MSP End Date' => (string) ($validated['msp_end_date'] ?? '-'),
            'Core 3.0 Roles' => $coreRoles !== '' ? $coreRoles : '-',
            'FTP Allowed' => (string) ($validated['ftp_allowed'] ?? '-'),
            'FTP Provider Code (CIC)' => (string) ($validated['ftp_provider_code'] ?? '-'),
            'FTP Password (CIC)' => (string) ($validated['ftp_password_cic'] ?? '-'),
            'FTP User Roles' => $ftpRoles !== '' ? $ftpRoles : '-',
            'PCDISS Provider Code (CIC)' => (string) ($validated['pcdiss_provider_code'] ?? '-'),
            'PCDISS Username (CIC)' => (string) ($validated['pcdiss_username'] ?? '-'),
            'PCDISS Password (CIC)' => (string) ($validated['pcdiss_password_cic'] ?? '-'),
            'PCDISS Submission Type' => (string) ($validated['pcdiss_submission_type'] ?? '-'),
            'PCDISS User Roles' => $pcdissRoles !== '' ? $pcdissRoles : '-',
        ];
    }
}
