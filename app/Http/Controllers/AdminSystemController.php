<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\AuditLog;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSystemController extends Controller
{
    private function classifyReportPeriod(CarbonInterface $start, CarbonInterface $end): string
    {
        if ($start->toDateString() === $end->toDateString()) {
            return 'daily';
        }

        if ($start->day === 1
            && $end->day === $end->daysInMonth
            && $start->month === $end->month
            && $start->year === $end->year) {
            return 'monthly';
        }

        if ($start->month === 1 && $start->day === 1
            && $end->month === 12 && $end->day === 31
            && $start->year === $end->year) {
            return 'yearly';
        }

        return 'custom_range';
    }

    public function index(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);
        $minYear = now()->year - 5;
        // Allow viewing future years too (so after 2026 it keeps incrementing).
        $maxYear = now()->year + 5;
        if ($year < $minYear || $year > $maxYear) {
            $year = now()->year;
        }

        // Prepare chart/table data for months 1..12.
        $months = range(1, 12);
        $labels = array_map(
            static fn (int $m): string => Carbon::createFromDate($year, $m, 1)->format('M'),
            $months
        );

        $pending = array_fill(0, 12, 0);
        $approved = array_fill(0, 12, 0);
        $rejected = array_fill(0, 12, 0);

        $rows = AccessRequest::query()
            ->selectRaw('MONTH(COALESCE(request_date, created_at)) as month, status, COUNT(*) as count')
            ->whereRaw('YEAR(COALESCE(request_date, created_at)) = ?', [$year])
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->groupByRaw('MONTH(COALESCE(request_date, created_at)), status')
            ->get();

        foreach ($rows as $row) {
            $monthIndex = max(1, (int) $row->month) - 1;
            if ($monthIndex < 0 || $monthIndex > 11) {
                continue;
            }
            $count = (int) $row->count;
            $status = (string) $row->status;
            if ($status === 'pending') {
                $pending[$monthIndex] = $count;
            } elseif ($status === 'approved') {
                $approved[$monthIndex] = $count;
            } elseif ($status === 'rejected') {
                $rejected[$monthIndex] = $count;
            }
        }

        $totals = array_map(
            static fn (int $p, int $a, int $r): int => $p + $a + $r,
            $pending,
            $approved,
            $rejected
        );

        $yearOptions = [];
        for ($y = $maxYear; $y >= $minYear; $y--) {
            $yearOptions[] = $y;
        }

        $systemModules = [
            'ATM Portal',
            'SMS Portal',
            'MSP-ISS Portal',
            'MSP-ISS FTP',
            'Helpdesk',
            'PASS',
            'CASH ONLINE',
            'CORE 3.0',
            'BIZMOTO PORTAL (Business Center)',
            'PINOYCOOP PORTAL',
            'MVM Portal',
        ];

        return view('admin.system-management', [
            'year' => $year,
            'yearOptions' => $yearOptions,
            'labels' => $labels,
            'pendingCounts' => $pending,
            'approvedCounts' => $approved,
            'rejectedCounts' => $rejected,
            'totalCounts' => $totals,
            'systemModules' => $systemModules,
            'defaultReportDay' => now()->toDateString(),
            'defaultReportMonth' => now()->format('Y-m'),
        ]);
    }

    public function auditTrail(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $scope = (string) $request->query('scope', 'request');
        if (! in_array($scope, ['request', 'auth', 'form', 'approval', 'reports', 'all'], true)) {
            $scope = 'request';
        }

        $logs = AuditLog::query()
            ->when($scope === 'request', function ($q): void {
                $q->where(function ($sub): void {
                    $sub->where('action', 'like', 'approval.%')
                        ->orWhere('action', 'like', 'form.%');
                });
            })
            ->when($scope === 'auth', function ($q): void {
                $q->where('action', 'like', 'auth.%');
            })
            ->when($scope === 'form', function ($q): void {
                $q->where(function ($sub): void {
                    $sub->where('action', 'like', 'form.%');
                });
            })
            ->when($scope === 'approval', function ($q): void {
                $q->where('action', 'like', 'approval.%');
            })
            ->when($scope === 'reports', function ($q): void {
                $q->where('action', 'like', 'report.%');
            })
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('description', 'like', '%'.$search.'%')
                        ->orWhere('action', 'like', '%'.$search.'%')
                        ->orWhere('admin_label', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.audit-trail', compact('logs', 'search', 'scope'));
    }

    public function reportData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'system' => ['required', 'string', 'max:255'],
            'statuses' => ['required', 'array', 'min:1', 'max:3'],
            'statuses.*' => ['required', 'string', 'in:approved,rejected,pending'],
            'format' => ['required', 'string', 'in:pdf,excel'],
        ]);

        /** @var Carbon $start */
        $start = Carbon::parse($validated['start_date'])->startOfDay();
        /** @var Carbon $end */
        $end = Carbon::parse($validated['end_date'])->endOfDay();

        $system = (string) $validated['system'];
        $statuses = array_values(array_map(static fn ($s): string => (string) $s, $validated['statuses']));

        $baseQuery = AccessRequest::query()->whereRaw(
            'DATE(COALESCE(request_date, created_at)) >= ? AND DATE(COALESCE(request_date, created_at)) <= ?',
            [$start->toDateString(), $end->toDateString()]
        )->whereIn('status', $statuses);

        if ($system !== 'all') {
            $baseQuery->whereJsonContains('systems', $system);
        }

        $totalRequests = (int) $baseQuery->count();
        $pendingCount = (int) (clone $baseQuery)->where('status', 'pending')->count();
        $approvedCount = (int) (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedCount = (int) (clone $baseQuery)->where('status', 'rejected')->count();

        $statusBreakdown = [
            'pending' => [
                'count' => $pendingCount,
                'percent' => $totalRequests > 0 ? round($pendingCount * 100 / $totalRequests, 1) : 0.0,
            ],
            'approved' => [
                'count' => $approvedCount,
                'percent' => $totalRequests > 0 ? round($approvedCount * 100 / $totalRequests, 1) : 0.0,
            ],
            'rejected' => [
                'count' => $rejectedCount,
                'percent' => $totalRequests > 0 ? round($rejectedCount * 100 / $totalRequests, 1) : 0.0,
            ],
        ];

        $approvalDenominator = $approvedCount + $rejectedCount;
        $approvalRate = $approvalDenominator > 0 ? round($approvedCount * 100 / $approvalDenominator, 2) : 0.0;

        // System counts within the filtered query; each request can include multiple systems.
        $systemCounts = [];
        $systemQuery = (clone $baseQuery)->select('systems');
        $maxSystems = 30;

        foreach ($systemQuery->get() as $req) {
            $systems = is_array($req->systems) ? $req->systems : [];
            $systems = array_values(array_unique(array_filter(array_map(static function ($s): string {
                $val = trim((string) $s);

                return $val === '' ? '' : $val;
            }, $systems), static fn (string $s): bool => $s !== '')));

            foreach ($systems as $systemName) {
                $systemCounts[$systemName] = ($systemCounts[$systemName] ?? 0) + 1;
            }
        }

        arsort($systemCounts);
        $mostRequestedSystem = (string) (array_key_first($systemCounts) ?? 'N/A');

        // Build system table rows; cap to avoid huge output.
        $systemRows = [];
        $i = 0;
        foreach ($systemCounts as $systemName => $count) {
            $systemRows[] = [
                'system' => (string) $systemName,
                'count' => (int) $count,
            ];
            $i++;
            if ($i >= $maxSystems) {
                break;
            }
        }

        // Show full exact date range in the report header.
        // Example: "Mar 01, 2026 - Mar 31, 2026"
        $periodLabel = $start->format('M d, Y').' - '.$end->format('M d, Y');

        $reportPeriod = $this->classifyReportPeriod($start, $end);

        AuditLogger::log(
            $request,
            'report.generated',
            sprintf(
                'Generated %s report (%s) for %s | system=%s | statuses=%s | total=%d',
                $reportPeriod,
                (string) $validated['format'],
                (string) $periodLabel,
                $system,
                implode(',', $statuses),
                (int) $totalRequests
            ),
            null,
            null,
            [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'system' => $system,
                'statuses' => $statuses,
                'format' => (string) $validated['format'],
                'totalRequests' => $totalRequests,
                'report_period' => $reportPeriod,
            ]
        );

        return response()->json([
            'periodLabel' => $periodLabel,
            'reportPeriod' => $reportPeriod,
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'system' => $system,
                'statuses' => $statuses,
                'format' => (string) $validated['format'],
            ],
            'summary' => [
                'totalRequests' => $totalRequests,
                'approvedCount' => $approvedCount,
                'rejectedCount' => $rejectedCount,
                'pendingCount' => $pendingCount,
                'approvalRate' => $approvalRate,
                'mostRequestedSystem' => $mostRequestedSystem,
                'statusBreakdown' => $statusBreakdown,
            ],
            'systemRows' => $systemRows,
        ]);
    }
}
