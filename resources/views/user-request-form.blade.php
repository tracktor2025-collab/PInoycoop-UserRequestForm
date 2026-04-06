<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Access Request Form</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ $formAction ?? route('user.submit') }}" method="POST" id="userRequestForm" class="paper-form">
                    @csrf
                    @if(! empty($adminEdit))
                        @method('PUT')
                    @endif
                    @php
                        $P = $editPrefill ?? [];
                        $o = function (string $key, mixed $default = null) use ($P) {
                            $fallback = array_key_exists($key, $P) ? $P[$key] : $default;

                            return old($key, $fallback);
                        };
                        $requestNumberValue = old('resource_request_number', $P['resource_request_number'] ?? $requestNumberPreview ?? session('request_number_preview') ?? '');
                    @endphp
                    @if(! empty($adminEditBanner))
                        <div class="alert alert-info mb-3">{{ $adminEditBanner }}</div>
                    @endif

                    <div class="sheet">
                        <div class="sheet-header">
                            <div class="sheet-header-logos">
                                <img src="{{ asset('MASS-SPECC Logo/MASS-SPECC Logo.png') }}" alt="MASS-SPECC Logo" class="logo left-logo">
                                <img src="{{ asset('MASS-SPECC Logo/Pinoy_Coop_Logo_21.png') }}" alt="Pinoy Coop Logo" class="logo right-logo">
                            </div>
                            <h1>User Access Request Form</h1>
                        </div>

                        <div class="request-type-line">
                            <label><input type="checkbox" name="request_type[]" value="New" id="req_new" {{ in_array('New', $o('request_type', [])) ? 'checked' : '' }}> New</label>
                            <label><input type="checkbox" name="request_type[]" value="Update" id="req_update" {{ in_array('Update', $o('request_type', [])) ? 'checked' : '' }}> Update</label>
                            <label><input type="checkbox" name="request_type[]" value="Removal" id="req_removal" {{ in_array('Removal', $o('request_type', [])) ? 'checked' : '' }}> Removal</label>
                        </div>
                        <div class="note">*Check if request is new user, update an existing user, or removal of existing user</div>

                        <input type="hidden" id="resource_request_number" name="resource_request_number" value="{{ $requestNumberValue }}">
                        <table class="pdf-section-head">
                            <tr>
                                <td>User Information</td>
                                <td class="right">Resource Access Request Number: {{ $requestNumberValue }}</td>
                            </tr>
                        </table>

                        <table class="pdf-grid">
                            <tr>
                                <td colspan="3">
                                    <label>Full Name: (Surname, First Name and Middle Name) <small>*of the requesting user</small></label>
                                    <input type="text" id="full_name" name="full_name" value="{{ $o('full_name', '') }}" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Cooperative name: <small>*of the requesting coop</small></label>
                                    <input type="text" id="coop_name" name="coop_name" value="{{ $o('coop_name', '') }}" required>
                                </td>
                                <td>
                                    <label>Branch: <small>*coop branch</small></label>
                                    <input type="text" id="branch" name="branch" value="{{ $o('branch', '') }}" required>
                                </td>
                                <td>
                                    <label>Date: <small>*date of request</small></label>
                                    <input type="date" id="request_date" name="request_date" value="{{ $o('request_date', date('Y-m-d')) }}" required>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>Mobile No: <small>*of requesting user</small></label>
                                    <input type="tel" id="mobile_no" name="mobile_no" value="{{ $o('mobile_no', '') }}" required maxlength="11" inputmode="numeric" pattern="[0-9]{11}">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <label>Address: <small>*address of the coop</small></label>
                                    <input type="text" id="address" name="address" value="{{ $o('address', '') }}" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Postal Code:</label>
                                    <input type="text" id="postal_code" name="postal_code" value="{{ $o('postal_code', '') }}" required>
                                </td>
                                <td>
                                    <label>Gender:</label>
                                    <div class="inline-checks">
                                        <label><input type="radio" name="gender" value="Male" id="gender_male" {{ $o('gender', '') === 'Male' ? 'checked' : '' }} required> Male</label>
                                        <label><input type="radio" name="gender" value="Female" id="gender_female" {{ $o('gender', '') === 'Female' ? 'checked' : '' }} required> Female</label>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>
                                    <label>Place of Birth: <small>*place of birth of the requesting user</small></label>
                                    <input type="text" id="place_of_birth" name="place_of_birth" value="{{ $o('place_of_birth', '') }}" required>
                                </td>
                                <td colspan="2">
                                    <label>Email Address: <small>*email address of the requesting user</small></label>
                                    <input type="email" id="email" name="email" value="{{ $o('email', '') }}" required>
                                </td>
                            </tr>
                        </table>

                        <div class="boxed-section">
                            <div class="sub-title">Access Request For: <small>*select the system that the user will be using</small></div>
                            <div class="systems-grid">
                                @foreach(config('access_request.system_modules', []) as $sys)
                                    <label><input type="checkbox" name="systems[]" value="{{ $sys }}" id="sys_{{ Str::slug($sys) }}" {{ in_array($sys, $o('systems', [])) ? 'checked' : '' }}> {{ $sys }}</label>
                                @endforeach
                            </div>
                        </div>

                        <div class="boxed-section two-panels">
                            <div class="panel">
                                <div class="sub-title">Access Type: <small>*usage of user to the system</small></div>
                                <div class="inline-checks">
                                    <label><input type="radio" name="access_type" value="Permanent" id="access_permanent" {{ $o('access_type', '') === 'Permanent' ? 'checked' : '' }} required> Permanent</label>
                                    <label><input type="radio" name="access_type" value="Temporary" id="access_temporary" {{ $o('access_type', '') === 'Temporary' ? 'checked' : '' }} required> Temporary</label>
                                </div>
                                <div class="access-end-date-wrap {{ $o('access_type', '') === 'Temporary' ? '' : 'uar-is-hidden' }}">
                                    <label for="access_end_date">End Date:</label>
                                    <input type="date" id="access_end_date" name="access_end_date" value="{{ $o('access_end_date', '') }}">
                                </div>
                            </div>
                            <div class="panel">
                                <div class="sub-title">Job Title/Designation <small>*of the requesting user</small></div>
                                <input type="text" id="job_title" name="job_title" value="{{ $o('job_title', '') }}" required>
                            </div>
                        </div>

                        <div class="boxed-section three-part">
                            <div class="half">
                                <div class="sub-title">For MVM Portal Access:</div>
                                <div class="system-block" data-system="MVM Portal" hidden>
                                    <div class="inline-checks">
                                        <label><input type="checkbox" name="mvm_roles[]" value="Branch Uploader" id="mvm_branch" {{ in_array('Branch Uploader', $o('mvm_roles', [])) ? 'checked' : '' }}> Branch Uploader</label>
                                        <label><input type="checkbox" name="mvm_roles[]" value="Data Consolidator" id="mvm_consolidator" {{ in_array('Data Consolidator', $o('mvm_roles', [])) ? 'checked' : '' }}> Data Consolidator</label>
                                    </div>
                                </div>
                            </div>
                            <div class="half">
                                <div class="sub-title">For Core 3.0 Access:</div>
                                <div class="system-block" data-system="CORE 3.0" hidden>
                                    <div class="mini-title">User Roles:</div>
                                    <div class="roles-grid">
                                        @foreach(['New Accounts', 'Teller', 'Accounting', 'Collector', 'Loans', 'System Admin'] as $role)
                                            <label><input type="checkbox" name="core_roles[]" value="{{ $role }}" id="core_{{ Str::slug($role) }}" {{ in_array($role, $o('core_roles', [])) ? 'checked' : '' }}> {{ $role }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="half">
                                <div class="sub-title">For ATM Portal Access:</div>
                                <div class="system-block" data-system="ATM Portal" hidden>
                                    <div class="mini-title">Access Level:</div>
                                    <label><input type="checkbox" name="atm_access[]" value="Maker (Card Issuance, Edit Information)" id="atm_maker" {{ in_array('Maker (Card Issuance, Edit Information)', $o('atm_access', [])) ? 'checked' : '' }}> Maker (Card Issuance, Edit Information)</label>
                                    <label><input type="checkbox" name="atm_access[]" value="Approver (Approval)" id="atm_approver" {{ in_array('Approver (Approval)', $o('atm_access', [])) ? 'checked' : '' }}> Approver (Approval)</label>
                                </div>
                            </div>
                            <div class="half">
                                <div class="sub-title">For MSP-ISS FTP Access:</div>
                                <div class="system-block" data-system="MSP-ISS FTP" hidden>
                                    <div class="mini-title">Are you allowed to use MASS-SPECC's FTP Access?</div>
                                    <div class="inline-checks">
                                        <label><input type="radio" name="ftp_allowed" value="Yes" id="ftp_yes" {{ $o('ftp_allowed', '') === 'Yes' ? 'checked' : '' }}> Yes</label>
                                        <label><input type="radio" name="ftp_allowed" value="No" id="ftp_no" {{ $o('ftp_allowed', '') === 'No' ? 'checked' : '' }}> No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="full">
                                <div class="sub-title">For MSP-ISS Portal Access:</div>
                                <div class="system-block" data-system="MSP-ISS Portal" hidden>
                                    <div class="portal-grid">
                                        <div>
                                            <label>Coop Code (MBWIN):</label>
                                            <input type="text" id="msp_coop_code" name="msp_coop_code" value="{{ $o('msp_coop_code', '') }}">
                                        </div>
                                        <div>
                                            <label>Provider Code (CIC):</label>
                                            <input type="text" id="ftp_provider_code" name="ftp_provider_code" value="{{ $o('ftp_provider_code', '') }}">
                                        </div>
                                        <div>
                                            <label>User Name (CIC):</label>
                                            <input type="text" id="msp_username" name="msp_username" value="{{ $o('msp_username', '') }}">
                                        </div>
                                        <div>
                                            <label>Password (CIC):</label>
                                            <input type="text" id="ftp_password_cic" name="ftp_password_cic" value="{{ $o('ftp_password_cic', '') }}" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="portal-grid second">
                                        <div>
                                            <div class="mini-title">Submission Type:</div>
                                            <div class="inline-checks">
                                                <label><input type="radio" name="msp_submission_type" value="Test" id="msp_test" {{ $o('msp_submission_type', '') === 'Test' ? 'checked' : '' }}> Test</label>
                                                <label><input type="radio" name="msp_submission_type" value="Production" id="msp_production" {{ $o('msp_submission_type', '') === 'Production' ? 'checked' : '' }}> Production</label>
                                            </div>
                                        </div>
                                        <div>
                                            <label>End Date:</label>
                                            <input type="date" id="msp_end_date" name="msp_end_date" value="{{ $o('msp_end_date', '') }}">
                                        </div>
                                        <div>
                                            <div class="mini-title">User Role:</div>
                                            <div class="inline-checks wrap">
                                                <label><input type="checkbox" name="ftp_roles[]" value="Branch Supervisor" id="ftp_role_supervisor" {{ in_array('Branch Supervisor', $o('ftp_roles', [])) ? 'checked' : '' }}> Branch Supervisor</label>
                                                <label><input type="checkbox" name="ftp_roles[]" value="Data Consolidator" id="ftp_role_consolidator" {{ in_array('Data Consolidator', $o('ftp_roles', [])) ? 'checked' : '' }}> Data Consolidator</label>
                                                <label><input type="checkbox" name="ftp_roles[]" value="Staff" id="ftp_role_staff" {{ in_array('Staff', $o('ftp_roles', [])) ? 'checked' : '' }}> Staff</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="full">
                                <div class="sub-title">For PCDISS Access:</div>
                                <div class="system-block" data-system="PCDISS" hidden>
                                    <div class="portal-grid">
                                        <div>
                                            <label>Provider Code (CIC):</label>
                                            <input type="text" id="pcdiss_provider_code" name="pcdiss_provider_code" value="{{ $o('pcdiss_provider_code', '') }}">
                                        </div>
                                        <div></div>
                                        <div>
                                            <label>Username (CIC):</label>
                                            <input type="text" id="pcdiss_username" name="pcdiss_username" value="{{ $o('pcdiss_username', '') }}">
                                        </div>
                                        <div>
                                            <label>Password (CIC):</label>
                                            <input type="text" id="pcdiss_password_cic" name="pcdiss_password_cic" value="{{ $o('pcdiss_password_cic', '') }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="portal-grid second">
                                        <div>
                                            <div class="mini-title">Submission Type:</div>
                                            <div class="inline-checks">
                                                <label><input type="radio" name="pcdiss_submission_type" value="Test" id="pcdiss_test" {{ $o('pcdiss_submission_type', '') === 'Test' ? 'checked' : '' }}> Test</label>
                                                <label><input type="radio" name="pcdiss_submission_type" value="Production" id="pcdiss_production" {{ $o('pcdiss_submission_type', '') === 'Production' ? 'checked' : '' }}> Production</label>
                                            </div>
                                        </div>
                                        <div></div>
                                        <div>
                                            <div class="mini-title">User Role:</div>
                                            <div class="inline-checks wrap">
                                                <label><input type="checkbox" name="pcdiss_roles[]" value="Viewer" id="pcdiss_role_viewer" {{ in_array('Viewer', $o('pcdiss_roles', [])) ? 'checked' : '' }}> Viewer</label>
                                                <label><input type="checkbox" name="pcdiss_roles[]" value="Uploader" id="pcdiss_role_uploader" {{ in_array('Uploader', $o('pcdiss_roles', [])) ? 'checked' : '' }}> Uploader</label>
                                                <label><input type="checkbox" name="pcdiss_roles[]" value="Approver" id="pcdiss_role_approver" {{ in_array('Approver', $o('pcdiss_roles', [])) ? 'checked' : '' }}> Approver</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="full">
                                <div class="sub-title">For SSL VPN Access:</div>
                                <div class="system-block" data-system="SSL VPN" hidden>
                                    <p class="small text-muted mb-0">No additional access details are required for this system on this form.</p>
                                </div>
                            </div>
                        </div>

                        <div class="signatory-block">
                            <div class="signatory-header">Approval / Authorization by the Immediate Supervisor / Unit Head of the Requesting User: (COOP)</div>
                            <div class="signatory-grid">
                                <div class="signatory-cell">
                                    <div class="line"></div>
                                    <div class="caption">Signature over Printed Name</div>
                                </div>
                                <div class="signatory-cell">
                                    <div class="line"></div>
                                    <div class="caption">Designation</div>
                                </div>
                                <div class="signatory-cell">
                                    <div class="line"></div>
                                    <div class="caption">Date Signed</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            @if(! empty($adminEdit))
                                <a href="{{ $adminCancelUrl ?? route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitRequestBtn">{{ ! empty($adminEditCreatesNew) ? 'Save as new request' : 'Save changes' }}</button>
                            @else
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitRequestBtn">Submit Request</button>
                            @endif
                        </div>
                    </div>
                </form>
                @if(! empty($adminEdit) && ! empty($adminEditAccessRequest))
                    <div class="mt-3 d-print-none">
                        <x-admin.delete-request-form :access-request="$adminEditAccessRequest" :redirect-to="$adminCancelUrl ?? route('admin.dashboard')" />
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var accessPermanent = document.getElementById('access_permanent');
            var accessTemporary = document.getElementById('access_temporary');
            var accessEndDateWrap = document.querySelector('.access-end-date-wrap');
            var accessEndDateInput = document.getElementById('access_end_date');

            if (accessPermanent && accessTemporary && accessEndDateWrap) {
                function toggleAccessEndDate() {
                    var shouldShow = !!accessTemporary.checked;
                    accessEndDateWrap.classList.toggle('uar-is-hidden', !shouldShow);
                    if (accessEndDateInput) {
                        accessEndDateInput.required = shouldShow;
                        if (!shouldShow) {
                            accessEndDateInput.value = '';
                        }
                    }
                }
                accessPermanent.addEventListener('change', toggleAccessEndDate);
                accessTemporary.addEventListener('change', toggleAccessEndDate);
                toggleAccessEndDate();
            }

            function selectedSystems() {
                var checked = document.querySelectorAll('input[name="systems[]"]:checked');
                var set = new Set();
                checked.forEach(function(el) { set.add(el.value); });
                return set;
            }

            function clearAndDisableInputs(container) {
                var inputs = container.querySelectorAll('input, select, textarea');
                inputs.forEach(function(input) {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                    input.disabled = true;
                });
            }

            function enableInputs(container) {
                var inputs = container.querySelectorAll('input, select, textarea');
                inputs.forEach(function(input) {
                    input.disabled = false;
                });
            }

            function toggleSystemBlocks() {
                var systems = selectedSystems();
                document.querySelectorAll('.system-block[data-system]').forEach(function(block) {
                    var sys = block.getAttribute('data-system');
                    var enabled = systems.has(sys);
                    if (!enabled && sys === 'MSP-ISS Portal' && systems.has('MSP-ISS FTP')) {
                        enabled = true;
                    }
                    if (enabled) {
                        block.classList.remove('uar-is-hidden');
                        enableInputs(block);
                    } else {
                        clearAndDisableInputs(block);
                        block.classList.add('uar-is-hidden');
                    }
                });
            }

            document.querySelectorAll('input[name="systems[]"]').forEach(function(cb) {
                cb.addEventListener('change', toggleSystemBlocks);
            });

            document.querySelectorAll('.system-block[data-system]').forEach(function(block) {
                block.removeAttribute('hidden');
            });
            toggleSystemBlocks();

            var form = document.getElementById('userRequestForm');
            var submitBtn = document.getElementById('submitRequestBtn');
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Submitting... Please wait <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>';
                });
            }
        });
    </script>
</body>
</html>
