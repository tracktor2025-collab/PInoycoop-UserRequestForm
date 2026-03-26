<?php

namespace App\Http\Controllers;

use App\Jobs\HandleAccessRequestSubmissionJob;
use App\Models\AccessRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Revolution\Google\Sheets\Facades\Sheets;

class UserAccessRequestController extends Controller
{
    private const REQUEST_NUMBER_DAY_MAP = [
        'MON' => 'MON',
        'TUE' => 'TUE',
        'WED' => 'WED',
        'THU' => 'THUR',
        'FRI' => 'FRI',
        'SAT' => 'SAT',
        'SUN' => 'SUN',
    ];

    private function requestNumberPrefix(): string
    {
        $dayKey = strtoupper(now()->format('D')); // Mon, Tue, Wed, Thu, Fri, Sat, Sun
        $dayKey = strtoupper(substr($dayKey, 0, 3)); // normalize to MON/TUE/...
        $mappedDay = self::REQUEST_NUMBER_DAY_MAP[$dayKey] ?? $dayKey;

        return 'REQ-' . $mappedDay . '-';
    }

    private function requestNumberCacheKey(): string
    {
        return 'access_request_counter:' . now()->toDateString();
    }

    /**
     * Count today's already-issued request numbers in Google Sheets (for a given counter tab).
     *
     * This is used to initialize the daily cache counter so numbering stays consistent.
     */
    private function countRequestNumbersTodayInSheets(string $spreadsheetId, string $sheetName): int
    {
        $prefix = $this->requestNumberPrefix();

        $this->ensureSheetTabExists($spreadsheetId, $sheetName);

        try {
            $values = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->range('B:B')
                ->all();
        } catch (\Throwable $e) {
            report($e);
            $values = [];
        }

        $countToday = 0;
        foreach ($values as $row) {
            $cell = is_array($row) ? (string) ($row[0] ?? '') : (string) $row;
            $cell = trim($cell);

            if ($cell === '' || strcasecmp($cell, 'Request Number') === 0) {
                continue;
            }

            if (str_starts_with($cell, $prefix)) {
                $countToday++;
            }
        }

        // If the numbering sheet has no entries yet for today (common on first deploy),
        // fall back to counting rows whose Timestamp date == today across known tabs.
        if ($countToday === 0) {
            $tabs = [];
            $tabs[] = (string) config('google.sheet_name', 'Sheet1');
            $tabs[] = (string) config('google.sheet_other', 'Other Systems');
            $tabs[] = (string) $sheetName;

            foreach ((array) config('google.sheet_by_system', []) as $tab) {
                $tabs[] = (string) $tab;
            }

            $tabs = array_values(array_unique(array_filter(array_map('trim', $tabs))));
            $countToday = $this->countRequestsTodayByTimestamp($spreadsheetId, $tabs);
        }

        return $countToday;
    }

    private function generateReservedRequestNumber(?string $spreadsheetId, string $numberingSheet): string
    {
        $key = $this->requestNumberCacheKey();
        $ttl = now()->endOfDay();

        $shouldInitFromSheets = $spreadsheetId !== null && $spreadsheetId !== '' && class_exists(\Revolution\Google\Sheets\Facades\Sheets::class);

        if (! Cache::has($key) && $shouldInitFromSheets) {
            // Initialize daily counter to the number of already-issued request numbers
            // so the next increment matches the Sheets-generated sequence.
            $initialCount = $this->countRequestNumbersTodayInSheets($spreadsheetId, $numberingSheet);
            Cache::add($key, $initialCount, $ttl);
        }

        // If Sheets init didn't run (or key already exists), ensure the key exists.
        Cache::add($key, 0, $ttl);

        $n = Cache::increment($key);
        if ($n === null || $n < 1) {
            // Defensive fallback: if increment behaves unexpectedly, restart the counter.
            Cache::put($key, 1, $ttl);
            $n = 1;
        }

        return $this->requestNumberPrefix() . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }

    private function getOrReserveRequestNumberPreview(Request $request): string
    {
        $existing = $request->session()->get('request_number_preview');
        if (is_string($existing) && trim($existing) !== '') {
            return $existing;
        }

        $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID');
        $numberingSheet = (string) config('google.request_number_sheet', config('google.sheet_name', 'Sheet1'));

        $requestNumber = $this->generateReservedRequestNumber($spreadsheetId ?: null, $numberingSheet);
        $request->session()->put('request_number_preview', $requestNumber);

        return $requestNumber;
    }

    /**
     * Google Sheets API requires quoting sheet titles containing spaces/special characters.
     */
    private function apiSheetTitle(string $title): string
    {
        $escaped = str_replace("'", "''", trim($title));
        return "'" . $escaped . "'";
    }

    private function columnLetter(int $index): string
    {
        // 1 => A, 2 => B ... 26 => Z, 27 => AA, etc.
        $index = max(1, $index);
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr(($index % 26) + 65) . $letters;
            $index = intdiv($index, 26);
        }
        return $letters;
    }

    /**
     * Generate a unique request number using the format: REQ-DAY-XXX.
     *
     * Key logic:
     * - DAY uses a custom mapping (THU => THUR).
     * - XXX increments based on how many request numbers already exist for *today* in Google Sheets.
     * - Counter resets daily because we only count rows for the current day prefix.
     */
    private function generateRequestNumber(string $spreadsheetId, string $sheetName): string
    {
        $dayKey = strtoupper(now()->format('D')); // Mon, Tue, Wed, Thu, Fri, Sat, Sun
        $dayKey = strtoupper(substr($dayKey, 0, 3)); // normalize to MON/TUE/...
        $mappedDay = self::REQUEST_NUMBER_DAY_MAP[$dayKey] ?? $dayKey;

        $prefix = 'REQ-' . $mappedDay . '-';

        // We store the generated number in column B ("Request Number").
        // Reading a single column is cheap and avoids needing to detect header indices.
        $this->ensureSheetTabExists($spreadsheetId, $sheetName);

        try {
            $values = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->range('B:B')
                ->all();
        } catch (\Throwable $e) {
            report($e);
            $values = [];
        }

        $countToday = 0;
        foreach ($values as $row) {
            $cell = is_array($row) ? (string) ($row[0] ?? '') : (string) $row;
            $cell = trim($cell);

            if ($cell === '' || strcasecmp($cell, 'Request Number') === 0) {
                continue;
            }

            if (str_starts_with($cell, $prefix)) {
                $countToday++;
            }
        }

        // If the numbering sheet has no entries yet for today (common on first deploy),
        // fall back to counting rows whose Timestamp date == today across known tabs.
        if ($countToday === 0) {
            $tabs = [];
            $tabs[] = (string) config('google.sheet_name', 'Sheet1');
            $tabs[] = (string) config('google.sheet_other', 'Other Systems');
            $tabs[] = (string) $sheetName;

            foreach ((array) config('google.sheet_by_system', []) as $tab) {
                $tabs[] = (string) $tab;
            }

            $tabs = array_values(array_unique(array_filter(array_map('trim', $tabs))));
            $countToday = $this->countRequestsTodayByTimestamp($spreadsheetId, $tabs);
        }

        return $prefix . str_pad((string) ($countToday + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Cache-backed fallback request number generator (REQ-DAY-XXX).
     * Used when Google Sheets is not configured/available.
     */
    private function generateRequestNumberFallback(): string
    {
        $dayKey = strtoupper(substr(now()->format('D'), 0, 3));
        $mappedDay = self::REQUEST_NUMBER_DAY_MAP[$dayKey] ?? $dayKey;
        $prefix = 'REQ-' . $mappedDay . '-';

        $key = 'access_request_counter:' . now()->toDateString();
        $n = Cache::increment($key);

        if ($n === 1) {
            // Ensure it resets daily even with file/database cache.
            Cache::put($key, 1, now()->endOfDay());
        }

        return $prefix . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Counts requests where the Timestamp date equals today across the given sheet tabs.
     * This is used as a fallback for request-number sequencing when older rows do not yet
     * have a Request Number column.
     */
    private function countRequestsTodayByTimestamp(string $spreadsheetId, array $sheetNames): int
    {
        $today = now()->toDateString();
        $count = 0;

        foreach ($sheetNames as $sheetName) {
            try {
                $values = Sheets::spreadsheet($spreadsheetId)
                    ->sheet($this->apiSheetTitle($sheetName))
                    ->range('A:A')
                    ->all();
            } catch (\Throwable $e) {
                report($e);
                continue;
            }

            foreach ($values as $row) {
                $cell = is_array($row) ? (string) ($row[0] ?? '') : (string) $row;
                $cell = trim($cell);

                if ($cell === '' || strcasecmp($cell, 'Timestamp') === 0) {
                    continue;
                }

                try {
                    $date = \Illuminate\Support\Carbon::parse($cell)->toDateString();
                } catch (\Throwable) {
                    continue;
                }

                if ($date === $today) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function ensureSheetTabExists(string $spreadsheetId, string $sheetTitle): void
    {
        $service = Sheets::spreadsheet($spreadsheetId)->getService();
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);

        $existingTitles = [];
        foreach (($spreadsheet->getSheets() ?? []) as $sheet) {
            $existingTitles[] = (string) data_get($sheet, 'properties.title', '');
        }

        if (in_array($sheetTitle, $existingTitles, true)) {
            return;
        }

        $req = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
            'requests' => [
                [
                    'addSheet' => [
                        'properties' => [
                            'title' => $sheetTitle,
                        ],
                    ],
                ],
            ],
        ]);

        $service->spreadsheets->batchUpdate($spreadsheetId, $req);
    }

    /**
     * Ensures the header exists on the target sheet, then appends the row.
     */
    private function appendRowToSheet(string $spreadsheetId, string $sheetName, array $headerRow, array $row): void
    {
        $this->ensureSheetTabExists($spreadsheetId, $sheetName);

        $endCol = $this->columnLetter(\count($headerRow));
        $firstRow = Sheets::spreadsheet($spreadsheetId)
            ->sheet($this->apiSheetTitle($sheetName))
            ->range('A1:' . $endCol . '1')
            ->first();

        // Compare the full header row to avoid column "shifting" when the sheet already exists
        // but has a different header layout.
        $existingHeader = \array_map(
            static fn ($cell) => trim((string) $cell),
            $firstRow ?? []
        );
        $expectedHeader = \array_map(
            static fn ($cell) => trim((string) $cell),
            $headerRow
        );

        $hasHeaders = $existingHeader === $expectedHeader;

        if (! $hasHeaders) {
            $sheetProps = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->sheetProperties();

            $sheetId = (int) ($sheetProps->sheetId ?? 0);
            if ($sheetId > 0) {
                $service = Sheets::spreadsheet($spreadsheetId)->getService();

                $body = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                    'requests' => [
                        [
                            'insertDimension' => [
                                'range' => [
                                    'sheetId' => $sheetId,
                                    'dimension' => 'ROWS',
                                    'startIndex' => 0,
                                    'endIndex' => 1,
                                ],
                                'inheritFromBefore' => false,
                            ],
                        ],
                    ],
                ]);
                $service->spreadsheets->batchUpdate($spreadsheetId, $body);
            }

            Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->range('A1')
                ->update([$headerRow]);
        }

        Sheets::spreadsheet($spreadsheetId)
            ->sheet($this->apiSheetTitle($sheetName))
            ->append([$row], 'RAW', 'INSERT_ROWS');

        $sheetProps = Sheets::spreadsheet($spreadsheetId)
            ->sheet($this->apiSheetTitle($sheetName))
            ->sheetProperties();

        $sheetId = (int) ($sheetProps->sheetId ?? 0);
        $startIndex = 0;
        $endIndex = max(1, \count($headerRow));

        if ($sheetId > 0) {
            $service = Sheets::spreadsheet($spreadsheetId)->getService();

            $body = new \Google\Service\Sheets\BatchUpdateSpreadsheetRequest([
                'requests' => [
                    [
                        'autoResizeDimensions' => [
                            'dimensions' => [
                                'sheetId' => $sheetId,
                                'dimension' => 'COLUMNS',
                                'startIndex' => $startIndex,
                                'endIndex' => $endIndex,
                            ],
                        ],
                    ],
                ],
            ]);

            $service->spreadsheets->batchUpdate($spreadsheetId, $body);
        }
    }

    private function buildSheetPayloadForSystem(string $system, Request $request, array $validated): array
    {
        $requestType = is_array($request->request_type) ? implode(', ', $request->request_type) : '';
        $systemsRequested = is_array($request->systems) ? implode(', ', $request->systems) : '';
        $systems = is_array($request->systems) ? array_values(array_filter($request->systems)) : [];
        $mvmRoles = is_array($request->mvm_roles) ? implode(', ', $request->mvm_roles) : '';
        $atmAccess = is_array($request->atm_access) ? implode(', ', $request->atm_access) : '';
        $coreRoles = is_array($request->core_roles) ? implode(', ', $request->core_roles) : '';
        $ftpRoles = is_array($request->ftp_roles) ? implode(', ', $request->ftp_roles) : '';

        $commonHeader = [
            'Timestamp',
            'Request Number',
            'Request Type',
            'Full Name',
            'Request Date',
            'Mobile No',
            'Coop Name Branch',
            'Postal Code',
            'Gender',
            'Address',
            'Email Address',
            'Place Of Birth',
            'Systems Requested',
            'Access Type',
            'Access End Date',
            'Job Title',
        ];

        $commonRow = [
            now()->toDateTimeString(),
            $validated['request_number'] ?? '',
            $requestType,
            $validated['full_name'] ?? '',
            $validated['request_date'] ?? '',
            $validated['mobile_no'] ?? '',
            $validated['coop_name_branch'] ?? '',
            $validated['postal_code'] ?? '',
            $validated['gender'] ?? '',
            $validated['address'] ?? '',
            $validated['email'] ?? '',
            $validated['place_of_birth'] ?? '',
            $systemsRequested,
            $validated['access_type'] ?? '',
            $validated['access_end_date'] ?? '',
            $validated['job_title'] ?? '',
        ];

        $system = trim($system);

        if ($system === 'CORE 3.0') {
            // Core 3.0 must use the same "common" columns as the other tabs; otherwise
            // appended rows will land in the wrong cells when the sheet already has headers.
            return [
                'header' => \array_merge($commonHeader, ['Core Roles', 'Status']),
                'row' => \array_merge($commonRow, [$coreRoles, 'Pending']),
            ];
        }

        if ($system === 'ATM Portal') {
            return [
                'header' => array_merge($commonHeader, ['ATM Access', 'Status']),
                'row' => array_merge($commonRow, [$atmAccess, 'Pending']),
            ];
        }

        if ($system === 'MVM Portal') {
            return [
                'header' => array_merge($commonHeader, ['MVM Roles', 'Status']),
                'row' => array_merge($commonRow, [$mvmRoles, 'Pending']),
            ];
        }

        if ($system === 'MSP-ISS Portal') {
            return [
                'header' => array_merge($commonHeader, [
                    'MSP Coop Code',
                    'MSP Username',
                    'MSP Submission Type',
                    'MSP End Date',
                    'Provider Code (CIC)',
                    'Password (CIC)',
                    'User Role',
                    'Status',
                ]),
                'row' => array_merge($commonRow, [
                    $validated['msp_coop_code'] ?? '',
                    $validated['msp_username'] ?? '',
                    $validated['msp_submission_type'] ?? '',
                    $validated['msp_end_date'] ?? '',
                    $validated['ftp_provider_code'] ?? '',
                    $validated['ftp_password_cic'] ?? '',
                    $ftpRoles,
                    'Pending',
                ]),
            ];
        }

        if ($system === 'MSP-ISS FTP') {
            return [
                'header' => array_merge($commonHeader, [
                    'FTP Allowed',
                    'Provider Code (CIC)',
                    'Password (CIC)',
                    'FTP Roles',
                    'Status',
                ]),
                'row' => array_merge($commonRow, [
                    $validated['ftp_allowed'] ?? '',
                    $validated['ftp_provider_code'] ?? '',
                    $validated['ftp_password_cic'] ?? '',
                    $ftpRoles,
                    'Pending',
                ]),
            ];
        }

        // Systems without dedicated detail blocks (Helpdesk, PASS, SMS Portal, etc.)
        return [
            'header' => array_merge($commonHeader, ['Status']),
            'row' => array_merge($commonRow, ['Pending']),
        ];
    }

    public function landing(): View
    {
        return view('landing');
    }

    public function form(): View
    {
        /** @var \Illuminate\Http\Request $request */
        $request = request();
        $requestNumberPreview = $this->getOrReserveRequestNumberPreview($request);

        return view('user-request-form', [
            'requestNumberPreview' => $requestNumberPreview,
        ]);
    }

    public function verifyCaptcha(Request $request): RedirectResponse
    {
        $token = (string) $request->input('g-recaptcha-response', '');

        if ($token === '') {
            return redirect()->route('landing')->with('error', 'Please complete the CAPTCHA to continue.');
        }

        $secret = (string) config('services.recaptcha.secret_key', '');
        if ($secret === '') {
            return redirect()->route('landing')->with('error', 'CAPTCHA is not configured (missing secret key).');
        }

        try {
            $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $data = $resp->json();
            $ok = (bool) data_get($data, 'success', false);
        } catch (\Throwable $e) {
            report($e);
            $ok = false;
        }

        if (! $ok) {
            return redirect()->route('landing')->with('error', 'CAPTCHA verification failed. Please try again.');
        }

        $request->session()->put('landing_captcha_verified', true);

        return redirect()->route('request.form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'request_type' => ['nullable', 'array'],
            'request_type.*' => ['nullable', 'string', 'in:New,Update,Removal'],
            'resource_request_number' => ['nullable', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'request_date' => ['required', 'date'],
            // PH mobile numbers are 11 digits (e.g. 09XXXXXXXXX)
            'mobile_no' => ['required', 'digits:11'],
            'coop_name_branch' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'systems' => ['nullable', 'array'],
            'systems.*' => ['nullable', 'string', 'max:255'],
            'access_type' => ['required', 'string', 'in:Permanent,Temporary'],
            'access_end_date' => ['nullable', 'date'],
            'job_title' => ['required', 'string', 'max:255'],
            'mvm_roles' => ['nullable', 'array'],
            'mvm_roles.*' => ['nullable', 'string', 'max:255'],
            'atm_access' => ['nullable', 'array'],
            'atm_access.*' => ['nullable', 'string', 'max:255'],
            'msp_coop_code' => ['nullable', 'string', 'max:255'],
            'msp_username' => ['nullable', 'string', 'max:255'],
            'msp_submission_type' => ['nullable', 'string', 'in:Test,Production'],
            'msp_end_date' => ['nullable', 'date'],
            'core_roles' => ['nullable', 'array'],
            'core_roles.*' => ['nullable', 'string', 'max:255'],
            'ftp_allowed' => ['nullable', 'string', 'in:Yes,No'],
            'ftp_provider_code' => ['nullable', 'string', 'max:255'],
            'ftp_password_cic' => ['nullable', 'string', 'max:255'],
            'ftp_roles' => ['nullable', 'array'],
            'ftp_roles.*' => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['access_type'] ?? null) === 'Temporary' && empty($validated['access_end_date'])) {
            return redirect()
                ->route('request.form')
                ->withInput()
                ->with('error', 'End Date is required for Temporary access.');
        }

        $requestType = is_array($request->request_type) ? implode(', ', $request->request_type) : '';
        $systemsRequested = is_array($request->systems) ? implode(', ', $request->systems) : '';
        $systems = is_array($request->systems) ? array_values(array_filter($request->systems)) : [];
        $mvmRoles = is_array($request->mvm_roles) ? implode(', ', $request->mvm_roles) : '';
        $atmAccess = is_array($request->atm_access) ? implode(', ', $request->atm_access) : '';
        $coreRoles = is_array($request->core_roles) ? implode(', ', $request->core_roles) : '';
        $ftpRoles = is_array($request->ftp_roles) ? implode(', ', $request->ftp_roles) : '';

        // Use the reserved preview number when available, otherwise try input/session,
        // and only then generate a new one.
        $sessionPreview = $request->session()->get('request_number_preview');
        $fromInput = isset($validated['resource_request_number']) && is_string($validated['resource_request_number'])
            ? trim($validated['resource_request_number'])
            : '';
        $validated['request_number'] = (is_string($sessionPreview) && trim($sessionPreview) !== '')
            ? $sessionPreview
            : ($fromInput !== '' ? $fromInput : '');

        if ($validated['request_number'] === '') {
            $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID');
            $numberingSheet = (string) config('google.request_number_sheet', config('google.sheet_name', 'Sheet1'));
            $validated['request_number'] = $this->generateReservedRequestNumber($spreadsheetId ?: null, $numberingSheet);
        }

        // Data for on-screen / printable summary.
        $summary = [
            'Request Number' => $validated['request_number'],
            'Request Type' => $requestType ?: '-',
            'Full Name' => $validated['full_name'] ?? '-',
            'Coop Name & Branch' => $validated['coop_name_branch'] ?? '-',
            'Date of Request' => $validated['request_date'] ?? '-',
            'Mobile No' => $validated['mobile_no'] ?? '-',
            'Address' => $validated['address'] ?? '-',
            'Postal Code' => $validated['postal_code'] ?? '-',
            'Email Address' => $validated['email'] ?? '-',
            'Place of Birth' => $validated['place_of_birth'] ?? '-',
            'Gender' => $validated['gender'] ?? '-',
            'Systems Requested' => $systemsRequested ?: '-',
            'Access Type' => $validated['access_type'] ?? '-',
            'Access End Date' => $validated['access_end_date'] ?? '-',
            'Job Title / Designation' => $validated['job_title'] ?? '-',
            'MVM Roles' => $mvmRoles ?: '-',
            'ATM Access Level' => $atmAccess ?: '-',
            'MSP Coop Code (MBWIN)' => $validated['msp_coop_code'] ?? '-',
            'MSP User Name (CIC)' => $validated['msp_username'] ?? '-',
            'MSP Submission Type' => $validated['msp_submission_type'] ?? '-',
            'MSP End Date' => $validated['msp_end_date'] ?? '-',
            'Core 3.0 Roles' => $coreRoles ?: '-',
            'FTP Allowed' => $validated['ftp_allowed'] ?? '-',
            'FTP Provider Code (CIC)' => $validated['ftp_provider_code'] ?? '-',
            'FTP Password (CIC)' => $validated['ftp_password_cic'] ?? '-',
            'FTP User Roles' => $ftpRoles ?: '-',
        ];

        // Create DB record first so the user can be redirected quickly.
        try {
            AccessRequest::create([
                'request_number' => $validated['request_number'] ?? null,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'mobile_no' => $validated['mobile_no'] ?? null,
                'coop_name_branch' => $validated['coop_name_branch'] ?? null,
                'request_date' => $validated['request_date'] ?? null,
                'status' => 'pending',
                'systems' => $systems,
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        // Clear the reserved preview immediately; slow Google Sheets work runs in the background.
        $request->session()->forget('request_number_preview');

        // Queue Google Sheets + email work.
        $submittedAt = now()->toDateTimeString();
        dispatch(new HandleAccessRequestSubmissionJob($validated, $submittedAt));

        $request->session()->put('request_summary', $summary);

        return redirect()->route('success');
    }

    public function success(): View
    {
        $summary = session('request_summary');
        // Re-store in session so Download PDF still has it (flash data is consumed after one request)
        if (is_array($summary) && ! empty($summary)) {
            session()->put('request_summary', $summary);
        }
        return view('success', [
            'summary' => $summary,
        ]);
    }

    public function successPdf(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $summary = session('request_summary');

        if (!is_array($summary) || empty($summary)) {
            return redirect()->route('success');
        }

        // Use DomPDF (barryvdh/laravel-dompdf) to generate a real downloadable PDF.
        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('success-pdf', [
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        $filename = 'user-access-request-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}
