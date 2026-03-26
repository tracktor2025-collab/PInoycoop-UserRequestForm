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
    <div class="container py-4 uar-container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="card shadow-sm uar-shell">
                    <div class="card-body p-3 p-md-4 p-lg-5">
                        <header class="uar-header mb-4">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
                                <div>
                                    <div class="uar-badge mb-2">Internal · User Access Request</div>
                                    <h2 class="form-main-title mb-1">User Access Request</h2>
                                    <p class="text-muted small mb-0">Fill out the details below. Only the portal sections you select will be editable.</p>
                                </div>
                                <div class="uar-progress d-none d-lg-flex">
                                    <div class="uar-progress-item is-active">
                                        <div class="uar-progress-dot"></div>
                                        <div class="uar-progress-text">User details</div>
                                    </div>
                                    <div class="uar-progress-item">
                                        <div class="uar-progress-dot"></div>
                                        <div class="uar-progress-text">Systems</div>
                                    </div>
                                    <div class="uar-progress-item">
                                        <div class="uar-progress-dot"></div>
                                        <div class="uar-progress-text">Access</div>
                                    </div>
                                </div>
                            </div>
                        </header>

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

                        <div class="row g-4 form-layout">
                            <aside class="col-12 col-lg-3 d-none d-lg-block">
                                <div class="form-sidebar uar-sidebar position-sticky" style="top: 1.25rem;">
                                    <h6 class="sidebar-title mb-2">Sections</h6>
                                    <nav class="uar-nav">
                                        <a class="uar-nav-link" href="#section-request-type">Request type</a>
                                        <a class="uar-nav-link" href="#section-user-info">User &amp; coop info</a>
                                        <a class="uar-nav-link" href="#section-systems">Access request for</a>
                                        <a class="uar-nav-link" href="#section-access-type">Access type</a>
                                        <a class="uar-nav-link" href="#section-details">Portal details</a>
                                    </nav>
                                    <div class="uar-sidebar-note small mt-3">
                                        <div class="text-muted">Fields marked with <span class="text-danger">*</span> are required.</div>
                                    </div>
                                </div>
                            </aside>

                            <div class="col-12 col-lg-9">
                                <form action="{{ route('user.submit') }}" method="POST" class="user-request-form" id="userRequestForm">
                                    @csrf

                                    {{-- Request Type --}}
                                    <section class="form-section uar-section mb-4" id="section-request-type">
                                        <div class="uar-section-header mb-3">
                                            <h6 class="section-label mb-0">Request type</h6>
                                            <div class="uar-section-subtext small text-muted">Select one or more.</div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="request_type[]" value="New" id="req_new" {{ in_array('New', old('request_type', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="req_new">New</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="request_type[]" value="Update" id="req_update" {{ in_array('Update', old('request_type', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="req_update">Update</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="request_type[]" value="Removal" id="req_removal" {{ in_array('Removal', old('request_type', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="req_removal">Removal</label>
                                            </div>
                                        </div>
                                        <p class="form-text small text-muted mb-0">*Check if request is new user, update an existing user, or removal of existing user.</p>
                                    </section>

                                    {{-- User Information --}}
                                    <section class="form-section uar-section mb-4" id="section-user-info">
                                <div class="uar-section-header mb-3">
                                    <h6 class="section-label mb-0">User information</h6>
                                    <div class="uar-section-subtext small text-muted">Tell us who needs access and where they belong.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="full_name" class="form-label">Full Name: (Surname, First Name and Middle Name) <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">of the requesting user</span>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="resource_request_number" class="form-label">Resource Access Request Number:</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="resource_request_number"
                                            name="resource_request_number"
                                            value="{{ old('resource_request_number', $requestNumberPreview ?? session('request_number_preview') ?? '') }}"
                                            readonly
                                        >
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="coop_name_branch" class="form-label">Coop Name & Branch: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">of the requesting coop</span>
                                        <input type="text" class="form-control" id="coop_name_branch" name="coop_name_branch" value="{{ old('coop_name_branch') }}" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="request_date" class="form-label">Date: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">date of request</span>
                                        <input type="date" class="form-control" id="request_date" name="request_date" value="{{ old('request_date', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="mobile_no" class="form-label">Mobile No: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">of requesting user</span>
                                        <input type="tel" class="form-control" id="mobile_no" name="mobile_no" value="{{ old('mobile_no') }}" required maxlength="11" inputmode="numeric" pattern="[0-9]{11}">
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="address" class="form-label">Address: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">address of the coop</span>
                                        <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="postal_code" class="form-label">Postal Code: <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label">Gender: <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-3 pt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" value="Male" id="gender_male" {{ old('gender') === 'Male' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="gender_male">Male</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="gender" value="Female" id="gender_female" {{ old('gender') === 'Female' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="gender_female">Female</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="place_of_birth" class="form-label">Place of Birth: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">of the requesting user</span>
                                        <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}" required>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="email" class="form-label">Email Address: <span class="text-danger">*</span></label>
                                        <span class="form-text d-block small text-muted">of the requesting user</span>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                    </section>

                                    {{-- Access Request For --}}
                                    <section class="form-section uar-section mb-4" id="section-systems">
                                <div class="uar-section-header mb-3">
                                    <h6 class="section-label mb-0">Access request for <span class="text-danger">*</span></h6>
                                    <div class="uar-section-subtext small text-muted">Select the systems the user will be using.</div>
                                </div>
                                <div class="row g-2 access-request-grid">
                                    @foreach(['ATM Portal', 'SMS Portal', 'MSP-ISS Portal', 'MSP-ISS FTP', 'Helpdesk', 'PASS', 'CASH ONLINE', 'CORE 3.0', 'BIZMOTO PORTAL (Business Center)', 'PINOYCOOP PORTAL', 'MVM Portal'] as $sys)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check uar-check-tile">
                                                <input class="form-check-input" type="checkbox" name="systems[]" value="{{ $sys }}" id="sys_{{ Str::slug($sys) }}" {{ in_array($sys, old('systems', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="sys_{{ Str::slug($sys) }}">{{ $sys }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                    </section>

                                    {{-- Access Type & Job Title --}}
                                    <section class="form-section uar-section mb-4" id="section-access-type">
                                <div class="uar-section-header mb-3">
                                    <h6 class="section-label mb-0">Access type &amp; job title</h6>
                                    <div class="uar-section-subtext small text-muted">Temporary access requires an end date.</div>
                                </div>
                                <div class="row g-4 align-items-start">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Access Type: <span class="text-danger">*</span></label>
                                        <p class="form-text small text-muted mb-2">usage of user to the system</p>
                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="access_type" value="Permanent" id="access_permanent" {{ old('access_type') === 'Permanent' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="access_permanent">Permanent</label>
                                            </div>

                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="radio" name="access_type" value="Temporary" id="access_temporary" {{ old('access_type') === 'Temporary' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="access_temporary">Temporary</label>
                                            </div>

                                            <div class="access-end-date-wrap d-flex align-items-center gap-2 {{ old('access_type') === 'Temporary' ? '' : 'uar-is-hidden' }}">
                                                <label for="access_end_date" class="form-label mb-0">End Date:</label>
                                                <input type="date" class="form-control" id="access_end_date" name="access_end_date" value="{{ old('access_end_date') }}" style="max-width: 12rem;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="job_title" class="form-label">Job Title/Designation <span class="text-danger">*</span></label>
                                        <p class="form-text small text-muted mb-2">of the requesting user</p>
                                        <input type="text" class="form-control" id="job_title" name="job_title" value="{{ old('job_title') }}" required>
                                    </div>
                                </div>
                                    </section>

                                    {{-- Detailed Access Configurations --}}
                                    <section class="form-section uar-section mb-4" id="section-details">
                                <div class="uar-section-header mb-3">
                                    <h6 class="section-label mb-0">Portal details</h6>
                                    <div class="uar-section-subtext small text-muted">These sections become editable when their system is selected above.</div>
                                </div>
                                <div class="row g-4">
                                    {{-- Left column --}}
                                    <div class="col-12 col-lg-6">
                                        <div class="config-block mb-4 system-block" data-system="MVM Portal" hidden>
                                            <h6 class="config-block-title">For MVM Portal Access:</h6>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="mvm_roles[]" value="Branch Uploader" id="mvm_branch" {{ in_array('Branch Uploader', old('mvm_roles', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mvm_branch">Branch Uploader</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="mvm_roles[]" value="Data Consolidator" id="mvm_consolidator" {{ in_array('Data Consolidator', old('mvm_roles', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="mvm_consolidator">Data Consolidator</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="config-block mb-4 system-block" data-system="ATM Portal" hidden>
                                            <h6 class="config-block-title">For ATM Portal Access:</h6>
                                            <p class="small text-muted mb-2">Access Level:</p>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="atm_access[]" value="Maker (Card Issuance, Edit Information)" id="atm_maker" {{ in_array('Maker (Card Issuance, Edit Information)', old('atm_access', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="atm_maker">Maker (Card Issuance, Edit Information)</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="atm_access[]" value="Approver (Approval)" id="atm_approver" {{ in_array('Approver (Approval)', old('atm_access', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="atm_approver">Approver (Approval)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Right column --}}
                                    <div class="col-12 col-lg-6">
                                        <div class="config-block mb-4 system-block" data-system="CORE 3.0" hidden>
                                            <h6 class="config-block-title">For Core 3.0 Access:</h6>
                                            <p class="small text-muted mb-2">User Roles:</p>
                                            <div class="row g-2">
                                                @foreach(['New Accounts', 'Teller', 'Accounting', 'Collector', 'Loans', 'System Admin'] as $role)
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="core_roles[]" value="{{ $role }}" id="core_{{ Str::slug($role) }}" {{ in_array($role, old('core_roles', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="core_{{ Str::slug($role) }}">{{ $role }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="config-block mb-4 system-block" data-system="MSP-ISS FTP" hidden>
                                            <h6 class="config-block-title">For MSP-ISS FTP Access:</h6>
                                            <p class="small text-muted mb-2">Are you allowed to use MASS-SPECC's FTP Access?</p>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ftp_allowed" value="Yes" id="ftp_yes" {{ old('ftp_allowed') === 'Yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ftp_yes">Yes</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="ftp_allowed" value="No" id="ftp_no" {{ old('ftp_allowed') === 'No' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ftp_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Full-width row below FTP (matches paper form layout) --}}
                                    <div class="col-12">
                                        <div class="config-block mb-0 system-block" data-system="MSP-ISS Portal" hidden>
                                            <h6 class="config-block-title">For MSP-ISS Portal Access:</h6>
                                            <div class="row g-3">
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-2">
                                                        <label for="msp_coop_code" class="form-label">Coop Code (MBWIN):</label>
                                                        <input type="text" class="form-control" id="msp_coop_code" name="msp_coop_code" value="{{ old('msp_coop_code') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="msp_username" class="form-label">User Name (CIC):</label>
                                                        <input type="text" class="form-control" id="msp_username" name="msp_username" value="{{ old('msp_username') }}">
                                                    </div>

                                                    <div class="mt-2">
                                                        <div class="form-label mb-1">Submission Type:</div>
                                                        <div class="d-flex flex-wrap align-items-center gap-3">
                                                            <div class="form-check mb-0">
                                                                <input class="form-check-input" type="radio" name="msp_submission_type" value="Test" id="msp_test" {{ old('msp_submission_type') === 'Test' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="msp_test">Test</label>
                                                            </div>

                                                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                                                <label for="msp_end_date" class="form-label mb-0">End Date:</label>
                                                                <input type="date" class="form-control" id="msp_end_date" name="msp_end_date" value="{{ old('msp_end_date') }}">
                                                            </div>
                                                        </div>

                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="radio" name="msp_submission_type" value="Production" id="msp_production" {{ old('msp_submission_type') === 'Production' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="msp_production">Production</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-2">
                                                        <label for="ftp_provider_code" class="form-label">Provider Code (CIC):</label>
                                                        <input type="text" class="form-control" id="ftp_provider_code" name="ftp_provider_code" value="{{ old('ftp_provider_code') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label for="ftp_password_cic" class="form-label">Password (CIC):</label>
                                                        <input type="text" class="form-control" id="ftp_password_cic" name="ftp_password_cic" value="{{ old('ftp_password_cic') }}" autocomplete="off">
                                                    </div>

                                                    <div class="mt-2">
                                                        <div class="form-label mb-1">User Role:</div>
                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" name="ftp_roles[]" value="Branch Supervisor" id="ftp_role_supervisor" {{ in_array('Branch Supervisor', old('ftp_roles', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="ftp_role_supervisor">Branch Supervisor</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" name="ftp_roles[]" value="Data Consolidator" id="ftp_role_consolidator" {{ in_array('Data Consolidator', old('ftp_roles', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="ftp_role_consolidator">Data Consolidator</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="form-check mb-0">
                                                                    <input class="form-check-input" type="checkbox" name="ftp_roles[]" value="Staff" id="ftp_role_staff" {{ in_array('Staff', old('ftp_roles', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="ftp_role_staff">Staff</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    </section>

                                    <div class="form-actions mt-4">
                                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary" id="submitRequestBtn">Submit Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                    accessEndDateWrap.setAttribute('aria-hidden', String(!shouldShow));
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
                    // Provider/Password/User Role are shown in the MSP-ISS Portal block,
                    // but those details are also relevant when MSP-ISS FTP is selected.
                    if (!enabled && sys === 'MSP-ISS Portal' && systems.has('MSP-ISS FTP')) {
                        enabled = true;
                    }
                    if (enabled) {
                        block.removeAttribute('hidden');
                        block.classList.remove('uar-is-hidden');
                        block.setAttribute('aria-hidden', 'false');
                        enableInputs(block);
                    } else {
                        clearAndDisableInputs(block);
                        block.classList.add('uar-is-hidden');
                        block.setAttribute('aria-hidden', 'true');
                    }
                });
            }

            document.querySelectorAll('input[name="systems[]"]').forEach(function(cb) {
                cb.addEventListener('change', toggleSystemBlocks);
            });

            // Remove the `hidden` attribute so the CSS collapse animations can run.
            document.querySelectorAll('.system-block[data-system]').forEach(function(block) {
                block.removeAttribute('hidden');
            });
            toggleSystemBlocks();

            // Prevent duplicate submissions while backend processing is ongoing.
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
