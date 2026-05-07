<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Request Form</title>
    <link rel="icon" type="image/png" href="{{ asset('MASS-SPECC Logo/MASS-SPECC LOGO 2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('MASS-SPECC Logo/MASS-SPECC LOGO 2.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/success.css') }}">
</head>
<body>
<div class="container py-4">
    @if(empty($adminPreview ?? false))
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
    @else
    <div class="d-print-none mb-3 d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ $adminBackUrl ?? route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        @if(! empty($accessRequestId))
            <a href="{{ route('admin.request.edit', $accessRequestId) }}" class="btn btn-primary btn-sm">Edit form</a>
        @endif
        @if(! empty($adminDeleteAccessRequest))
            <x-admin.delete-request-form :access-request="$adminDeleteAccessRequest" :redirect-to="$adminBackUrl ?? route('admin.dashboard')" />
        @endif
    </div>
    @endif

    @php
        $s = is_array($summary ?? null) ? $summary : [];
        $val = fn($key) => $s[$key] ?? '';
        $coopNameVal = $val('Cooperative Name') !== '' ? $val('Cooperative Name') : $val('Coop Name & Branch');
        $branchVal = $val('Branch');
    @endphp

    <div class="uarf-page uarf-paper">
        @php
            $systems = $val('Systems Requested');
            $sysList = config('access_request.system_modules', []);
            $mvm = $val('MVM Roles');
            $core = $val('Core 3.0 Roles');
            $atm = $val('ATM Access Level');
            $ftpRoles = $val('FTP User Roles');
            $pcdissRoles = $val('PCDISS User Roles');
        @endphp

        <div class="uarf-paper-header">
            <div class="uarf-paper-logos">
                <img src="{{ asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}" class="uarf-paper-logo-left" alt="MASS-SPECC Logo">
                <img src="{{ asset('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" class="uarf-paper-logo-right" alt="Pinoy Coop Logo">
            </div>
            <div class="uarf-paper-title">User Access Request Form</div>
            <div class="uarf-paper-rule"></div>
            <div class="uarf-paper-reqtype">
                @foreach (['New', 'Update', 'Removal'] as $type)
                    <span class="uarf-paper-cb-label"><span class="uarf-paper-cb">{{ str_contains($val('Request Type'), $type) ? '✓' : '' }}</span> {{ $type }}</span>
                @endforeach
            </div>
            <div class="uarf-paper-note">*Check if request is new user, update an existing user, or removal of existing user</div>
        </div>

        <div class="uarf-paper-body">
        <div class="uarf-paper-section-head">
            <div>User Information</div>
            <div class="uarf-paper-reqno">
                <span class="uarf-paper-reqno-label">Resource Access Request Number:</span>
                <span class="uarf-paper-reqno-value">{{ $val('Request Number') }}</span>
            </div>
        </div>

        <table class="uarf-paper-grid">
            <tr>
                <td colspan="3">
                    <div class="uarf-paper-label">Full Name: (Surname, First Name and Middle Name) <span class="uarf-paper-hint">*of the requesting user</span></div>
                    <div class="uarf-paper-val">{{ $val('Full Name') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="uarf-paper-label">Cooperative name: <span class="uarf-paper-hint">*of the requesting coop</span></div>
                    <div class="uarf-paper-val">{{ $coopNameVal }}</div>
                </td>
                <td>
                    <div class="uarf-paper-label">Branch: <span class="uarf-paper-hint">*coop branch</span></div>
                    <div class="uarf-paper-val">{{ $branchVal !== '' ? $branchVal : '—' }}</div>
                </td>
                <td>
                    <div class="uarf-paper-label">Date: <span class="uarf-paper-hint">*date of request</span></div>
                    <div class="uarf-paper-val">{{ $val('Date of Request') }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="uarf-paper-label">Mobile No: <span class="uarf-paper-hint">*of requesting user</span></div>
                    <div class="uarf-paper-val">{{ $val('Mobile No') }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="uarf-paper-label">Address: <span class="uarf-paper-hint">*address of the coop</span></div>
                    <div class="uarf-paper-val">{{ $val('Address') }}</div>
                </td>
                <td>
                    <div class="uarf-paper-label">Postal Code:</div>
                    <div class="uarf-paper-val">{{ $val('Postal Code') }}</div>
                </td>
                <td>
                    <div class="uarf-paper-label">Gender:</div>
                    <span class="uarf-paper-cb-label" style="margin-left:12px;"><span class="uarf-paper-cb">{{ $val('Gender') === 'Male' ? '✓' : '' }}</span> Male</span>
                    <span class="uarf-paper-cb-label"><span class="uarf-paper-cb">{{ $val('Gender') === 'Female' ? '✓' : '' }}</span> Female</span>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="uarf-paper-label">Place of Birth: <span class="uarf-paper-hint">*place of birth of the requesting user</span></div>
                    <div class="uarf-paper-val">{{ $val('Place of Birth') }}</div>
                </td>
                <td colspan="2">
                    <div class="uarf-paper-label">Email Address: <span class="uarf-paper-hint">*email address of the requesting user</span></div>
                    <div class="uarf-paper-val">{{ $val('Email Address') }}</div>
                </td>
            </tr>
        </table>

        <div class="uarf-paper-box">
            <div class="uarf-paper-box-title">
                <span>Access Request For:</span>
                <span class="uarf-paper-hint">*select the system that the user will be using</span>
            </div>
            <table class="uarf-paper-systems">
                <tr>
                    @foreach($sysList as $i => $item)
                        <td><span class="uarf-paper-cb">{{ str_contains($systems, $item) ? '✓' : '' }}</span>{{ $item }}</td>
                        @if(($i + 1) % 3 === 0 && $i < count($sysList) - 1)
                            </tr><tr>
                        @endif
                    @endforeach
                </tr>
            </table>
        </div>

        <table class="uarf-paper-two">
            <tr>
                <td>
                    <div class="uarf-paper-label">Access Type: <span class="uarf-paper-hint">*usage of user to the system</span></div>
                    <span class="uarf-paper-cb">{{ $val('Access Type') === 'Permanent' ? '✓' : '' }}</span> Permanent
                    <span style="margin-left:14px;"><span class="uarf-paper-cb">{{ $val('Access Type') === 'Temporary' ? '✓' : '' }}</span> Temporary</span>
                    @if($val('Access Type') === 'Temporary' && $val('Access End Date') && $val('Access End Date') !== '-')
                        <span style="margin-left:14px;">End Date: {{ $val('Access End Date') }}</span>
                    @endif
                </td>
                <td>
                    <div class="uarf-paper-label">Job Title/Designation: <span class="uarf-paper-hint">*of the requesting user</span></div>
                    <div class="uarf-paper-line" style="margin-top:10px;">{{ $val('Job Title / Designation') }}</div>
                </td>
            </tr>
        </table>

        <table class="uarf-paper-four">
            <tr>
                <td>
                    <div class="uarf-paper-label">For MVM Portal Access:</div>
                    <span class="uarf-paper-cb">{{ str_contains($mvm, 'Branch Uploader') ? '✓' : '' }}</span> Branch Uploader
                    <span style="margin-left:14px;"><span class="uarf-paper-cb">{{ str_contains($mvm, 'Data Consolidator') ? '✓' : '' }}</span> Data Consolidator</span>
                </td>
                <td>
                    <div class="uarf-paper-label">For Core 3.0 Access:</div>
                    <div class="uarf-paper-mini">User Roles:</div>
                    @foreach(['New Accounts','Teller','Accounting','Collector','Loans','System Admin'] as $role)
                        <span class="uarf-paper-mini"><span class="uarf-paper-cb">{{ str_contains($core, $role) ? '✓' : '' }}</span> {{ $role }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>
                    <div class="uarf-paper-label">For ATM Portal Access:</div>
                    <div class="uarf-paper-mini">Access Level:</div>
                    <div class="uarf-paper-mini"><span class="uarf-paper-cb">{{ str_contains($atm, 'Maker') ? '✓' : '' }}</span> Maker (Card Issuance, Edit Information)</div>
                    <div class="uarf-paper-mini"><span class="uarf-paper-cb">{{ str_contains($atm, 'Approver') ? '✓' : '' }}</span> Approver (Approval)</div>
                </td>
                <td>
                    <div class="uarf-paper-label">For MSP-ISS FTP Access:</div>
                    <div class="uarf-paper-mini">Are you allowed to use MASS-SPECC's FTP Access?</div>
                    <span class="uarf-paper-mini"><span class="uarf-paper-cb">{{ $val('FTP Allowed') === 'Yes' ? '✓' : '' }}</span> Yes</span>
                    <span class="uarf-paper-mini" style="margin-left:14px;"><span class="uarf-paper-cb">{{ $val('FTP Allowed') === 'No' ? '✓' : '' }}</span> No</span>
                </td>
            </tr>
        </table>

        <div class="uarf-paper-full">
            <div class="uarf-paper-label">For MSP-ISS Portal Access:</div>
            <table class="uarf-paper-msp">
                <tr>
                    <td colspan="2"><span class="uarf-paper-mini">Coop Code (MBWIN):</span> <span class="uarf-paper-inline-line">{{ $val('MSP Coop Code (MBWIN)') }}</span></td>
                    <td colspan="2"><span class="uarf-paper-mini">Provider Code (CIC):</span> <span class="uarf-paper-inline-line">{{ $val('FTP Provider Code (CIC)') }}</span></td>
                </tr>
                <tr>
                    <td colspan="2"><span class="uarf-paper-mini">User Name (CIC):</span> <span class="uarf-paper-inline-line">{{ $val('MSP User Name (CIC)') }}</span></td>
                    <td colspan="2"><span class="uarf-paper-mini">Password (CIC):</span> <span class="uarf-paper-inline-line">{{ $val('FTP Password (CIC)') }}</span></td>
                </tr>
                <tr>
                    <td class="uarf-paper-msp-right-shift" style="width:28%;">
                        <div class="uarf-paper-mini">Submission Type:</div>
                        <div class="uarf-paper-mini uarf-paper-msp-line"><span class="uarf-paper-cb">{{ $val('MSP Submission Type') === 'Test' ? '✓' : '' }}</span> Test</div>
                        <div class="uarf-paper-mini uarf-paper-msp-line"><span class="uarf-paper-cb">{{ $val('MSP Submission Type') === 'Production' ? '✓' : '' }}</span> Production</div>
                    </td>
                    <td class="uarf-paper-msp-right-shift" style="width:22%;">
                        <div class="uarf-paper-mini">End Date:</div>
                        <div class="uarf-paper-inline-line" style="margin-top:0.35rem;">{{ $val('MSP End Date') }}</div>
                    </td>
                    <td class="uarf-paper-msp-right-shift" colspan="2" style="width:50%;">
                        <div class="uarf-paper-mini">User Role:</div>
                        <div class="uarf-paper-mini uarf-paper-msp-line">
                            <span><span class="uarf-paper-cb">{{ str_contains($ftpRoles, 'Branch Supervisor') ? '✓' : '' }}</span> Branch Supervisor</span>
                            <span style="margin-left:10px;"><span class="uarf-paper-cb">{{ str_contains($ftpRoles, 'Data Consolidator') ? '✓' : '' }}</span> Data Consolidator</span>
                        </div>
                        <div class="uarf-paper-mini uarf-paper-msp-line">
                            <span class="uarf-paper-cb">{{ str_contains($ftpRoles, 'Staff') ? '✓' : '' }}</span> Staff
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        </div>

        <div class="uarf-paper-page-break">
            <div class="uarf-paper-logos uarf-paper-logos-second-page">
                <img src="{{ asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}" class="uarf-paper-logo-left" alt="MASS-SPECC Logo">
                <img src="{{ asset('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" class="uarf-paper-logo-right" alt="Pinoy Coop Logo">
            </div>
            <div class="uarf-paper-full uarf-paper-full-bordered">
                <div class="uarf-paper-label">For PCDISS Access:</div>
                <table class="uarf-paper-msp">
                    <tr>
                        <td colspan="2"><span class="uarf-paper-mini">Provider Code (CIC):</span> <span class="uarf-paper-inline-line uarf-paper-inline-line--cic">{{ $val('PCDISS Provider Code (CIC)') }}</span></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2"><span class="uarf-paper-mini">Username (CIC):</span> <span class="uarf-paper-inline-line uarf-paper-inline-line--cic">{{ $val('PCDISS Username (CIC)') }}</span></td>
                        <td colspan="2"><span class="uarf-paper-mini">Password (CIC):</span> <span class="uarf-paper-inline-line uarf-paper-inline-line--cic">{{ $val('PCDISS Password (CIC)') }}</span></td>
                    </tr>
                    <tr>
                        <td class="uarf-paper-msp-right-shift" colspan="2">
                            <div class="uarf-paper-mini">Submission Type:</div>
                            <div class="uarf-paper-mini uarf-paper-msp-line">
                                <span><span class="uarf-paper-cb">{{ $val('PCDISS Submission Type') === 'Test' ? '✓' : '' }}</span> Test</span>
                                <span style="margin-left:10px;"><span class="uarf-paper-cb">{{ $val('PCDISS Submission Type') === 'Production' ? '✓' : '' }}</span> Production</span>
                            </div>
                        </td>
                        <td class="uarf-paper-msp-right-shift" colspan="2">
                            <div class="uarf-paper-mini">User Role:</div>
                            <div class="uarf-paper-mini uarf-paper-msp-line">
                                <span><span class="uarf-paper-cb">{{ str_contains($pcdissRoles, 'Viewer') ? '✓' : '' }}</span> Viewer</span>
                                <span style="margin-left:10px;"><span class="uarf-paper-cb">{{ str_contains($pcdissRoles, 'Uploader') ? '✓' : '' }}</span> Uploader</span>
                                <span style="margin-left:10px;"><span class="uarf-paper-cb">{{ str_contains($pcdissRoles, 'Approver') ? '✓' : '' }}</span> Approver</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="uarf-paper-box" style="margin-top:10px;">
                <div class="uarf-paper-label">For SSL VPN Access:</div>
                <div class="uarf-paper-val text-muted small">No additional access details are required for this system on this form.</div>
            </div>
            <div class="uarf-paper-sign">
                <div class="uarf-paper-sign-head">Approval / Authorization by the Immediate Supervisor / Unit Head of the Requesting User: (COOP)</div>
                <div class="uarf-paper-sign-body">
                    <table class="uarf-paper-sign-grid">
                        <tr>
                            <td>
                                <div class="uarf-paper-sign-line"></div>
                                <div class="uarf-paper-sign-cap">Signature over Printed Name</div>
                            </td>
                            <td>
                                <div class="uarf-paper-sign-line"></div>
                                <div class="uarf-paper-sign-cap">Designation</div>
                            </td>
                            <td>
                                <div class="uarf-paper-sign-line"></div>
                                <div class="uarf-paper-sign-cap">Date Signed</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
