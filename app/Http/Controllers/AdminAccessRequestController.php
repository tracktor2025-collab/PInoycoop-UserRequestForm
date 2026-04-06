<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessRequestFormRequest;
use App\Jobs\HandleAccessRequestSubmissionJob;
use App\Mail\AccessRequestStatusUpdated;
use App\Models\AccessRequest;
use App\Models\Admin;
use App\Services\AccessRequestGoogleSheetsService;
use App\Services\AccessRequestSummaryBuilder;
use App\Services\AuditLogger;
use App\Support\RequestNumberIssuer;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Revolution\Google\Sheets\Facades\Sheets;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAccessRequestController extends Controller
{
    /**
     * @param  array<int, string>  $allowedColumns
     * @return array{sort: string, direction: string}
     */
    private function normalizeAccessRequestSort(Request $request, array $allowedColumns, string $defaultColumn, string $defaultDirection): array
    {
        $sort = (string) $request->query('sort', $defaultColumn);
        $direction = strtolower((string) $request->query('direction', $defaultDirection));
        if (! in_array($sort, $allowedColumns, true)) {
            $sort = $defaultColumn;
        }
        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = $defaultDirection;
        }

        return ['sort' => $sort, 'direction' => $direction];
    }

    private function applyAccessRequestSort(Builder $query, string $sort, string $direction): void
    {
        $query->orderBy($sort, $direction)->orderByDesc('id');
    }

    private function apiSheetTitle(string $title): string
    {
        $escaped = str_replace("'", "''", trim($title));

        return "'".$escaped."'";
    }

    private function columnLetter(int $index): string
    {
        $index = max(1, $index);
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr(($index % 26) + 65).$letters;
            $index = intdiv($index, 26);
        }

        return $letters;
    }

    private function syncStatusToGoogleSheets(AccessRequest $accessRequest): void
    {
        $spreadsheetId = (string) (config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', ''));
        $requestNumber = trim((string) ($accessRequest->request_number ?? ''));

        if ($spreadsheetId === '' || $requestNumber === '' || ! class_exists(Sheets::class)) {
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
        return view('admin.login', [
            'portal' => 'admin',
            'formAction' => route('admin.login'),
            'title' => 'Admin Dashboard Login',
            'subtitle' => 'Sign in with your admin email and password.',
            'switchUrl' => route('super.login.form'),
            'switchLabel' => 'Login as Super Admin',
        ]);
    }

    public function loginFormSuper(): View
    {
        return view('admin.login', [
            'portal' => 'super',
            'formAction' => route('super.login'),
            'title' => 'Super Admin Dashboard Login',
            'subtitle' => 'Sign in with your super admin email and password.',
            'switchUrl' => route('admin.login.form'),
            'switchLabel' => 'Login as Normal Admin',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        return $this->authenticateByPortal($request, 'admin');
    }

    public function loginSuper(Request $request): RedirectResponse
    {
        return $this->authenticateByPortal($request, 'super');
    }

    private function authenticateByPortal(Request $request, string $portal): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::query()->where('email', $validated['email'])->first();

        if ($admin === null || ! Hash::check($validated['password'], $admin->password)) {
            return back()->withInput()->with('error', 'Invalid email or password.');
        }

        if ($portal === 'admin' && $admin->isSuperAdmin()) {
            return back()->withInput()->with('error', 'This account is a super admin. Please use the Super Admin login page.');
        }
        if ($portal === 'super' && ! $admin->isSuperAdmin()) {
            return back()->withInput()->with('error', 'This account is not a super admin.');
        }

        $request->session()->forget([
            'admin_authenticated',
            'admin_id',
            'pending_2fa_admin_id',
            'admin_totp_enrollment_secret',
            'login_portal',
        ]);
        $request->session()->regenerate();
        $request->session()->put('pending_2fa_admin_id', $admin->id);
        $request->session()->put('login_portal', $portal);

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
            'login_portal',
        ]);
        // Invalidate the whole session so Back/Forward can't restore auth state.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }

    public function dashboard(Request $request): View
    {
        return view('admin.dashboard', $this->dashboardViewData($request));
    }

    public function superDashboard(Request $request): View
    {
        return view('admin.super-dashboard', $this->dashboardViewData($request));
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardViewData(Request $request): array
    {
        $total = AccessRequest::count();
        $pending = AccessRequest::where('status', 'pending')->count();
        $approved = AccessRequest::where('status', 'approved')->count();
        $rejected = AccessRequest::where('status', 'rejected')->count();
        $search = trim((string) $request->query('search', ''));
        $sortState = $this->normalizeAccessRequestSort(
            $request,
            ['request_number', 'full_name', 'systems', 'status', 'created_at'],
            'created_at',
            'desc'
        );
        $sort = $sortState['sort'];
        $direction = $sortState['direction'];

        // Pie chart data: number of requests ("bookings") per system.
        // Each AccessRequest can include multiple systems (JSON array), so counts can exceed total requests.
        $systemModules = config('access_request.system_modules', []);

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
                    $sub->where('request_number', 'like', '%'.$search.'%')
                        ->orWhere('full_name', 'like', '%'.$search.'%');
                });
            })
            ->tap(function (Builder $q) use ($sort, $direction): void {
                $this->applyAccessRequestSort($q, $sort, $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return compact(
            'total',
            'pending',
            'approved',
            'rejected',
            'recent',
            'search',
            'sort',
            'direction',
            'pieLabels',
            'pieValues'
        );
    }

    public function approvals(Request $request): View
    {
        $status = (string) $request->query('status', 'pending');
        $allowedStatuses = ['pending', 'approved', 'rejected', 'all'];
        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $sortState = $this->normalizeAccessRequestSort(
            $request,
            ['request_number', 'full_name', 'systems', 'status', 'created_at'],
            'created_at',
            'desc'
        );
        $sort = $sortState['sort'];
        $direction = $sortState['direction'];

        $requests = AccessRequest::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->tap(function (Builder $q) use ($sort, $direction): void {
                $this->applyAccessRequestSort($q, $sort, $direction);
            })
            ->paginate(12)
            ->withQueryString();

        return view('admin.approvals', compact('requests', 'status', 'sort', 'direction'));
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
            'approval_signed' => ['nullable', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:5120'],
        ]);

        $toApproved = $validated['status'] === 'approved';
        $fromNonApproved = $accessRequest->status !== 'approved';
        if ($toApproved && $fromNonApproved) {
            $hasNewFile = $request->hasFile('approval_signed');
            $existingPath = (string) ($accessRequest->approval_signed_path ?? '');
            $hasExisting = $existingPath !== '' && Storage::disk('local')->exists($existingPath);
            if (! $hasNewFile && ! $hasExisting) {
                return back()
                    ->withErrors(['approval_signed' => 'Upload a signed approval document (PDF or image) before approving.'])
                    ->withInput();
            }
        }

        if ($request->hasFile('approval_signed')) {
            $newPath = $request->file('approval_signed')->store(
                'approval-signatures/'.$accessRequest->id,
                'local'
            );
            $oldPath = (string) ($accessRequest->approval_signed_path ?? '');
            if ($oldPath !== '' && Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
            $accessRequest->approval_signed_path = $newPath;
        }

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
        $approvalAction = match ($validated['status']) {
            'approved' => 'approval.approved',
            'rejected' => 'approval.rejected',
            default => 'approval.pending',
        };
        AuditLogger::log(
            $request,
            $approvalAction,
            sprintf('Request %s: status changed from %s to %s.', $ref, $previousStatus, $validated['status']),
            AccessRequest::class,
            $accessRequest->id,
            ['from' => $previousStatus, 'to' => $validated['status']],
        );

        return back()->with('success', 'Request status updated successfully.');
    }

    public function showRequestSummary(Request $request, AccessRequest $accessRequest): View
    {
        $admin = Admin::query()->find((int) $request->session()->get('admin_id'));
        $backUrl = ($admin !== null && $admin->isSuperAdmin())
            ? route('super.dashboard')
            : route('admin.dashboard');

        $summary = is_array($accessRequest->summary) ? $accessRequest->summary : [];
        if ($summary === []) {
            $summary = $this->minimalSummaryFromAccessRequest($accessRequest);
        }

        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
        AuditLogger::log(
            $request,
            'form.viewed',
            sprintf('Viewed access request form summary for %s.', $ref),
            AccessRequest::class,
            $accessRequest->id,
            ['request_number' => $accessRequest->request_number],
        );

        return view('success', [
            'summary' => $summary,
            'adminPreview' => true,
            'adminBackUrl' => $backUrl,
            'accessRequestId' => $accessRequest->id,
            'adminDeleteAccessRequest' => $accessRequest,
        ]);
    }

    public function destroyRequest(Request $request, AccessRequest $accessRequest): RedirectResponse
    {
        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);

        foreach (['approval_signed_path', 'pdf_path'] as $key) {
            $path = (string) ($accessRequest->{$key} ?? '');
            if ($path !== '' && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }

        try {
            app(AccessRequestGoogleSheetsService::class)->deleteRequestRows($accessRequest);
        } catch (\Throwable $e) {
            report($e);
        }

        AuditLogger::log(
            $request,
            'form.deleted',
            sprintf('Deleted access request %s.', $ref),
            AccessRequest::class,
            $accessRequest->id,
            ['request_number' => $accessRequest->request_number],
        );

        $accessRequest->delete();

        return redirect()->to($this->safeRedirectAfterDelete($request))
            ->with('success', 'Access request deleted.');
    }

    private function safeRedirectAfterDelete(Request $request): string
    {
        $to = $request->input('redirect_to');
        $base = rtrim((string) config('app.url'), '/');
        if (is_string($to) && $to !== '' && ($base === '' || str_starts_with($to, $base))) {
            return $to;
        }

        $admin = Admin::query()->find((int) $request->session()->get('admin_id'));
        if ($admin !== null && $admin->isSuperAdmin()) {
            return route('super.dashboard');
        }

        return route('admin.dashboard');
    }

    public function editRequestForm(Request $request, AccessRequest $accessRequest): View
    {
        $creatingNewFromApproved = $accessRequest->status === 'approved';
        $requestNumberPreview = (string) ($accessRequest->request_number ?? '');

        if ($creatingNewFromApproved) {
            $sessionKey = 'admin_new_request_preview:'.$accessRequest->id;
            $cached = $request->session()->get($sessionKey);
            if (is_string($cached) && trim($cached) !== '') {
                $requestNumberPreview = trim($cached);
            } else {
                $requestNumberPreview = RequestNumberIssuer::reserveNext();
                $request->session()->put($sessionKey, $requestNumberPreview);
            }
        }

        $admin = Admin::query()->find((int) $request->session()->get('admin_id'));
        $adminCancelUrl = ($admin !== null && $admin->isSuperAdmin())
            ? route('super.dashboard')
            : route('admin.dashboard');

        return view('user-request-form', [
            'requestNumberPreview' => $requestNumberPreview,
            'editPrefill' => $this->formPrefillFromAccessRequest($accessRequest),
            'adminEdit' => true,
            'adminEditAccessRequest' => $accessRequest,
            'adminEditCreatesNew' => $creatingNewFromApproved,
            'formAction' => route('admin.request.update', $accessRequest),
            'adminCancelUrl' => $adminCancelUrl,
            'adminEditBanner' => $creatingNewFromApproved
                ? 'This request is approved. Saving creates a new pending request with the request number shown below. The approved record will not be modified.'
                : null,
        ]);
    }

    public function updateRequest(AccessRequestFormRequest $request, AccessRequest $accessRequest): RedirectResponse
    {
        $validated = $request->validated();

        if (($validated['access_type'] ?? null) === 'Temporary' && empty($validated['access_end_date'])) {
            return redirect()
                ->route('admin.request.edit', $accessRequest)
                ->withInput()
                ->with('error', 'End Date is required for Temporary access.');
        }

        $systems = is_array($request->systems) ? array_values(array_filter($request->systems)) : [];

        if ($accessRequest->status === 'approved') {
            $request->session()->forget('admin_new_request_preview:'.$accessRequest->id);

            $newNumber = trim((string) ($validated['resource_request_number'] ?? ''));
            if ($newNumber === '' || ! RequestNumberIssuer::isValidIssuedFormat($newNumber)) {
                $newNumber = RequestNumberIssuer::reserveNext();
            }
            $validated['request_number'] = $newNumber;

            $summary = AccessRequestSummaryBuilder::fromValidated($request, $validated);

            $new = AccessRequest::query()->create([
                'source_request_id' => $accessRequest->id,
                'request_number' => $newNumber,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'mobile_no' => $validated['mobile_no'] ?? null,
                'coop_name' => $validated['coop_name'] ?? null,
                'branch' => $validated['branch'] ?? null,
                'request_date' => $validated['request_date'] ?? null,
                'status' => 'pending',
                'systems' => $systems,
                'summary' => $summary,
            ]);

            $jobPayload = array_merge($validated, [
                'request_number' => $newNumber,
                'systems' => $systems,
            ]);
            dispatch(new HandleAccessRequestSubmissionJob($jobPayload, now()->toDateTimeString()));

            $refOld = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
            AuditLogger::log(
                $request,
                'form.created_from_approved',
                sprintf('Created new pending request %s from approved request %s.', $newNumber, $refOld),
                AccessRequest::class,
                $new->id,
                ['source_id' => $accessRequest->id, 'new_request_number' => $newNumber],
            );

            return redirect()
                ->route('admin.request.summary', $new)
                ->with('success', 'A new pending request was created from the approved record. The original approval was not changed.');
        }

        $validated['request_number'] = (string) ($accessRequest->request_number ?? '');
        $summary = AccessRequestSummaryBuilder::fromValidated($request, $validated);

        $before = [
            'full_name' => $accessRequest->full_name,
            'email' => $accessRequest->email,
            'status' => $accessRequest->status,
        ];

        $accessRequest->fill([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'] ?? null,
            'mobile_no' => $validated['mobile_no'] ?? null,
            'coop_name' => $validated['coop_name'] ?? null,
            'branch' => $validated['branch'] ?? null,
            'request_date' => $validated['request_date'] ?? null,
            'systems' => $systems,
            'summary' => $summary,
        ]);
        $accessRequest->save();

        try {
            $accessRequest->refresh();
            $syncPayload = array_merge($validated, [
                'request_number' => $accessRequest->request_number,
                'systems' => $systems,
            ]);
            app(AccessRequestGoogleSheetsService::class)->updateRequestRows(
                $accessRequest,
                $syncPayload,
                now()->toDateTimeString(),
            );
            $this->syncStatusToGoogleSheets($accessRequest);
        } catch (\Throwable $e) {
            report($e);
        }

        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
        AuditLogger::log(
            $request,
            'form.updated',
            sprintf('Updated access request %s.', $ref),
            AccessRequest::class,
            $accessRequest->id,
            ['before' => $before, 'request_number' => $accessRequest->request_number],
        );

        return redirect()
            ->route('admin.request.summary', $accessRequest)
            ->with('success', 'Request saved and Google Sheet rows updated where the request number was found.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formPrefillFromAccessRequest(AccessRequest $ar): array
    {
        $s = is_array($ar->summary) ? $ar->summary : [];
        $g = static fn (string $k): string => trim((string) ($s[$k] ?? ''));
        $list = static function (string $v): array {
            $v = trim($v);
            if ($v === '' || $v === '-') {
                return [];
            }

            return array_values(array_filter(array_map('trim', explode(',', $v))));
        };

        $reqTypeStr = $g('Request Type');
        $requestType = $reqTypeStr === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $reqTypeStr))));

        $systems = is_array($ar->systems) ? $ar->systems : [];

        $rd = $ar->request_date;
        $requestDate = $rd !== null
            ? $rd->format('Y-m-d')
            : (($t = $g('Date of Request')) !== '-' && $t !== '' ? $t : date('Y-m-d'));

        return [
            'request_type' => $requestType,
            'resource_request_number' => (string) ($ar->request_number ?? ''),
            'full_name' => $ar->full_name ?: ($g('Full Name') !== '-' ? $g('Full Name') : ''),
            'coop_name' => $ar->coop_name ?: ($g('Cooperative Name') !== '-' ? $g('Cooperative Name') : ''),
            'branch' => $ar->branch ?: ($g('Branch') !== '-' ? $g('Branch') : ''),
            'request_date' => $requestDate,
            'mobile_no' => $ar->mobile_no ?: ($g('Mobile No') !== '-' ? $g('Mobile No') : ''),
            'address' => $g('Address') !== '-' ? $g('Address') : '',
            'postal_code' => $g('Postal Code') !== '-' ? $g('Postal Code') : '',
            'gender' => $g('Gender') !== '-' ? $g('Gender') : '',
            'place_of_birth' => $g('Place of Birth') !== '-' ? $g('Place of Birth') : '',
            'email' => $ar->email ?: ($g('Email Address') !== '-' ? $g('Email Address') : ''),
            'systems' => $systems,
            'access_type' => $g('Access Type') !== '-' ? $g('Access Type') : '',
            'access_end_date' => $g('Access End Date') !== '-' ? $g('Access End Date') : '',
            'job_title' => $g('Job Title / Designation') !== '-' ? $g('Job Title / Designation') : '',
            'mvm_roles' => $list($g('MVM Roles')),
            'core_roles' => $list($g('Core 3.0 Roles')),
            'atm_access' => $list($g('ATM Access Level')),
            'msp_coop_code' => $g('MSP Coop Code (MBWIN)') !== '-' ? $g('MSP Coop Code (MBWIN)') : '',
            'msp_username' => $g('MSP User Name (CIC)') !== '-' ? $g('MSP User Name (CIC)') : '',
            'msp_submission_type' => $g('MSP Submission Type') !== '-' ? $g('MSP Submission Type') : '',
            'msp_end_date' => $g('MSP End Date') !== '-' ? $g('MSP End Date') : '',
            'ftp_allowed' => $g('FTP Allowed') !== '-' ? $g('FTP Allowed') : '',
            'ftp_provider_code' => $g('FTP Provider Code (CIC)') !== '-' ? $g('FTP Provider Code (CIC)') : '',
            'ftp_password_cic' => $g('FTP Password (CIC)') !== '-' ? $g('FTP Password (CIC)') : '',
            'ftp_roles' => $list($g('FTP User Roles')),
            'pcdiss_provider_code' => $g('PCDISS Provider Code (CIC)') !== '-' ? $g('PCDISS Provider Code (CIC)') : '',
            'pcdiss_username' => $g('PCDISS Username (CIC)') !== '-' ? $g('PCDISS Username (CIC)') : '',
            'pcdiss_password_cic' => $g('PCDISS Password (CIC)') !== '-' ? $g('PCDISS Password (CIC)') : '',
            'pcdiss_submission_type' => $g('PCDISS Submission Type') !== '-' ? $g('PCDISS Submission Type') : '',
            'pcdiss_roles' => $list($g('PCDISS User Roles')),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function minimalSummaryFromAccessRequest(AccessRequest $accessRequest): array
    {
        $systems = is_array($accessRequest->systems)
            ? implode(', ', array_filter(array_map('strval', $accessRequest->systems)))
            : '';

        return [
            'Request Number' => (string) ($accessRequest->request_number ?: '-'),
            'Request Type' => '-',
            'Full Name' => (string) ($accessRequest->full_name ?? '-'),
            'Cooperative Name' => (string) ($accessRequest->coop_name ?? '-'),
            'Branch' => (string) ($accessRequest->branch ?? '-'),
            'Date of Request' => $accessRequest->request_date?->format('Y-m-d') ?? '-',
            'Mobile No' => (string) ($accessRequest->mobile_no ?? '-'),
            'Address' => '-',
            'Postal Code' => '-',
            'Email Address' => (string) ($accessRequest->email ?? '-'),
            'Place of Birth' => '-',
            'Gender' => '-',
            'Systems Requested' => $systems !== '' ? $systems : '-',
            'Access Type' => '-',
            'Access End Date' => '-',
            'Job Title / Designation' => '-',
        ];
    }

    public function downloadApprovalSigned(Request $request, AccessRequest $accessRequest): StreamedResponse
    {
        $path = (string) ($accessRequest->approval_signed_path ?? '');
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Signed approval file not found.');
        }

        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
        AuditLogger::log(
            $request,
            'form.approval_file.downloaded',
            sprintf('Downloaded signed approval file for request %s.', $ref),
            AccessRequest::class,
            $accessRequest->id,
        );

        return Storage::disk('local')->download($path);
    }

    public function pdfArchive(Request $request): View
    {
        $system = trim((string) $request->query('system', 'all'));
        $status = trim((string) $request->query('status', 'all'));
        $search = trim((string) $request->query('search', ''));

        $systemModules = config('access_request.system_modules', []);

        $sortState = $this->normalizeAccessRequestSort(
            $request,
            ['request_number', 'full_name', 'systems', 'status', 'created_at'],
            'created_at',
            'desc'
        );
        $sort = $sortState['sort'];
        $direction = $sortState['direction'];

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
                    $sub->where('request_number', 'like', '%'.$search.'%')
                        ->orWhere('full_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->tap(function (Builder $q) use ($sort, $direction): void {
                $this->applyAccessRequestSort($q, $sort, $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.pdf-archive', compact('requests', 'systemModules', 'system', 'status', 'search', 'sort', 'direction'));
    }

    public function downloadPdf(Request $request, AccessRequest $accessRequest)
    {
        $ref = (string) ($accessRequest->request_number ?: '#'.$accessRequest->id);
        AuditLogger::log(
            $request,
            'form.pdf.downloaded',
            sprintf('Downloaded PDF backup for request %s.', $ref),
            AccessRequest::class,
            $accessRequest->id,
        );

        $summary = is_array($accessRequest->summary) ? $accessRequest->summary : [];
        $baseName = (string) ($accessRequest->request_number ?: $accessRequest->id ?: 'request');
        $safeBaseName = preg_replace('/[^A-Za-z0-9\-]/', '-', $baseName) ?: 'request';
        $filename = 'access-request-'.$safeBaseName.'.pdf';

        // Regenerate from current template so admin downloads always use latest PDF layout.
        if (! empty($summary)) {
            try {
                /** @var PDF $pdf */
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
