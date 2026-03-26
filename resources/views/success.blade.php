<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/success.css') }}">
</head>
<body>
<div class="container py-4">
    <div class="d-print-none mb-3">
        <div class="alert alert-success mb-2">
            Your request has been submitted successfully.
        </div>
        <form action="{{ route('success.pdf') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm me-2">Download PDF</button>
        </form>
        <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="window.print()">
            Print / Save as PDF
        </button>
        <a href="{{ url('/') }}" class="btn btn-primary btn-sm">Return to Home</a>
    </div>

    @php
        $s = is_array($summary ?? null) ? $summary : [];
        $val = fn($key) => $s[$key] ?? '';
    @endphp

    <div class="uarf-page border">
        {{-- TITLE + REQUEST TYPE --}}
        <div class="uarf-header text-center mb-2">
            <h1 class="uarf-title">User Access Request Form</h1>
            <div class="uarf-request-type-row">
                <table style="border: none; margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        @foreach (['New', 'Update', 'Removal'] as $type)
                            <td style="border: none; padding: 0 14px 0 0;">
                                <span class="uarf-check-label">
                                    <span class="uarf-box">{{ str_contains($val('Request Type'), $type) ? '✓' : '' }}</span>
                                    {{ $type }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
            <div class="uarf-note small mt-1">
                *Check if request is new user, update an existing user, or removal of existing user
            </div>
        </div>

        {{-- USER INFORMATION BOX --}}
        <div class="uarf-section">
            <div class="uarf-section-header">
                <table style="width: 100%; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; padding: 0;">User Information</td>
                        <td style="border: none; padding: 0; text-align: right; font-weight: normal;"></td>
                    </tr>
                </table>
            </div>
            <table class="uarf-table">
                <tr>
                    <th style="width: 35%;">Full Name: <span class="fw-normal">(Surname, First Name and Middle Name)</span></th>
                    <td colspan="4">{{ $val('Full Name') }}</td>
                    <th>Request Number:</th>
                    <td>{{ $val('Request Number') }}</td>
                </tr>
                <tr>
                    <th>Coop Name &amp; Branch: <span class="fw-normal">(of the requesting coop)</span></th>
                    <td colspan="4">{{ $val('Coop Name & Branch') }}</td>
                    <th>Date:</th>
                    <td>{{ $val('Date of Request') }}</td>
                </tr>
                <tr>
                    <th>Address: <span class="fw-normal">(address of the coop)</span></th>
                    <td colspan="4">{{ $val('Address') }}</td>
                    <th>Mobile No:</th>
                    <td>{{ $val('Mobile No') }}</td>
                </tr>
                <tr>
                    <th>Place of Birth: <span class="fw-normal">(of the requesting user)</span></th>
                    <td colspan="4">{{ $val('Place of Birth') }}</td>
                    <th>Postal Code:</th>
                    <td>{{ $val('Postal Code') }}</td>
                </tr>
                <tr>
                    <th>Email Address: <span class="fw-normal">(email address of the requesting user)</span></th>
                    <td colspan="4">{{ $val('Email Address') }}</td>
                    <th>Gender:</th>
                    <td>
                        <span class="uarf-check-label">
                            <span class="uarf-box">
                                {{ $val('Gender') === 'Male' ? '✓' : '' }}
                            </span> Male
                        </span>
                        <span class="uarf-check-label ms-3">
                            <span class="uarf-box">
                                {{ $val('Gender') === 'Female' ? '✓' : '' }}
                            </span> Female
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ACCESS REQUEST FOR --}}
        <div class="uarf-section">
            <div class="uarf-section-header">
                <span>Access Request For:</span>
                <span class="small fst-italic">*select the system that the user will be using</span>
            </div>
            @php
                $systems = $val('Systems Requested');
                $sysList = [
                    'ATM Portal','SMS Portal','MSP-ISS Portal','MSP-ISS FTP',
                    'Helpdesk','PASS','CASH ONLINE','CORE 3.0',
                    'BIZMOTO PORTAL (Business Center)','PINOYCOOP PORTAL','MVM Portal'
                ];
            @endphp
            <div class="uarf-systems-grid">
                @foreach($sysList as $item)
                    <span class="uarf-check-label">
                        <span class="uarf-box">
                            {{ str_contains($systems, $item) ? '✓' : '' }}
                        </span> {{ $item }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ACCESS TYPE + JOB TITLE --}}
        <div class="uarf-section">
            <table class="uarf-table">
                <tr>
                    <th style="width: 50%;">Access Type: <span class="fw-normal">(usage of user to the system)</span></th>
                    <th>Job Title/Designation: <span class="fw-normal">(of the requesting user)</span></th>
                </tr>
                <tr>
                    <td>
                        <span class="uarf-check-label">
                            <span class="uarf-box">
                                {{ $val('Access Type') === 'Permanent' ? '✓' : '' }}
                            </span> Permanent
                        </span>
                        <span class="uarf-check-label ms-3">
                            <span class="uarf-box">
                                {{ $val('Access Type') === 'Temporary' ? '✓' : '' }}
                            </span> Temporary
                        </span>
                        @if($val('Access Type') === 'Temporary' && $val('Access End Date') && $val('Access End Date') !== '-')
                            <span class="ms-4">End date: {{ $val('Access End Date') }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $val('Job Title / Designation') }}</td>
                </tr>
            </table>
        </div>

        {{-- LOWER BOXES: MVM / ATM / CORE / MSP / FTP --}}
        <div class="uarf-section">
            <table class="uarf-table">
                <tr>
                    <th style="width: 50%;">For MVM Portal Access:</th>
                    <th>For Core 3.0 Access:</th>
                </tr>
                <tr>
                    <td>
                        @php $mvm = $val('MVM Roles'); @endphp
                        <span class="uarf-check-label">
                            <span class="uarf-box">
                                {{ str_contains($mvm, 'Branch Uploader') ? '✓' : '' }}
                            </span> Branch Uploader
                        </span>
                        <span class="uarf-check-label ms-3">
                            <span class="uarf-box">
                                {{ str_contains($mvm, 'Data Consolidator') ? '✓' : '' }}
                            </span> Data Consolidator
                        </span>
                    </td>
                    <td>
                        <div class="mb-1">User Roles:</div>
                        @php $core = $val('Core 3.0 Roles'); @endphp
                        @foreach(['New Accounts','Teller','Accounting','Collector','Loans','System Admin'] as $role)
                            <span class="uarf-check-label me-3">
                                <span class="uarf-box">
                                    {{ str_contains($core, $role) ? '✓' : '' }}
                                </span> {{ $role }}
                            </span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>For ATM Portal Access:</th>
                    <th>For MSP-ISS FTP Access:</th>
                </tr>
                <tr>
                    <td>
                        <div class="mb-1">Access Level:</div>
                        @php $atm = $val('ATM Access Level'); @endphp
                        <span class="uarf-check-label d-block">
                            <span class="uarf-box">
                                {{ str_contains($atm, 'Maker') ? '✓' : '' }}
                            </span> Maker (Card Issuance, Edit Information)
                        </span>
                        <span class="uarf-check-label d-block mt-1">
                            <span class="uarf-box">
                                {{ str_contains($atm, 'Approver') ? '✓' : '' }}
                            </span> Approver (Approval)
                        </span>
                    </td>
                    <td>
                        <div class="mb-1">Are you allowed to use MASS-SPECC's FTP Access?</div>
                        <span class="uarf-check-label">
                            <span class="uarf-box">
                                {{ $val('FTP Allowed') === 'Yes' ? '✓' : '' }}
                            </span> Yes
                        </span>
                        <span class="uarf-check-label ms-3">
                            <span class="uarf-box">
                                {{ $val('FTP Allowed') === 'No' ? '✓' : '' }}
                            </span> No
                        </span>
                    </td>
                </tr>
                <tr>
                    <th colspan="2">For MSP-ISS Portal Access:</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <table style="width: 100%; border: none; border-collapse: collapse;">
                            <tr>
                                <td style="border: none; padding: 0; width: 33.33%;">
                                    Coop Code (MBWIN): {{ $val('MSP Coop Code (MBWIN)') }}
                                </td>
                                <td style="border: none; padding: 0; width: 33.33%;">
                                    User Name (CIC): {{ $val('MSP User Name (CIC)') }}
                                </td>
                                <td style="border: none; padding: 0; width: 33.34%;">
                                    Submission Type:
                                    <span class="uarf-check-label ms-2">
                                        <span class="uarf-box">
                                            {{ $val('MSP Submission Type') === 'Test' ? '✓' : '' }}
                                        </span> Test
                                    </span>
                                    <span class="uarf-check-label ms-2">
                                        <span class="uarf-box">
                                            {{ $val('MSP Submission Type') === 'Production' ? '✓' : '' }}
                                        </span> Production
                                    </span>
                                    @if($val('MSP End Date'))
                                        <span class="ms-2">End Date: {{ $val('MSP End Date') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 0; width: 33.33%;">
                                    Provider Code (CIC): {{ $val('FTP Provider Code (CIC)') }}
                                </td>
                                <td style="border: none; padding: 0; width: 33.33%;">
                                    Password (CIC): {{ $val('FTP Password (CIC)') }}
                                </td>
                                <td style="border: none; padding: 0; width: 33.34%;">
                                    @php $ftpRoles = $val('FTP User Roles'); @endphp
                                    User Role:
                                    <span class="uarf-check-label d-block">
                                        <span class="uarf-box">
                                            {{ str_contains($ftpRoles, 'Branch Supervisor') ? '✓' : '' }}
                                        </span> Branch Supervisor
                                    </span>
                                    <span class="uarf-check-label d-block">
                                        <span class="uarf-box">
                                            {{ str_contains($ftpRoles, 'Data Consolidator') ? '✓' : '' }}
                                        </span> Data Consolidator
                                    </span>
                                    <span class="uarf-check-label d-block">
                                        <span class="uarf-box">
                                            {{ str_contains($ftpRoles, 'Staff') ? '✓' : '' }}
                                        </span> Staff
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="mt-3 pt-2 border-top">
            <table class="w-100">
                <tr>
                    <td style="width: 33%; padding-left: 0;" class="fw-bold">Requested by:</td>
                    <td style="width: 34%;" class="fw-bold">Noted by:</td>
                    <td style="width: 33%; padding-right: 0;" class="fw-bold">Approved by:</td>
                </tr>
            </table>
            <div style="border-top: 1px solid #d9d9d9; margin-top: 6px;"></div>
            <table class="w-100">
                <tr>
                    <td style="width: 33%; vertical-align: bottom; padding: 22px 18px 0 0;">
                        <div style="border-bottom: 1px solid #000; min-height: 16px;"></div>
                    </td>
                    <td style="width: 34%; vertical-align: bottom; padding: 22px 18px 0 18px;">
                        <div style="border-bottom: 1px solid #000; min-height: 16px;"></div>
                    </td>
                    <td style="width: 33%; vertical-align: bottom; padding: 22px 0 0 18px;">
                        <div style="border-bottom: 1px solid #000; min-height: 16px;"></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
