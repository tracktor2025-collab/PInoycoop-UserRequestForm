@extends('admin.layout')

@section('title', 'Approval Module')

@section('content')
    @if($errors->any())
        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title">Approval Module</h1>
            <p class="page-subtitle">Review requests and set approval status. Approving requires a signed document (PDF or image).</p>
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

    <div class="dashboard-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <x-admin.sort-th column="request_number" label="Request #" :sort="$sort" :direction="$direction" />
                        <x-admin.sort-th column="full_name" label="Requester" :sort="$sort" :direction="$direction" />
                        <x-admin.sort-th column="systems" label="Systems" :sort="$sort" :direction="$direction" />
                        <x-admin.sort-th column="status" label="Status" :sort="$sort" :direction="$direction" />
                        <th scope="col" class="text-end" style="min-width: 320px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $item)
                        <tr>
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
                                <div class="d-flex flex-wrap gap-2 justify-content-end mb-2 align-items-center">
                                    <a href="{{ route('admin.request.summary', $item) }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">View form</a>
                                    <a href="{{ route('admin.request.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <x-admin.delete-request-form :access-request="$item" />
                                    @if(! empty($item->approval_signed_path))
                                        <a href="{{ route('admin.request.approval-signed', $item) }}" class="btn btn-sm btn-outline-primary">Signed file</a>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('admin.approvals.update', $item) }}" class="d-flex flex-column gap-2 align-items-end" enctype="multipart/form-data">
                                    @csrf
                                    <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
                                        <select class="form-select form-select-sm" name="status" style="width: auto; min-width: 9rem;">
                                            <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $item->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                            <option value="rejected" {{ $item->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                    </div>
                                    <div class="w-100" style="max-width: 280px;">
                                        <label class="form-label small mb-0 text-muted">Signed approval (PDF / JPG / PNG) — required when setting to Approved</label>
                                        <input type="file" name="approval_signed" class="form-control form-control-sm @error('approval_signed') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf">
                                        @error('approval_signed')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <textarea class="form-control form-control-sm" rows="2" name="approval_remarks" placeholder="Remarks (optional)">{{ $item->approval_remarks }}</textarea>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center p-4">
                                    <div class="fw-semibold mb-1">No requests found</div>
                                    <div class="text-muted small mb-0">Try changing the status filter.</div>
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
