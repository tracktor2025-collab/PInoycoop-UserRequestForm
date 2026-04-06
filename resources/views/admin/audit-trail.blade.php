@extends('admin.layout')

@section('title', 'Audit Trail')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="page-title">Audit Trail</h1>
            <p class="page-subtitle mb-0">History log of administrative actions</p>
        </div>
        <a href="{{ route('admin.system.index') }}" class="btn btn-outline-secondary btn-sm">Back to System management</a>
    </div>

    <div class="dashboard-card p-4 mb-3">
        <div class="h6 text-uppercase text-muted mb-3" style="letter-spacing:0.05em;">Filters</div>
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-lg-5">
                <label class="form-label">Search</label>
                <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Description, action, or admin…">
            </div>
            <div class="col-12 col-lg-4">
                <label class="form-label">Scope</label>
                <select name="scope" class="form-select">
                    <option value="request" {{ ($scope ?? 'request') === 'request' ? 'selected' : '' }}>Forms &amp; approvals</option>
                    <option value="auth" {{ ($scope ?? 'request') === 'auth' ? 'selected' : '' }}>Log in</option>
                    <option value="form" {{ ($scope ?? 'request') === 'form' ? 'selected' : '' }}>Form activity</option>
                    <option value="approval" {{ ($scope ?? 'request') === 'approval' ? 'selected' : '' }}>Approvals</option>
                    <option value="reports" {{ ($scope ?? 'request') === 'reports' ? 'selected' : '' }}>Reports</option>
                    <option value="all" {{ ($scope ?? 'request') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-12 col-lg-3 d-flex gap-2 flex-wrap">
                <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                @if($search !== '' || ($scope ?? 'request') !== 'request')
                    <a href="{{ route('admin.system.audit') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>

        <hr class="my-4">

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <div class="h6 text-uppercase text-muted mb-1" style="letter-spacing:0.05em;">Audit Entries</div>
                <div class="small text-muted">
                    Showing
                    <b>{{ $logs->firstItem() }}</b>
                    to
                    <b>{{ $logs->lastItem() }}</b>
                    of
                    <b>{{ $logs->total() }}</b>
                </div>
            </div>
            <div class="small text-muted">
                <span class="badge text-bg-primary me-1">report.*</span>
                <span class="badge text-bg-success me-1">approval.*</span>
                <span class="badge text-bg-info me-1">form.*</span>
                <span class="badge text-bg-warning me-1">auth.*</span>
                <span class="badge text-bg-secondary">Other</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 170px;">When</th>
                        <th style="width: 180px;">Admin</th>
                        <th style="width: 170px;">Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    @php($action = (string) $log->action)
                    @php($badgeClass =
                        str_starts_with($action, 'report.') ? 'text-bg-primary' :
                        (str_starts_with($action, 'approval.') ? 'text-bg-success' :
                            (str_starts_with($action, 'form.') ? 'text-bg-info' :
                                (str_starts_with($action, 'auth.') ? 'text-bg-warning' :
                                    (str_starts_with($action, 'account.') ? 'text-bg-secondary' : 'text-bg-dark')
                                )
                            )
                        )
                    )
                    <tr>
                        <td class="text-nowrap text-muted small">
                            {{ $log->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                        </td>
                        <td><small>{{ $log->admin_label }}</small></td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $action }}</span>
                        </td>
                        <td>
                            @php($desc = (string) $log->description)
                            <span title="{{ $desc }}">
                                {{ \Illuminate\Support\Str::limit($desc, 120, '...') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted p-4">No audit entries found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
