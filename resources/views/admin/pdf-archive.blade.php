@extends('admin.layout')

@section('title', 'PDF Backup Module')

@push('styles')
<style>
    .module-filter-wrap {
        border: 1px solid #e6ebf7;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        box-shadow: 0 6px 18px rgba(31, 44, 93, 0.06);
    }
    .module-filter-head {
        border-bottom: 1px solid #edf1fb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .module-title {
        font-size: 1rem;
        color: #2a3657;
        font-weight: 700;
        margin: 0;
    }
    .module-meta {
        font-size: 0.78rem;
        color: #6b7898;
        margin: 0;
    }
    .module-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .module-chip {
        border-radius: 999px;
        padding: 0.42rem 0.85rem;
        font-size: 0.84rem;
        line-height: 1.15;
        border-width: 1px;
        font-weight: 500;
    }
    .module-chip.btn-outline-primary {
        color: #2b5fbe;
        border-color: #9eb8e8;
        background: #ffffff;
    }
    .module-chip.btn-outline-primary:hover {
        color: #1f4fa9;
        border-color: #86a8e3;
        background: #f2f7ff;
    }
    .module-chip.btn-primary {
        background: linear-gradient(135deg, #2f74e7, #1f62d6);
        border-color: #2b66cf;
        box-shadow: 0 6px 14px rgba(38, 95, 201, 0.28);
    }
    .module-chip.btn-primary:hover {
        background: linear-gradient(135deg, #2a69d1, #1b57be);
    }
    @media (max-width: 768px) {
        .module-filter-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title">PDF Backup Module</h1>
            <p class="page-subtitle">Browse and download archived forms by internal module</p>
        </div>
    </div>

    <div class="dashboard-card p-3 mb-3">
        <div class="module-filter-wrap">
            <div class="module-filter-head px-3 py-3">
                <p class="module-title">Internal System Modules</p>
                <p class="module-meta">Choose one module to filter PDF backups</p>
            </div>
            <div class="p-3">
                <div class="module-chip-list">
            <a class="btn module-chip {{ $system === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.pdf.archive', array_merge(request()->query(), ['system' => 'all'])) }}">All Internal Systems</a>
            @foreach($systemModules as $module)
                <a class="btn module-chip {{ $system === $module ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.pdf.archive', array_merge(request()->query(), ['system' => $module])) }}">{{ $module }}</a>
            @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-card p-3">
        <form method="GET" class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Request #, name, email">
            </div>
            <input type="hidden" name="system" value="{{ $system }}">
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>Request #</th>
                    <th>Requester</th>
                    <th>Sub Module(s)</th>
                    <th>Status</th>
                    <th>Backup PDF</th>
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
                        <td><span class="badge text-bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($item->status) }}</span></td>
                        <td>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.pdf.download', $item) }}">Download PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No backup PDFs found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    </div>
@endsection
