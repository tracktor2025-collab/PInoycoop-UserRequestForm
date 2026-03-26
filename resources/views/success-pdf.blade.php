<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>User Access Request Form</title>
    <style>
        * { box-sizing: border-box; }
        /* Slightly larger page margin avoids border clipping in DomPDF/printers */
        @page { size: A4 portrait; margin: 15mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 7pt; margin: 0; padding: 0; color: #000; }
        .container { width: 100%; }
        /*
         * DomPDF can clip table borders when content is exactly flush to the page box.
         * Keep the page a bit narrower than the available width to preserve inner grid lines.
         */
        .uarf-page { border: 1px solid #000; padding: 7px; background: #fff; page-break-inside: avoid; width: 170mm; margin: 0 auto; }
        .uarf-header { text-align: center; margin-bottom: 3px; }
        .uarf-title { font-size: 10pt; font-weight: bold; margin: 0 0 2px 0; }
        .uarf-request-type-row { font-size: 7pt; margin-bottom: 2px; }
        .uarf-request-type-row span { display: inline-block; margin-right: 8px; }
        .uarf-note { font-size: 6pt; color: #333; margin-top: 2px; }
        .uarf-check-label { display: inline-block; margin-right: 8px; margin-bottom: 2px; vertical-align: middle; white-space: nowrap; }
        /* Checkbox box + check size */
        .uarf-cb {
            border: 1px solid #000;
            width: 11px;
            height: 11px;
            margin-right: 3px;
            text-align: center;
            font-size: 9pt;      /* larger check mark */
            font-weight: bold;
            line-height: 10px;   /* keeps the check visually centered */
            display: inline-block;
            vertical-align: middle;
        }
        .uarf-section { margin-top: 3px; }
        .uarf-section-header { font-weight: bold; font-size: 7pt; margin-bottom: 2px; padding: 2px 0; border-bottom: 1px solid #ccc; }
        .uarf-section-header span:last-child { float: right; font-weight: normal; }
        /*
         * Avoid border-collapse in DomPDF to prevent the last column border from disappearing.
         * Also keep tables slightly under 100% width to avoid rounding overflow.
         */
        .uarf-table { width: 99.6%; border-collapse: separate; border-spacing: 0; border: 1px solid #000; font-size: 7pt; }
        .uarf-table th, .uarf-table td { border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 2px 4px; vertical-align: top; }
        .uarf-table tr:last-child th, .uarf-table tr:last-child td { border-bottom: 0; }
        .uarf-table th:last-child, .uarf-table td:last-child { border-right: 0; }
        .uarf-table th { font-weight: bold; background: #f5f5f5; }
        .uarf-systems-grid { border: 1px solid #000; padding: 4px; width: 99.6%; border-collapse: separate; border-spacing: 0; }
        .clearfix { overflow: hidden; }
        .mb-1 { margin-bottom: 2px; }
        .signature-space { margin-top: 10px; border-top: 1px solid #ccc; padding-top: 6px; }
        .sign-table { width: 100%; border-collapse: collapse; }
        .sign-table td { border: none; padding: 0 6px; vertical-align: top; font-weight: bold; font-size: 8pt; }
        .sign-line-row td { vertical-align: bottom; padding-top: 22px; }
        .sign-line { border-bottom: 1px solid #000; height: 16px; }
        .uarf-signatories-header { font-weight: bold; font-size: 7pt; margin-top: 0; margin-bottom: 4px; padding-top: 0; }
        .sub-row td { white-space: nowrap; vertical-align: middle; }
    </style>
</head>
<body>
@php
    $s = is_array($summary ?? null) ? $summary : [];
    $val = fn($key) => $s[$key] ?? '';
@endphp

<div class="container">
    <div class="uarf-page">
        <div class="uarf-header">
            <h1 class="uarf-title">User Access Request Form</h1>
            <table style="border: none; margin: 0 auto; font-size: 7pt;"><tr>
                @foreach (['New', 'Update', 'Removal'] as $type)
                    <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! str_contains($val('Request Type'), $type) ? '&#10003;' : '' !!}</span> {{ $type }}</td>
                @endforeach
            </tr></table>
            <div class="uarf-note">*Check if request is new user, update an existing user, or removal of existing user</div>
        </div>

        <div class="uarf-section">
            <table style="width: 100%; border: none; margin-bottom: 2px;">
                <tr>
                    <td style="border: none; padding: 0; font-weight: bold; font-size: 7pt;">User Information</td>
                    <td style="border: none; padding: 0; text-align: right; font-weight: normal;"></td>
                </tr>
            </table>
            <div style="border-bottom: 1px solid #ccc; margin-bottom: 2px;"></div>
            <table class="uarf-table">
                <tr>
                    <th style="width: 35%;">Full Name: (Surname, First Name and Middle Name)</th>
                    <td colspan="4">{{ $val('Full Name') }}</td>
                    <th>Request Number:</th>
                    <td>{{ $val('Request Number') }}</td>
                </tr>
                <tr>
                    <th>Coop Name &amp; Branch: (of the requesting coop)</th>
                    <td colspan="4">{{ $val('Coop Name & Branch') }}</td>
                    <th>Date:</th>
                    <td>{{ $val('Date of Request') }}</td>
                </tr>
                <tr>
                    <th>Address: (address of the coop)</th>
                    <td colspan="4">{{ $val('Address') }}</td>
                    <th>Mobile No:</th>
                    <td>{{ $val('Mobile No') }}</td>
                </tr>
                <tr>
                    <th>Place of Birth: (of the requesting user)</th>
                    <td colspan="4">{{ $val('Place of Birth') }}</td>
                    <th>Postal Code:</th>
                    <td>{{ $val('Postal Code') }}</td>
                </tr>
                <tr>
                    <th>Email Address: (email address of the requesting user)</th>
                    <td colspan="4">{{ $val('Email Address') }}</td>
                    <th>Gender:</th>
                    <td>
                        <table style="border: none; font-size: 7pt;"><tr>
                            <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! $val('Gender') === 'Male' ? '&#10003;' : '' !!}</span> Male</td>
                            <td style="border: none; padding: 0;"><span class="uarf-cb">{!! $val('Gender') === 'Female' ? '&#10003;' : '' !!}</span> Female</td>
                        </tr></table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="uarf-section">
            <table style="width: 100%; border: none; margin-bottom: 2px;"><tr>
                <td style="border: none; padding: 0; font-weight: bold; font-size: 7pt;">Access Request For:</td>
                <td style="border: none; padding: 0; text-align: right; font-weight: normal; font-style: italic; font-size: 6pt;">*select the system that the user will be using</td>
            </tr></table>
            <div style="border-bottom: 1px solid #ccc; margin-bottom: 2px;"></div>
            @php
                $systems = $val('Systems Requested');
                $sysList = ['ATM Portal','SMS Portal','MSP-ISS Portal','MSP-ISS FTP','Helpdesk','PASS','CASH ONLINE','CORE 3.0','BIZMOTO PORTAL (Business Center)','PINOYCOOP PORTAL','MVM Portal'];
            @endphp
            <table class="uarf-systems-grid" style="width: 99.6%; border-collapse: separate; border-spacing: 0; border: 1px solid #000; margin: 0 auto;"><tr>
                @foreach($sysList as $i => $item)
                    <td style="border: none; padding: 2px 8px 2px 4px; font-size: 7pt; width: 33%;"><span class="uarf-cb">{!! str_contains($systems, $item) ? '&#10003;' : '' !!}</span> {{ $item }}</td>
                    @if(($i + 1) % 3 === 0 && $i < count($sysList) - 1)</tr><tr>@endif
                @endforeach
            </tr></table>
        </div>

        <div class="uarf-section">
            <table class="uarf-table">
                <tr>
                    <th style="width: 50%;">Access Type: (usage of user to the system)</th>
                    <th>Job Title/Designation: (of the requesting user)</th>
                </tr>
                <tr>
                    <td>
                        <table style="border: none; font-size: 7pt;"><tr>
                            <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! $val('Access Type') === 'Permanent' ? '&#10003;' : '' !!}</span> Permanent</td>
                            <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! $val('Access Type') === 'Temporary' ? '&#10003;' : '' !!}</span> Temporary</td>
                            @if($val('Access Type') === 'Temporary' && $val('Access End Date') && $val('Access End Date') !== '-')
                                <td style="border: none; padding: 0 0 0 20px; white-space: nowrap;">End date: {{ $val('Access End Date') }}</td>
                            @endif
                        </tr></table>
                    </td>
                    <td style="text-align: center;">{{ $val('Job Title / Designation') }}</td>
                </tr>
            </table>
        </div>

        <div class="uarf-section">
            <table class="uarf-table">
                <tr>
                    <th style="width: 50%;">For MVM Portal Access:</th>
                    <th>For Core 3.0 Access:</th>
                </tr>
                <tr>
                    <td>
                        @php $mvm = $val('MVM Roles'); @endphp
                        <table style="border: none; font-size: 7pt;"><tr>
                            <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! str_contains($mvm, 'Branch Uploader') ? '&#10003;' : '' !!}</span> Branch Uploader</td>
                            <td style="border: none; padding: 0;"><span class="uarf-cb">{!! str_contains($mvm, 'Data Consolidator') ? '&#10003;' : '' !!}</span> Data Consolidator</td>
                        </tr></table>
                    </td>
                    <td>
                        <div class="mb-1">User Roles:</div>
                        @php $core = $val('Core 3.0 Roles'); $coreRoles = ['New Accounts','Teller','Accounting','Collector','Loans','System Admin']; @endphp
                        <table style="border: none; font-size: 7pt;"><tr>
                        @foreach($coreRoles as $i => $role)
                            <td style="border: none; padding: 0 10px 0 0;"><span class="uarf-cb">{!! str_contains($core, $role) ? '&#10003;' : '' !!}</span> {{ $role }}</td>
                            @if(($i + 1) % 3 === 0 && $i < 5)</tr><tr>@endif
                        @endforeach
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <th>For ATM Portal Access:</th>
                    <th>For MSP-ISS FTP Access:</th>
                </tr>
                <tr>
                    <td>
                        <div class="mb-1">Access Level:</div>
                        <table style="border: none; font-size: 7pt;"><tr>
                            <td style="border: none; padding: 1px 0;"><span class="uarf-cb">{!! str_contains($val('ATM Access Level'), 'Maker') ? '&#10003;' : '' !!}</span> Maker (Card Issuance, Edit Information)</td>
                        </tr><tr>
                            <td style="border: none; padding: 1px 0;"><span class="uarf-cb">{!! str_contains($val('ATM Access Level'), 'Approver') ? '&#10003;' : '' !!}</span> Approver (Approval)</td>
                        </tr></table>
                    </td>
                    <td>
                        <div class="mb-1">Are you allowed to use MASS-SPECC's FTP Access?</div>
                        <table style="border: none; font-size: 7pt;"><tr>
                            <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! $val('FTP Allowed') === 'Yes' ? '&#10003;' : '' !!}</span> Yes</td>
                            <td style="border: none; padding: 0;"><span class="uarf-cb">{!! $val('FTP Allowed') === 'No' ? '&#10003;' : '' !!}</span> No</td>
                        </tr></table>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">For MSP-ISS Portal Access:</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width: 100%; border: none; font-size: 7pt;">
                            <tr>
                                <td style="width: 28%; border: none; padding: 1px 4px 1px 0; vertical-align: top;">Coop Code (MBWIN): {{ $val('MSP Coop Code (MBWIN)') }}</td>
                                <td style="width: 28%; border: none; padding: 1px 4px; vertical-align: top;">User Name (CIC): {{ $val('MSP User Name (CIC)') }}</td>
                                <td style="width: 44%; border: none; padding: 1px 0 1px 4px; vertical-align: top;">
                                    <table style="border: none; font-size: 7pt;">
                                        <tr class="sub-row">
                                            <td style="border: none; padding: 0 8px 0 0;">Submission Type:</td>
                                            <td style="border: none; padding: 0 10px 0 0;"><span class="uarf-cb">{!! $val('MSP Submission Type') === 'Test' ? '&#10003;' : '' !!}</span> Test</td>
                                            <td style="border: none; padding: 0 10px 0 0;"><span class="uarf-cb">{!! $val('MSP Submission Type') === 'Production' ? '&#10003;' : '' !!}</span> Production</td>
                                            <td style="border: none; padding: 0;">@if($val('MSP End Date'))End Date: {{ $val('MSP End Date') }}@endif</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 1px 4px 1px 0;">Provider Code (CIC): {{ $val('FTP Provider Code (CIC)') }}</td>
                                <td style="border: none; padding: 1px 4px;">Password (CIC): {{ $val('FTP Password (CIC)') }}</td>
                                <td style="border: none; padding: 1px 0 1px 4px;">
                                    @php $ftpRoles = $val('FTP User Roles'); @endphp
                                    User Role:
                                    <table style="border: none; font-size: 7pt; margin-top: 2px;"><tr>
                                        <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! str_contains($ftpRoles, 'Branch Supervisor') ? '&#10003;' : '' !!}</span> Branch Supervisor</td>
                                        <td style="border: none; padding: 0 12px 0 0;"><span class="uarf-cb">{!! str_contains($ftpRoles, 'Data Consolidator') ? '&#10003;' : '' !!}</span> Data Consolidator</td>
                                        <td style="border: none; padding: 0;"><span class="uarf-cb">{!! str_contains($ftpRoles, 'Staff') ? '&#10003;' : '' !!}</span> Staff</td>
                                    </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="signature-space">
            <table class="sign-table">
                <tr>
                    <td style="width: 33%; padding-left: 0;">Requested by:</td>
                    <td style="width: 34%;">Noted by:</td>
                    <td style="width: 33%; padding-right: 0;">Approved by:</td>
                </tr>
            </table>
            <div style="border-top: 1px solid #d9d9d9; margin-top: 6px;"></div>
            <table class="sign-table sign-line-row">
                <tr>
                    <td style="width: 33%; padding-left: 0; padding-right: 18px;">
                        <div class="sign-line"></div>
                    </td>
                    <td style="width: 34%; padding: 0 18px;">
                        <div class="sign-line"></div>
                    </td>
                    <td style="width: 33%; padding-right: 0; padding-left: 18px;">
                        <div class="sign-line"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>
