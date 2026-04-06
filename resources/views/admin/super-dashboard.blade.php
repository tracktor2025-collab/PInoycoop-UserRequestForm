@extends('admin.layout')

@section('title', 'Super Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="page-title">Super Admin Dashboard</h1>
            <p class="page-subtitle">Quick metrics and latest submitted access requests</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card p-3">
                <div class="text-muted">Total Requests</div>
                <div class="metric-value">{{ $total }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card p-3">
                <div class="text-muted">Pending</div>
                <div class="metric-value text-warning">{{ $pending }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card p-3">
                <div class="text-muted">Approved</div>
                <div class="metric-value text-success">{{ $approved }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card p-3">
                <div class="text-muted">Rejected</div>
                <div class="metric-value text-danger">{{ $rejected }}</div>
            </div>
        </div>
    </div>

    <div class="dashboard-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Recent Requests</h2>
            <a href="{{ route('admin.pdf.archive') }}" class="btn btn-sm btn-outline-primary">Open PDF Backup Module</a>
        </div>

        <form method="GET" class="d-flex gap-2 align-items-center mb-3">
            <input
                type="text"
                name="search"
                class="form-control form-control-sm"
                placeholder="Search by Request # or Name"
                value="{{ request('search', '') }}"
            >
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
            @if(trim((string) request('search', '')) !== '')
                <a href="{{ route('admin.super.dashboard') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <x-admin.sort-th column="request_number" label="Request #" :sort="$sort" :direction="$direction" />
                    <x-admin.sort-th column="full_name" label="Name" :sort="$sort" :direction="$direction" />
                    <x-admin.sort-th column="systems" label="System(s)" :sort="$sort" :direction="$direction" />
                    <x-admin.sort-th column="status" label="Status" :sort="$sort" :direction="$direction" />
                    <x-admin.sort-th column="created_at" label="Date" :sort="$sort" :direction="$direction" />
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($recent as $item)
                    <tr>
                        <td>{{ $item->request_number ?: '-' }}</td>
                        <td>{{ $item->full_name }}</td>
                        <td>{{ implode(', ', $item->systems ?? []) ?: '-' }}</td>
                        <td><span class="badge text-bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($item->status) }}</span></td>
                        <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <div class="d-flex flex-wrap gap-1 justify-content-end align-items-center">
                                <a href="{{ route('admin.request.summary', $item) }}" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener">View form</a>
                                <a href="{{ route('admin.request.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <x-admin.delete-request-form :access-request="$item" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No requests yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $recent->links() }}
        </div>
    </div>

    <div class="dashboard-card p-4 mt-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
                <div class="h6 text-uppercase text-muted mb-1" style="letter-spacing:0.05em;">System Bookings</div>
                <h2 class="h5 mb-0">Requests by System</h2>
                <div class="small text-muted">Pie chart based on the latest 500 requests.</div>
            </div>
        </div>

        <div style="height: 280px;">
            <canvas id="systemsBookingsPieChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('systemsBookingsPieChart');
            if (!ctx) return;

            var labels = {!! json_encode($pieLabels ?? []) !!};
            var values = {!! json_encode($pieValues ?? []) !!};

            var palette = [
                'rgba(79, 70, 229, 0.85)',
                'rgba(16, 185, 129, 0.75)',
                'rgba(245, 158, 11, 0.75)',
                'rgba(239, 68, 68, 0.75)',
                'rgba(59, 130, 246, 0.75)',
                'rgba(236, 72, 153, 0.75)',
                'rgba(99, 102, 241, 0.75)',
                'rgba(20, 184, 166, 0.75)',
                'rgba(107, 114, 128, 0.75)'
            ];

            var colors = [];
            for (var i = 0; i < values.length; i++) {
                colors.push(palette[i % palette.length]);
            }

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
