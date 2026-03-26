<?php

namespace App\Http\Controllers;

use App\Models\AccessRequest;
use App\Models\Admin;
use App\Mail\AccessRequestStatusUpdated;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Revolution\Google\Sheets\Facades\Sheets;

class AdminAccessRequestController extends Controller
{
    private function apiSheetTitle(string $title): string
    {
        $escaped = str_replace("'", "''", trim($title));
        return "'" . $escaped . "'";
    }

    private function columnLetter(int $index): string
    {
        $index = max(1, $index);
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr(($index % 26) + 65) . $letters;
            $index = intdiv($index, 26);
        }
        return $letters;
    }

    private function syncStatusToGoogleSheets(AccessRequest $accessRequest): void
    {
        $spreadsheetId = (string) (config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', ''));
        $requestNumber = trim((string) ($accessRequest->request_number ?? ''));

        if ($spreadsheetId === '' || $requestNumber === '' || ! class_exists(\Revolution\Google\Sheets\Facades\Sheets::class)) {
            return;
        }

        $sheetBySystem = (array) config('google.sheet_by_system', []);
        $otherSheet = (string) config('google.sheet_other', config('google.sheet_name', 'Sheet1'));
        $defaultSheet = (string) config('google.sheet_name', 'Sheet1');

        $systems = is_array($accessRequest->systems) ? $accessRequest->systems : [];
        $tabs = [];
        foreach ($systems as $sys) {
            $sys = trim((string) $sys);
            if ($sys === '') {
                continue;
            }
            $tabs[] = (string) ($sheetBySystem[$sys] ?? $otherSheet);
        }

        if (empty($tabs)) {
            $tabs = [$defaultSheet, $otherSheet];
        }

        $tabs = array_values(array_unique(array_filter(array_map('trim', $tabs))));

        foreach ($tabs as $sheetName) {
            try {
                $quotedSheet = $this->apiSheetTitle($sheetName);
                $rows = Sheets::spreadsheet($spreadsheetId)
                    ->sheet($quotedSheet)
                    ->range('A1:AZ5000')
                    ->all();
            } catch (\Throwable $e) {
                report($e);
                continue;
            }

            if (empty($rows) || ! is_array($rows[0] ?? null)) {
                continue;
            }

            $header = array_map(
                static fn ($cell) => trim((string) $cell),
                $rows[0]
            );

            $requestNumberCol = array_search('Request Number', $header, true);
            if ($requestNumberCol === false) {
                continue;
            }

            for ($i = 1; $i < count($rows); $i++) {
                $row = is_array($rows[$i] ?? null) ? $rows[$i] : [];
                $sheetRequestNumber = trim((string) ($row[$requestNumberCol] ?? ''));

                if ($sheetRequestNumber !== $requestNumber) {
                    continue;
                }

                $rowNumber = $i + 1;
                $this->applyGoogleSheetApprovalColumns(
                    $spreadsheetId,
                    $quotedSheet,
                    $header,
                    $rowNumber,
                    $accessRequest
                );

                break;
            }
        }
    }

    /**
     * @param  array<int, string>  $header
     */
    private function applyGoogleSheetApprovalColumns(
        string $spreadsheetId,
        string $quotedSheet,
        array $header,
        int $rowNumber,
        AccessRequest $accessRequest,
    ): void {
        $fieldValues = $this->sheetApprovalFieldValues($accessRequest);
        $headerConfig = (array) config('google.sheet_sync_approval_headers', []);

        foreach ($headerConfig as $fieldKey => $aliases) {
            if (! is_string($fieldKey) || ! isset($fieldValues[$fieldKey])) {
                continue;
            }

            $aliasList = array_values(array_filter((array) $aliases, static fn ($v) => is_string($v) && trim($v) !== ''));
            $col = $this->firstMatchingHeaderColumn($header, $aliasList);
            if ($col === false) {
                // Auto-add missing approval columns so admin metadata can be written
                // even when the sheet was created before these fields existed.
                $headerName = (string) ($aliasList[0] ?? $fieldKey);
                $newColIndex = count($header); // zero-based
                $headerCell = $this->columnLetter($newColIndex + 1).'1';

                try {
                    Sheets::spreadsheet($spreadsheetId)
                        ->sheet($quotedSheet)
                        ->range($headerCell)
                        ->update([[$headerName]]);

                    $header[] = $headerName;
                    $col = $newColIndex;
                } catch (\Throwable $e) {
                    report($e);
                    continue;
                }
            }

            $cell = $this->columnLetter($col + 1).$rowNumber;
            $value = (string) $fieldValues[$fieldKey];

            try {
                Sheets::spreadsheet($spreadsheetId)
                    ->sheet($quotedSheet)
                    ->range($cell)
                    ->update([[$value]]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function sheetApprovalFieldValues(AccessRequest $accessRequest): array
    {
        $tz = (string) config('app.timezone', 'UTC');
        $at = $accessRequest->approved_at;
        $atStr = '';
        if ($at !== null) {
            $atStr = $at->copy()->timezone($tz)->format('Y-m-d H:i');
        }

        return [
            'status' => ucfirst((string) $accessRequest->status),
            'approved_by' => (string) ($accessRequest->approved_by ?? ''),
            'approved_at' => $atStr,
            'approval_remarks' => (string) ($accessRequest->approval_remarks ?? ''),
        ];
    }

    /**
     * @param  array<int, string>  $header
     * @param  array<int, mixed>  $aliases
     */
    private function firstMatchingHeaderColumn(array $header, array $aliases): int|false
    {
        // Normalize header values for matching.
        // Google Sheets headers might differ by case (e.g., "Approved by" vs "Approved By").
        $normalizedHeader = array_map(
            static fn ($cell) => mb_strtolower(trim((string) $cell)),
            $header
        );

        foreach ($aliases as $name) {
            if (! is_string($name) || $name === '') {
                continue;
            }

            $needle = mb_strtolower(trim($name));
            $col = array_search($needle, $normalizedHeader, true);
            if ($col !== false) {
                return $col;
            }
        }

        return false;
    }

    public function loginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()->where('email', $validated['email'])->first();

        if ($admin === null || ! Hash::check($validated['password'], $admin->password)) {
            return back()->withInput()->with('error', 'Invalid email or password.');
        }

        $request->session()->forget([
            'admin_authenticated',
            'admin_id',
            'pending_2fa_admin_id',
            'admin_totp_enrollment_secret',
        ]);
        $request->session()->regenerate();
        $request->session()->put('pending_2fa_admin_id', $admin->id);

        if ($admin->hasEnabledTwoFactor()) {
            return redirect()->route('admin.two-factor.challenge');
        }

        return redirect()->route('admin.two-factor.setup');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'admin_authenticated',
            'admin_id',
            'pending_2fa_admin_id',
            'admin_totp_enrollment_secret',
        ]);
        // Invalidate the whole session so Back/Forward can't restore auth state.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }

    public function dashboard(Request $request): View
    {
        $total = AccessRequest::count();
        $pending = AccessRequest::where('status', 'pending')->count();
        $approved = AccessRequest::where('status', 'approved')->count();
        $rejected = AccessRequest::where('status', 'rejected')->count();
        $search = trim((string) $request->query('search', ''));

        // Pie chart data: number of requests ("bookings") per system.
        // Each AccessRequest can include multiple systems (JSON array), so counts can exceed total requests.
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

        $systemCounts = array_fill_keys($systemModules, 0);
        $unknownCounts = [];

        // Limit the scan to keep dashboard fast.
        $requestsForPie = AccessRequest::query()
            ->select('systems')
            ->latest()
            ->limit(500)
            ->get();

        foreach ($requestsForPie as $accessRequest) {
            $systems = is_array($accessRequest->systems) ? $accessRequest->systems : [];
            foreach ($systems as $sys) {
                $sys = trim((string) $sys);
                if ($sys === '') {
                    continue;
                }
                if (array_key_exists($sys, $systemCounts)) {
                    $systemCounts[$sys]++;
                } else {
                    $unknownCounts[$sys] = ($unknownCounts[$sys] ?? 0) + 1;
                }
            }
        }

        $systemCounts = array_merge($systemCounts, $unknownCounts);
        arsort($systemCounts);

        // Keep the chart readable: show top 8 plus an "Other" bucket.
        $topN = 8;
        $pieLabels = [];
        $pieValues = [];
        $otherTotal = 0;

        $idx = 0;
        foreach ($systemCounts as $sysName => $count) {
            if ($idx < $topN) {
                if ((int) $count > 0) {
                    $pieLabels[] = (string) $sysName;
                    $pieValues[] = (int) $count;
                }
            } else {
                $otherTotal += (int) $count;
            }
            $idx++;
        }

        if ($otherTotal > 0) {
            $pieLabels[] = 'Other';
            $pieValues[] = $otherTotal;
        }

        // Paginate "Recent Requests" (10 per page) instead of a fixed limit.
        $recent = AccessRequest::query()
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('request_number', 'like', '%' . $search . '%')
                        ->orWhere('full_name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.dashboard',
            compact('total', 'pending', 'approved', 'rejected', 'recent', 'search', 'pieLabels', 'pieValues')
        );
    }

    public function approvals(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');
        $allowedStatuses = ['pending', 'approved', 'rejected', 'all'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $requests = AccessRequest::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.approvals', compact('requests', 'status'));
    }

    /**
     * Apply status, sync sheets, and notify requester when approved or rejected.
     */
    private function finalizeAccessRequestDecision(
        AccessRequest $accessRequest,
        string $status,
        ?string $approvalRemarks,
        string $adminLabel,
    ): void {
        $accessRequest->status = $status;
        $accessRequest->approval_remarks = $approvalRemarks;

        if (in_array($status, ['approved', 'rejected'], true)) {
            $accessRequest->approved_by = $adminLabel;
            $accessRequest->approved_at = now();
        } else {
            $accessRequest->approved_by = null;
            $accessRequest->approved_at = null;
        }

        $accessRequest->save();
        $this->syncStatusToGoogleSheets($accessRequest);

        if (in_array($status, ['approved', 'rejected'], true)) {
            try {
                $userEmail = (string) ($accessRequest->email ?? '');
                if ($userEmail !== '') {
                    $systemsText = is_array($accessRequest->systems)
                        ? implode(', ', array_filter(array_map('strval', $accessRequest->systems)))
                        : (string) ($accessRequest->systems ?? '');

                    Mail::to($userEmail)->queue(new AccessRequestStatusUpdated(
                        name: (string) ($accessRequest->full_name ?? ''),
                        requestNumber: (string) ($accessRequest->request_number ?? $accessRequest->id ?? ''),
                        status: (string) $status,
                        systems: $systemsText,
                        remarks: $accessRequest->approval_remarks,
                        adminLabel: $adminLabel,
                    ));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public function updateApproval(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:approved,rejected,pending'],
            'approval_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $actingAdmin = Admin::query()->find((int) $request->session()->get('admin_id'));
        $adminLabel = $actingAdmin !== null
            ? trim($actingAdmin->name).' <'.$actingAdmin->email.'>'
            : 'Admin';

        $previousStatus = (string) $accessRequest->status;

        $this->finalizeAccessRequestDecision(
            $accessRequest,
            $validated['status'],
            $validated['approval_remarks'] ?? null,
            $adminLabel,
        );

        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
        AuditLogger::log(
            $request,
            'approval.updated',
            sprintf('Request %s: status changed from %s to %s.', $ref, $previousStatus, $validated['status']),
            AccessRequest::class,
            $accessRequest->id,
            ['from' => $previousStatus, 'to' => $validated['status']],
        );

        return back()->with('success', 'Request status updated successfully.');
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:5'],
            'ids.*' => ['integer', 'exists:access_requests,id'],
            'bulk_approval_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $actingAdmin = Admin::query()->find((int) $request->session()->get('admin_id'));
        $adminLabel = $actingAdmin !== null
            ? trim($actingAdmin->name).' <'.$actingAdmin->email.'>'
            : 'Admin';

        $requests = AccessRequest::query()
            ->whereIn('id', $validated['ids'])
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        if ($requests->isEmpty()) {
            return back()->with('error', 'No pending requests matched the selection.');
        }

        $remarks = $validated['bulk_approval_remarks'] ?? null;
        foreach ($requests as $accessRequest) {
            $this->finalizeAccessRequestDecision($accessRequest, 'approved', $remarks, $adminLabel);
        }

        $numbers = $requests->map(fn (AccessRequest $r) => (string) ($r->request_number ?: '#'.$r->id))->implode(', ');

        AuditLogger::log(
            $request,
            'approval.bulk_approved',
            sprintf('Bulk approved %d request(s): %s', $requests->count(), $numbers),
            null,
            null,
            [
                'ids' => $requests->pluck('id')->values()->all(),
                'count' => $requests->count(),
            ],
        );

        return back()->with('success', $requests->count().' request(s) approved.');
    }

    public function pdfArchive(Request $request): View
    {
        $system = trim((string) $request->query('system', 'all'));
        $status = trim((string) $request->query('status', 'all'));
        $search = trim((string) $request->query('search', ''));

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

        $requests = AccessRequest::query()
            ->where(function ($q): void {
                // Prefer stored PDF backups when available, but fall back to requests that can
                // regenerate PDFs from their stored `summary` data.
                $q->whereNotNull('pdf_path')
                    ->where('pdf_path', '!=', '')
                    ->orWhereNotNull('summary');
            })
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($system !== 'all', fn ($q) => $q->whereJsonContains('systems', $system))
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('request_number', 'like', '%' . $search . '%')
                        ->orWhere('full_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pdf-archive', compact('requests', 'systemModules', 'system', 'status', 'search'));
    }

    public function downloadPdf(AccessRequest $accessRequest)
    {
        $summary = is_array($accessRequest->summary) ? $accessRequest->summary : [];
        $baseName = (string) ($accessRequest->request_number ?: $accessRequest->id ?: 'request');
        $safeBaseName = preg_replace('/[^A-Za-z0-9\-]/', '-', $baseName) ?: 'request';
        $filename = 'access-request-' . $safeBaseName . '.pdf';

        // Regenerate from current template so admin downloads always use latest PDF layout.
        if (! empty($summary)) {
            try {
                /** @var \Barryvdh\DomPDF\PDF $pdf */
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('success-pdf', [
                    'summary' => $summary,
                ])->setPaper('a4', 'portrait');

                return $pdf->download($filename);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $path = (string) $accessRequest->pdf_path;
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            return back()->with('error', 'PDF backup file is missing.');
        }

        return Storage::disk('local')->download(
            $path,
            $filename
        );
    }
}
