<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>User Access Request Form</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: A4 portrait; margin: 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; margin: 0; padding: 0; color: #000; }
        .page { width: 100%; }

        .header-logos { width: 86%; margin: 0 auto; border-collapse: collapse; }
        .header-logos td { border: none; padding: 0; vertical-align: middle; }
        .logo-left { height: 82px; }
        .logo-right { height: 64px; }

        .title { margin: 2px 0 4px; text-align: center; font-family: "Times New Roman", serif; font-size: 18pt; font-weight: 600; color: #1d3f6e; }
        .title-rule { border-top: 2px solid #8ea1c3; margin: 0 0 6px; }

        .req-type-row { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .req-type-row td { border: none; padding: 0; text-align: right; }
        .note { font-size: 7pt; font-style: italic; text-align: right; margin-bottom: 6px; }

        .cb { border: 1px solid #000; width: 11px; height: 11px; display: inline-block; text-align: center; line-height: 10px; font-size: 9pt; font-weight: 700; margin-right: 4px; vertical-align: middle; }
        .cb-label { display: inline-block; margin-left: 10px; white-space: nowrap; font-size: 8pt; }

        .section-head { width: 100%; border-collapse: collapse; margin: 0 0 2px; }
        .section-head td { border: none; padding: 0; font-weight: 700; font-size: 9pt; }
        .section-head td.right { text-align: right; font-weight: 700; font-size: 9pt; width: 38%; }
        .req-no-box { text-align: left; }
        .req-no-label { font-family: "Times New Roman", serif; font-size: 8.5pt; font-weight: 700; white-space: nowrap; }
        .req-no-value { display: inline; margin-left: 4px; font-size: 8.5pt; font-weight: 700; white-space: nowrap; }

        .grid { width: 100%; border-collapse: collapse; border: 1px solid #000; }
        .grid td { border: 1px solid #000; padding: 6px 6px 5px; vertical-align: top; }

        .label { font-weight: 700; font-size: 8pt; }
        .hint { font-weight: 400; font-style: italic; font-size: 7pt; }
        .val { display: block; margin-top: 6px; font-size: 8pt; }
        .val-line { display: block; border-bottom: 1px solid #666; height: 12px; margin-top: 4px; }

        .box { border: 1px solid #000; padding: 6px; margin-top: 8px; }
        .box-title { font-weight: 700; font-size: 8pt; margin-bottom: 4px; }
        .box-title .hint { float: right; }
        .systems { width: 100%; border-collapse: collapse; }
        .systems td { border: none; padding: 2px 10px 2px 0; width: 33.33%; vertical-align: top; }

        .two { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-top: 8px; }
        .two td { border: 1px solid #000; padding: 6px; vertical-align: top; width: 50%; }
        .subline {
            border-bottom: 1px solid #666;
            min-height: 14px;
            line-height: 1.2;
            padding-bottom: 1px;
            margin-top: 8px;
            overflow: visible;
        }

        .four { width: 100%; border-collapse: collapse; border: 1px solid #000; margin-top: 8px; }
        .four td { border: 1px solid #000; padding: 6px; vertical-align: top; width: 50%; }
        .full { border: 1px solid #000; border-top: none; padding: 6px; }
        .full-bordered { border-top: 1px solid #000; }
        .pdf-avoid-break { page-break-inside: avoid; break-inside: avoid; }
        .mini { font-size: 7.5pt; }
        .msp-right-shift { padding-top: 5px !important; }
        .msp-line { margin-top: 3px; white-space: nowrap; }

        /* Original inline layout; avoid fixed height:10px (PDF clips and merges adjacent cells). */
        .pdf-cic-val {
            display: inline-block;
            border-bottom: 1px solid #666;
            vertical-align: bottom;
            line-height: 1.25;
            min-height: 11px;
            padding-bottom: 1px;
            word-wrap: break-word;
            overflow: visible;
        }

        .sign { margin-top: 8px; }
        .sign-head { border-bottom: 1px solid #000; padding: 4px 2px; font-weight: 700; font-size: 8pt; }
        .sign-body { border: 1px solid #000; border-top: none; }
        .sign-grid { width: 100%; border-collapse: collapse; }
        .sign-grid td { border-right: 1px solid #000; border-bottom: none; padding: 6px 8px; height: 74px; text-align: center; vertical-align: bottom; width: 33.33%; }
        .sign-grid td:last-child { border-right: none; }
        .sign-line { border-top: 1.5px solid #000; margin: 0 4px 8px; height: 0; }
        .sign-cap { font-size: 8pt; line-height: 1.1; }

        /* Single bordered content area; avoid forced blank pages between sections */
        .page-box {
            border: 1px solid #000;
            padding: 8px;
        }

        .page-header { margin-bottom: 4px; }
        .pdf-page-break { page-break-before: always; }
        .pdf-header-second { margin-top: 6mm; margin-bottom: 6px; }
    </style>
</head>
<body>
@php
    $s = is_array($summary ?? null) ? $summary : [];
    $val = fn($key) => $s[$key] ?? '';
    $coopNameVal = $val('Cooperative Name') !== '' ? $val('Cooperative Name') : $val('Coop Name & Branch');
    $branchVal = $val('Branch');
@endphp

<div class="page">
    <div class="page-header">
        <table class="header-logos">
            <tr>
                <td style="text-align:left;">
                    <img src="{{ public_path('MASS-SPECC Logo/MASS-SPECC Logo.png') }}" class="logo-left" alt="MASS-SPECC Logo">
                </td>
                <td style="text-align:right;">
                    <img src="{{ public_path('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" class="logo-right" alt="Pinoy Coop Logo">
                </td>
            </tr>
        </table>
        <div class="title">User Access Request Form</div>
        <div class="title-rule"></div>

        <table class="req-type-row">
            <tr>
                <td>
                    @foreach (['New', 'Update', 'Removal'] as $type)
                        <span class="cb-label"><span class="cb">{!! str_contains($val('Request Type'), $type) ? '&#10003;' : '' !!}</span> {{ $type }}</span>
                    @endforeach
                </td>
            </tr>
        </table>
        <div class="note">*Check if request is new user, update an existing user, or removal of existing user</div>
    </div>

    <div class="page-box">
    <table class="section-head">
        <tr>
            <td>User Information</td>
            <td class="right">
                <div class="req-no-box">
                    <span class="req-no-label">Resource Access Request Number:</span><span class="req-no-value">{{ $val('Request Number') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="grid">
        <tr>
            <td colspan="3">
                <span class="label">Full Name: (Surname, First Name and Middle Name)</span>
                <span class="hint">*of the requesting user</span>
                <span class="val">{{ $val('Full Name') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Cooperative name:</span> <span class="hint">*of the requesting coop</span>
                <span class="val">{{ $coopNameVal }}</span>
            </td>
            <td>
                <span class="label">Branch:</span> <span class="hint">*coop branch</span>
                <span class="val">{{ $branchVal !== '' ? $branchVal : '—' }}</span>
            </td>
            <td>
                <span class="label">Date:</span> <span class="hint">*date of request</span>
                <span class="val">{{ $val('Date of Request') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="label">Mobile No:</span> <span class="hint">*of requesting user</span>
                <span class="val">{{ $val('Mobile No') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="label">Address:</span> <span class="hint">*address of the coop</span>
                <span class="val">{{ $val('Address') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Postal Code:</span>
                <span class="val">{{ $val('Postal Code') }}</span>
            </td>
            <td colspan="2">
                <span class="label">Gender:</span>
                <span class="cb-label" style="margin-left:14px;"><span class="cb">{!! $val('Gender') === 'Male' ? '&#10003;' : '' !!}</span> Male</span>
                <span class="cb-label"><span class="cb">{!! $val('Gender') === 'Female' ? '&#10003;' : '' !!}</span> Female</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Place of Birth:</span> <span class="hint">*place of birth of the requesting user</span>
                <span class="val">{{ $val('Place of Birth') }}</span>
            </td>
            <td colspan="2">
                <span class="label">Email Address:</span> <span class="hint">*email address of the requesting user</span>
                <span class="val">{{ $val('Email Address') }}</span>
            </td>
        </tr>
    </table>

    @php
        $systems = $val('Systems Requested');
        $sysList = config('access_request.system_modules', []);
    @endphp

    <div class="box">
        <div class="box-title">
            Access Request For:
            <span class="hint">*select the system that the user will be using</span>
        </div>
        <table class="systems">
            <tr>
                @foreach($sysList as $i => $item)
                    <td>
                        <span class="cb">{!! str_contains($systems, $item) ? '&#10003;' : '' !!}</span>{{ $item }}
                    </td>
                    @if(($i + 1) % 3 === 0 && $i < count($sysList) - 1)
                        </tr><tr>
                    @endif
                @endforeach
            </tr>
        </table>
    </div>

    <table class="two">
        <tr>
            <td>
                <span class="label">Access Type:</span> <span class="hint">*usage of user to the system</span><br>
                <span class="cb">{!! $val('Access Type') === 'Permanent' ? '&#10003;' : '' !!}</span> Permanent
                <span style="margin-left:16px;"><span class="cb">{!! $val('Access Type') === 'Temporary' ? '&#10003;' : '' !!}</span> Temporary</span>
                @if($val('Access Type') === 'Temporary' && $val('Access End Date') && $val('Access End Date') !== '-')
                    <span style="margin-left:16px;">End Date: {{ $val('Access End Date') }}</span>
                @endif
            </td>
            <td>
                <span class="label">Job Title/Designation:</span> <span class="hint">*of the requesting user</span>
                <div class="subline">{{ $val('Job Title / Designation') }}</div>
            </td>
        </tr>
    </table>

    @php
        $mvm = $val('MVM Roles');
        $core = $val('Core 3.0 Roles');
        $atm = $val('ATM Access Level');
        $ftpRoles = $val('FTP User Roles');
        $pcdissRoles = $val('PCDISS User Roles');
    @endphp

    <table class="four">
        <tr>
            <td>
                <span class="label">For MVM Portal Access:</span><br>
                <span class="cb">{!! str_contains($mvm, 'Branch Uploader') ? '&#10003;' : '' !!}</span> Branch Uploader
                <span style="margin-left:16px;"><span class="cb">{!! str_contains($mvm, 'Data Consolidator') ? '&#10003;' : '' !!}</span> Data Consolidator</span>
            </td>
            <td>
                <span class="label">For Core 3.0 Access:</span><br>
                <span class="mini">User Roles:</span><br>
                @foreach(['New Accounts','Teller','Accounting','Collector','Loans','System Admin'] as $role)
                    <span class="cb">{!! str_contains($core, $role) ? '&#10003;' : '' !!}</span> {{ $role }}&nbsp;&nbsp;
                @endforeach
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">For ATM Portal Access:</span><br>
                <span class="mini">Access Level:</span><br>
                <span class="cb">{!! str_contains($atm, 'Maker') ? '&#10003;' : '' !!}</span> Maker (Card Issuance, Edit Information)<br>
                <span class="cb">{!! str_contains($atm, 'Approver') ? '&#10003;' : '' !!}</span> Approver (Approval)
            </td>
            <td>
                <span class="label">For MSP-ISS FTP Access:</span><br>
                <span class="mini">Are you allowed to use MASS-SPECC's FTP Access?</span><br>
                <span class="cb">{!! $val('FTP Allowed') === 'Yes' ? '&#10003;' : '' !!}</span> Yes
                <span style="margin-left:16px;"><span class="cb">{!! $val('FTP Allowed') === 'No' ? '&#10003;' : '' !!}</span> No</span>
            </td>
        </tr>
    </table>

    <div class="full pdf-avoid-break">
        <span class="label">For MSP-ISS Portal Access:</span>
        <table style="width:100%; border-collapse:collapse; margin-top:4px;">
            <tr>
                <td style="border:none; padding:2px 8px 2px 0; width:50%;">
                    <span class="mini">Coop Code (MBWIN):</span>
                    <span class="pdf-cic-val" style="width:62%;">{{ $val('MSP Coop Code (MBWIN)') }}</span>
                </td>
                <td style="border:none; padding:2px 0; width:50%;">
                    <span class="mini">Provider Code (CIC):</span>
                    <span class="pdf-cic-val" style="width:62%;">{{ $val('FTP Provider Code (CIC)') }}</span>
                </td>
            </tr>
            <tr>
                <td style="border:none; padding:2px 8px 2px 0;">
                    <span class="mini">User Name (CIC):</span>
                    <span class="pdf-cic-val" style="width:66%;">{{ $val('MSP User Name (CIC)') }}</span>
                </td>
                <td style="border:none; padding:2px 0;">
                    <span class="mini">Password (CIC):</span>
                    <span class="pdf-cic-val" style="width:66%;">{{ $val('FTP Password (CIC)') }}</span>
                </td>
            </tr>
        </table>
        <table style="width:100%; border-collapse:collapse; margin-top:3px;">
            <tr>
                <td style="border:none; width:37%; vertical-align:top; padding:2px 8px 2px 0;">
                    <div class="mini">Submission Type:</div>
                    <div class="msp-line"><span class="cb">{!! $val('MSP Submission Type') === 'Test' ? '&#10003;' : '' !!}</span> Test</div>
                    <div class="msp-line"><span class="cb">{!! $val('MSP Submission Type') === 'Production' ? '&#10003;' : '' !!}</span> Production</div>
                </td>
                <td style="border:none; width:24%; vertical-align:top; padding:2px 8px 2px 0;">
                    <div class="mini">End Date:</div>
                    <div class="pdf-cic-val" style="display:block; width:100%; margin-top:6px;">{{ $val('MSP End Date') }}</div>
                </td>
                <td style="border:none; width:39%; vertical-align:top; padding:2px 0;">
                    <div class="mini">User Role:</div>
                    <div class="msp-line">
                        <span><span class="cb">{!! str_contains($ftpRoles, 'Branch Supervisor') ? '&#10003;' : '' !!}</span> Branch Supervisor</span>
                        <span style="margin-left:6px;"><span class="cb">{!! str_contains($ftpRoles, 'Data Consolidator') ? '&#10003;' : '' !!}</span> Data Consolidator</span>
                    </div>
                    <div class="msp-line"><span class="cb">{!! str_contains($ftpRoles, 'Staff') ? '&#10003;' : '' !!}</span> Staff</div>
                </td>
            </tr>
        </table>
    </div>
    </div>

    <div class="pdf-page-break"></div>
    <div class="page-header pdf-header-second">
        <table class="header-logos">
            <tr>
                <td style="text-align:left;">
                    <img src="{{ public_path('MASS-SPECC Logo/MASS-SPECC Logo.png') }}" class="logo-left" alt="MASS-SPECC Logo">
                </td>
                <td style="text-align:right;">
                    <img src="{{ public_path('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" class="logo-right" alt="Pinoy Coop Logo">
                </td>
            </tr>
        </table>
    </div>
    <div class="page-box">
    <div class="full full-bordered pdf-avoid-break">
        <span class="label">For PCDISS Access:</span>
        <table style="width:100%; border-collapse:collapse; margin-top:4px;">
            <tr>
                <td style="border:none; padding:2px 8px 2px 0; width:50%;">
                    <span class="mini">Provider Code (CIC):</span>
                    <span class="pdf-cic-val" style="width:62%;">{{ $val('PCDISS Provider Code (CIC)') }}</span>
                </td>
                <td style="border:none; padding:2px 0; width:50%;"></td>
            </tr>
            <tr>
                <td style="border:none; padding:2px 8px 2px 0; width:50%;">
                    <span class="mini">Username (CIC):</span>
                    <span class="pdf-cic-val" style="width:62%;">{{ $val('PCDISS Username (CIC)') }}</span>
                </td>
                <td style="border:none; padding:2px 0; width:50%;">
                    <span class="mini">Password (CIC):</span>
                    <span class="pdf-cic-val" style="width:62%;">{{ $val('PCDISS Password (CIC)') }}</span>
                </td>
            </tr>
            <tr>
                <td style="border:none; width:50%; vertical-align:top; padding:2px 8px 2px 0;">
                    <div class="mini">Submission Type:</div>
                    <div class="msp-line">
                        <span><span class="cb">{!! $val('PCDISS Submission Type') === 'Test' ? '&#10003;' : '' !!}</span> Test</span>
                        <span style="margin-left:8px;"><span class="cb">{!! $val('PCDISS Submission Type') === 'Production' ? '&#10003;' : '' !!}</span> Production</span>
                    </div>
                </td>
                <td style="border:none; width:50%; vertical-align:top; padding:2px 0;">
                    <div class="mini">User Role:</div>
                    <div class="msp-line">
                        <span><span class="cb">{!! str_contains($pcdissRoles, 'Viewer') ? '&#10003;' : '' !!}</span> Viewer</span>
                        <span style="margin-left:8px;"><span class="cb">{!! str_contains($pcdissRoles, 'Uploader') ? '&#10003;' : '' !!}</span> Uploader</span>
                        <span style="margin-left:8px;"><span class="cb">{!! str_contains($pcdissRoles, 'Approver') ? '&#10003;' : '' !!}</span> Approver</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="full full-bordered" style="margin-top:6px;">
        <span class="label">For SSL VPN Access:</span>
        <div class="mini" style="margin-top:4px;">No additional access details are required for this system on this form.</div>
    </div>
    <div class="sign pdf-avoid-break">
        <div class="sign-head">Approval / Authorization by the Immediate Supervisor / Unit Head of the Requesting User: (COOP)</div>
        <div class="sign-body">
            <table class="sign-grid">
                <tr>
                    <td>
                        <div class="sign-line"></div>
                        <div class="sign-cap">Signature over Printed Name</div>
                    </td>
                    <td>
                        <div class="sign-line"></div>
                        <div class="sign-cap">Designation</div>
                    </td>
                    <td>
                        <div class="sign-line"></div>
                        <div class="sign-cap">Date Signed</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    </div>
</div>
</body>
</html>
