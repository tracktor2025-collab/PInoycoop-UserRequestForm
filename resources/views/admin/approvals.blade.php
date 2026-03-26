@extends('admin.layout')

@section('title', 'Approval Module')

@section('content')
    @php
        $showBulk = in_array($status, ['pending', 'all'], true);
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title">Approval Module</h1>
            <p class="page-subtitle">Review requests and set approval status</p>
        </div>
    </div>

    <div class="dashboard-card p-4 mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Filter by Status</label>
                <select name="status" class="form-select">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Apply</button>
            </div>
        </form>
        <div class="mt-2 small text-muted">
            Showing: <span class="fw-semibold">{{ ucfirst($status) }}</span>
        </div>
    </div>

    @if($showBulk)
        <div class="dashboard-card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <div class="h6 text-uppercase text-muted mb-1" style="letter-spacing:0.05em;">Bulk approval</div>
                    <div class="small text-muted">Approve multiple pending requests at once (up to 5).</div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Selected: <span id="bulk-selected-count">0</span> / 5</div>
                    <div class="small text-muted">Only <b>Pending</b> rows can be selected.</div>
                </div>
            </div>

            <form id="bulk-approve-form" method="POST" action="{{ route('admin.approvals.bulk') }}" class="row g-3 align-items-end flex-wrap">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Shared remarks (optional)</label>
                    <textarea name="bulk_approval_remarks" class="form-control form-control-sm" rows="2" placeholder="Applied to all selected pending requests">{{ old('bulk_approval_remarks') }}</textarea>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success btn-sm w-100" id="bulk-approve-btn" disabled>Approve selected</button>
                    <div class="small text-muted mt-2 text-center">Sets status to <b>Approved</b>.</div>
                </div>
            </form>
        </div>
    @endif

    <div class="dashboard-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @if($showBulk)
                            <th style="width: 2.5rem;">
                                @if($requests->contains(fn ($r) => $r->status === 'pending'))
                                    <input type="checkbox" class="form-check-input" id="bulk-select-all" title="Select pending rows (max 5)">
                                @endif
                            </th>
                        @endif
                        <th>Request #</th>
                        <th>Requester</th>
                        <th>Systems</th>
                        <th>Status</th>
                        <th class="text-end" style="min-width: 250px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $item)
                        <tr>
                            @if($showBulk)
                                <td>
                                    @if($item->status === 'pending')
                                        <input type="checkbox"
                                            form="bulk-approve-form"
                                            name="ids[]"
                                            value="{{ $item->id }}"
                                            class="form-check-input bulk-request-cb"
                                            aria-label="Select request {{ $item->request_number ?: $item->id }}">
                                    @endif
                                </td>
                            @endif
                            <td>{{ $item->request_number ?: '-' }}</td>
                            <td>
                                <div>{{ $item->full_name }}</div>
                                <small class="text-muted">{{ $item->email }}</small>
                            </td>
                            <td>{{ implode(', ', $item->systems ?? []) ?: '-' }}</td>
                            <td>
                                <span class="badge text-bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.approvals.update', $item) }}" class="d-flex flex-column gap-2 align-items-end">
                                    @csrf
                                    <div class="d-flex gap-2 align-items-center">
                                        <select class="form-select form-select-sm" name="status">
                                            <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $item->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                    </div>
                                    <textarea class="form-control form-control-sm" rows="2" name="approval_remarks" placeholder="Remarks (optional)">{{ $item->approval_remarks }}</textarea>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $showBulk ? 6 : 5 }}">
                                <div class="text-center p-4">
                                    <div class="fw-semibold mb-1">No requests found</div>
                                    <div class="text-muted small mb-0">
                                        Try changing the status filter.
                                        @if($showBulk)
                                            Tip: select <b>Pending</b> to enable bulk approval.
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    @if($showBulk)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var btn = document.getElementById('bulk-approve-btn');
                var selectAll = document.getElementById('bulk-select-all');
                var selectedCountEl = document.getElementById('bulk-selected-count');
                var max = 5;

                function cbs() {
                    return Array.prototype.slice.call(document.querySelectorAll('.bulk-request-cb'));
                }

                function setSelectedCount(n) {
                    if (!selectedCountEl) return;
                    selectedCountEl.textContent = String(n);
                }

                function sync() {
                    var list = cbs();
                    var checked = list.filter(function (cb) { return cb.checked; });
                    var count = checked.length;

                    if (btn) btn.disabled = count === 0;

                    if (selectAll) {
                        selectAll.indeterminate = count > 0 && count < list.length;
                        selectAll.checked = count > 0 && count === list.length;
                    }

                    setSelectedCount(count);
                }

                cbs().forEach(function (cb) {
                    cb.addEventListener('change', function () {
                        var list = cbs();
                        var checked = list.filter(function (x) { return x.checked; });

                        if (checked.length > max) {
                            cb.checked = false;
                            alert('You can approve up to ' + max + ' requests at once.');
                        }

                        sync();
                    });
                });

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        var list = cbs();
                        if (selectAll.checked) {
                            list.forEach(function (cb, idx) {
                                cb.checked = idx < max;
                            });
                        } else {
                            list.forEach(function (cb) { cb.checked = false; });
                        }
                        sync();
                    });
                }

                sync();
            });
        </script>
    @endif
@endpush
