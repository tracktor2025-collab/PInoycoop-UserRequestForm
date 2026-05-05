<?php

namespace App\Services;

use App\Models\AccessRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Support\Facades\Cache;
use Revolution\Google\Sheets\Facades\Sheets;

class AccessRequestGoogleSheetsService
{
    private function apiSheetTitle(string $title): string
    {
        $escaped = str_replace("'", "''", trim($title));

        return "'".$escaped."'";
    }

    private function columnLetter(int $index): string
    {
        // 1 => A, 2 => B ... 26 => Z, 27 => AA, etc.
        $index = max(1, $index);
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr(($index % 26) + 65).$letters;
            $index = intdiv($index, 26);
        }

        return $letters;
    }

    private function ensureSheetTabExists(string $spreadsheetId, string $sheetTitle): void
    {
        // Avoid expensive spreadsheets->get calls on every submission.
        $cacheKey = 'google:sheets:tab_exists:'.md5($spreadsheetId.':'.$sheetTitle);
        if (Cache::has($cacheKey)) {
            return;
        }

        $service = Sheets::spreadsheet($spreadsheetId)->getService();
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);

        $existingTitles = [];
        foreach (($spreadsheet->getSheets() ?? []) as $sheet) {
            $existingTitles[] = (string) data_get($sheet, 'properties.title', '');
        }

        if (in_array($sheetTitle, $existingTitles, true)) {
            Cache::put($cacheKey, true, now()->addHours(6));

            return;
        }

        $req = new BatchUpdateSpreadsheetRequest([
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
        Cache::put($cacheKey, true, now()->addHours(6));
    }

    /**
     * Ensures the header exists on the target sheet, then appends the row.
     *
     * Note: we cache successful header checks to reduce calls on every submission.
     */
    private function appendRowToSheet(string $spreadsheetId, string $sheetName, array $headerRow, array $row): void
    {
        $this->ensureSheetTabExists($spreadsheetId, $sheetName);

        $headerHash = sha1(json_encode($headerRow, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $headerOkCacheKey = 'google:sheets:header_ok:'.md5($spreadsheetId.':'.$sheetName.':'.$headerHash);

        $headerOk = Cache::get($headerOkCacheKey, false) === true;
        $shouldAutoResize = false;

        if (! $headerOk) {
            $endCol = $this->columnLetter(\count($headerRow));
            $firstRow = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->range('A1:'.$endCol.'1')
                ->first();

            $existingHeader = \array_map(
                static fn ($cell) => trim((string) $cell),
                $firstRow ?? []
            );

            $expectedHeader = \array_map(
                static fn ($cell) => trim((string) $cell),
                $headerRow
            );

            if ($existingHeader !== $expectedHeader) {
                $normalizedExisting = array_map(
                    static fn ($cell) => mb_strtolower(trim((string) $cell)),
                    $existingHeader
                );
                $normalizedExpected = array_map(
                    static fn ($cell) => mb_strtolower(trim((string) $cell)),
                    $expectedHeader
                );

                $canUpdateHeaderInPlace = $existingHeader === []
                    || $normalizedExisting === array_slice($normalizedExpected, 0, \count($normalizedExisting));

                if ($canUpdateHeaderInPlace) {
                    Sheets::spreadsheet($spreadsheetId)
                        ->sheet($this->apiSheetTitle($sheetName))
                        ->range('A1')
                        ->update([$headerRow]);

                    $shouldAutoResize = true;
                } else {
                    $sheetProps = Sheets::spreadsheet($spreadsheetId)
                        ->sheet($this->apiSheetTitle($sheetName))
                        ->sheetProperties();

                    $sheetId = (int) ($sheetProps->sheetId ?? 0);
                    if ($sheetId > 0) {
                        $service = Sheets::spreadsheet($spreadsheetId)->getService();

                        $body = new BatchUpdateSpreadsheetRequest([
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

                    $shouldAutoResize = true;
                }
            }

            Cache::put($headerOkCacheKey, true, now()->addHours(12));
        }

        Sheets::spreadsheet($spreadsheetId)
            ->sheet($this->apiSheetTitle($sheetName))
            ->append([$row], 'RAW', 'INSERT_ROWS');

        // Auto-resize is expensive; only do it when we changed header layout.
        if ($shouldAutoResize) {
            $sheetProps = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->sheetProperties();

            $sheetId = (int) ($sheetProps->sheetId ?? 0);
            $startIndex = 0;
            $endIndex = max(1, \count($headerRow));

            if ($sheetId > 0) {
                $service = Sheets::spreadsheet($spreadsheetId)->getService();
                $body = new BatchUpdateSpreadsheetRequest([
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
    }

    /**
     * @return array<int, string>
     */
    private function approvalMetadataHeaders(): array
    {
        $headerConfig = (array) config('google.sheet_sync_approval_headers', []);

        return [
            (string) (($headerConfig['approved_by'][0] ?? null) ?: 'Approved By'),
            (string) (($headerConfig['approved_at'][0] ?? null) ?: 'Approved At'),
            (string) (($headerConfig['approval_remarks'][0] ?? null) ?: 'Approval Remarks'),
        ];
    }

    /**
     * @return array{header: array<int, string>, row: array<int, mixed>}
     */
    private function approvalMetadataValues(?AccessRequest $accessRequest = null): array
    {
        if ($accessRequest === null) {
            return ['', '', ''];
        }

        $approvedAt = $accessRequest->approved_at;

        return [
            (string) ($accessRequest->approved_by ?? ''),
            $approvedAt === null
                ? ''
                : $approvedAt->copy()->timezone((string) config('app.timezone', 'UTC'))->format('Y-m-d H:i'),
            (string) ($accessRequest->approval_remarks ?? ''),
        ];
    }

    /**
     * @param  array<int, mixed>|null  $approvalValues
     * @return array{header: array<int, string>, row: array<int, mixed>}
     */
    private function withStatusAndApprovalMetadata(array $header, array $row, string $statusCell, ?array $approvalValues = null): array
    {
        return [
            'header' => array_merge($header, ['Status'], $this->approvalMetadataHeaders()),
            'row' => array_merge($row, [$statusCell], $approvalValues ?? $this->approvalMetadataValues()),
        ];
    }

    private function buildSheetPayloadForSystem(
        string $system,
        array $validated,
        string $timestamp,
        ?string $statusLabel = null,
        ?array $approvalValues = null,
    ): array {
        $statusCell = $statusLabel ?? 'Pending';

        $requestType = is_array($validated['request_type'] ?? null)
            ? implode(', ', $validated['request_type'])
            : '';

        $systems = is_array($validated['systems'] ?? null) ? array_values(array_filter($validated['systems'])) : [];
        $systemsRequested = is_array($validated['systems'] ?? null)
            ? implode(', ', $validated['systems'])
            : '';

        $mvmRoles = is_array($validated['mvm_roles'] ?? null) ? implode(', ', $validated['mvm_roles']) : '';
        $atmAccess = is_array($validated['atm_access'] ?? null) ? implode(', ', $validated['atm_access']) : '';
        $coreRoles = is_array($validated['core_roles'] ?? null) ? implode(', ', $validated['core_roles']) : '';
        $ftpRoles = is_array($validated['ftp_roles'] ?? null) ? implode(', ', $validated['ftp_roles']) : '';
        $pcdissRoles = is_array($validated['pcdiss_roles'] ?? null) ? implode(', ', $validated['pcdiss_roles']) : '';

        $commonHeader = [
            'Timestamp',
            'Request Number',
            'Request Type',
            'Full Name',
            'Request Date',
            'Mobile No',
            'Coop Name',
            'Branch',
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
            $timestamp,
            $validated['request_number'] ?? '',
            $requestType,
            $validated['full_name'] ?? '',
            $validated['request_date'] ?? '',
            $validated['mobile_no'] ?? '',
            $validated['coop_name'] ?? '',
            $validated['branch'] ?? '',
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
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, ['Core Roles']),
                array_merge($commonRow, [$coreRoles]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'ATM Portal') {
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, ['ATM Access']),
                array_merge($commonRow, [$atmAccess]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'MVM Portal') {
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, ['MVM Roles']),
                array_merge($commonRow, [$mvmRoles]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'MSP-ISS Portal') {
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, [
                    'MSP Coop Code',
                    'MSP Username',
                    'MSP Submission Type',
                    'MSP End Date',
                    'Provider Code (CIC)',
                    'Password (CIC)',
                    'User Role',
                ]),
                array_merge($commonRow, [
                    $validated['msp_coop_code'] ?? '',
                    $validated['msp_username'] ?? '',
                    $validated['msp_submission_type'] ?? '',
                    $validated['msp_end_date'] ?? '',
                    $validated['ftp_provider_code'] ?? '',
                    $validated['ftp_password_cic'] ?? '',
                    $ftpRoles,
                ]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'MSP-ISS FTP') {
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, [
                    'FTP Allowed',
                    'Provider Code (CIC)',
                    'Password (CIC)',
                    'FTP Roles',
                ]),
                array_merge($commonRow, [
                    $validated['ftp_allowed'] ?? '',
                    $validated['ftp_provider_code'] ?? '',
                    $validated['ftp_password_cic'] ?? '',
                    $ftpRoles,
                ]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'PCDISS') {
            return $this->withStatusAndApprovalMetadata(
                array_merge($commonHeader, [
                    'Provider Code (CIC)',
                    'Username (CIC)',
                    'Password (CIC)',
                    'PCDISS Submission Type',
                    'PCDISS Roles',
                ]),
                array_merge($commonRow, [
                    $validated['pcdiss_provider_code'] ?? '',
                    $validated['pcdiss_username'] ?? '',
                    $validated['pcdiss_password_cic'] ?? '',
                    $validated['pcdiss_submission_type'] ?? '',
                    $pcdissRoles,
                ]),
                $statusCell,
                $approvalValues
            );
        }

        if ($system === 'SSL VPN') {
            return $this->withStatusAndApprovalMetadata($commonHeader, $commonRow, $statusCell, $approvalValues);
        }

        // Systems without dedicated detail blocks (Helpdesk, PASS, SMS Portal, etc.)
        return $this->withStatusAndApprovalMetadata($commonHeader, $commonRow, $statusCell, $approvalValues);
    }

    public function submitToSheets(array $validated, string $timestamp): void
    {
        $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', '');
        if ($spreadsheetId === '' || ! class_exists(Sheets::class)) {
            return;
        }

        $sheetBySystem = (array) config('google.sheet_by_system', []);
        $otherSheet = (string) config('google.sheet_other', config('google.sheet_name', 'Sheet1'));
        $defaultSheet = (string) config('google.sheet_name', 'Sheet1');
        $numberingSheet = (string) config('google.request_number_sheet', $defaultSheet);

        $systems = is_array($validated['systems'] ?? null) ? array_values(array_filter($validated['systems'])) : [];

        $requestNumber = (string) ($validated['request_number'] ?? '');
        if ($requestNumber === '') {
            return;
        }

        // Always log the reserved request number in the dedicated counter sheet
        $this->appendRowToSheet(
            (string) $spreadsheetId,
            (string) $numberingSheet,
            ['Timestamp', 'Request Number'],
            [$timestamp, $requestNumber]
        );

        $sheetToSystems = [];
        foreach ($systems as $sys) {
            $sheet = (string) ($sheetBySystem[$sys] ?? $otherSheet);
            $sheetToSystems[$sheet] ??= [];
            $sheetToSystems[$sheet][] = (string) $sys;
        }

        if (empty($sheetToSystems)) {
            $sheetToSystems[$defaultSheet] = [''];
        }

        foreach ($sheetToSystems as $sheetName => $sysList) {
            $mainSystem = (string) ($sysList[0] ?? '');
            $payload = $this->buildSheetPayloadForSystem($mainSystem, $validated, $timestamp);
            $this->appendRowToSheet((string) $spreadsheetId, (string) $sheetName, $payload['header'], $payload['row']);
        }
    }

    /**
     * Merge payload columns into an existing sheet row (matched header names, case-insensitive).
     *
     * @param  array<int, string>  $sheetHeader
     * @param  array<int, mixed>  $existingRow
     * @param  array<int, string>  $payloadHeader
     * @param  array<int, mixed>  $payloadRow
     * @return array<int, string>
     */
    private function mergeRowFromPayload(array $sheetHeader, array $existingRow, array $payloadHeader, array $payloadRow): array
    {
        $width = \count($sheetHeader);
        $out = [];
        for ($i = 0; $i < $width; $i++) {
            $out[$i] = trim((string) ($existingRow[$i] ?? ''));
        }

        $indexByName = [];
        foreach ($sheetHeader as $i => $name) {
            $key = mb_strtolower(trim((string) $name));
            if ($key !== '') {
                $indexByName[$key] = $i;
            }
        }

        foreach ($payloadHeader as $pi => $colName) {
            $key = mb_strtolower(trim((string) $colName));
            if ($key === '' || ! isset($indexByName[$key])) {
                continue;
            }
            $idx = $indexByName[$key];
            $out[$idx] = trim((string) ($payloadRow[$pi] ?? ''));
        }

        return $out;
    }

    /**
     * Update existing rows for this request number on relevant tabs (admin form edit).
     */
    public function updateRequestRows(AccessRequest $accessRequest, array $validated, string $timestamp): void
    {
        $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', '');
        if ($spreadsheetId === '' || ! class_exists(Sheets::class)) {
            return;
        }

        $requestNumber = trim((string) ($accessRequest->request_number ?? ''));
        if ($requestNumber === '') {
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

        if ($tabs === []) {
            $tabs = [$defaultSheet, $otherSheet];
        }

        $tabs = array_values(array_unique(array_filter(array_map('trim', $tabs))));

        $statusLabel = ucfirst((string) $accessRequest->status);

        foreach ($tabs as $sheetName) {
            $mainSystem = '';
            foreach ($systems as $sys) {
                $sys = trim((string) $sys);
                if ($sys === '') {
                    continue;
                }
                $mapped = (string) ($sheetBySystem[$sys] ?? $otherSheet);
                if ($mapped === $sheetName) {
                    $mainSystem = $sys;
                    break;
                }
            }
            if ($mainSystem === '') {
                $mainSystem = (string) ($systems[0] ?? '');
            }

            $payload = $this->buildSheetPayloadForSystem(
                $mainSystem,
                $validated,
                $timestamp,
                $statusLabel,
                $this->approvalMetadataValues($accessRequest)
            );

            try {
                $quotedSheet = $this->apiSheetTitle($sheetName);
                $rows = Sheets::spreadsheet((string) $spreadsheetId)
                    ->sheet($quotedSheet)
                    ->range('A1:AZ5000')
                    ->all();
            } catch (\Throwable $e) {
                report($e);

                continue;
            }

            if ($rows === [] || ! is_array($rows[0] ?? null)) {
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

            for ($i = 1; $i < \count($rows); $i++) {
                $row = is_array($rows[$i] ?? null) ? $rows[$i] : [];
                $sheetRequestNumber = trim((string) ($row[$requestNumberCol] ?? ''));

                if ($sheetRequestNumber !== $requestNumber) {
                    continue;
                }

                $rowNumber = $i + 1;
                $merged = $this->mergeRowFromPayload($header, $row, $payload['header'], $payload['row']);
                $endCol = $this->columnLetter(max(1, \count($header)));
                $range = 'A'.$rowNumber.':'.$endCol.$rowNumber;

                try {
                    Sheets::spreadsheet((string) $spreadsheetId)
                        ->sheet($quotedSheet)
                        ->range($range)
                        ->update([$merged]);
                } catch (\Throwable $e) {
                    report($e);
                }

                break;
            }
        }
    }

    /**
     * Delete all rows for this request number on every relevant tab (including the request-number log sheet).
     * Call before removing the {@see AccessRequest} from the database.
     */
    public function deleteRequestRows(AccessRequest $accessRequest): void
    {
        $spreadsheetId = (string) (config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', ''));
        $requestNumber = trim((string) ($accessRequest->request_number ?? ''));

        if ($spreadsheetId === '' || $requestNumber === '' || ! class_exists(Sheets::class)) {
            return;
        }

        $sheetBySystem = (array) config('google.sheet_by_system', []);
        $otherSheet = (string) config('google.sheet_other', config('google.sheet_name', 'Sheet1'));
        $defaultSheet = (string) config('google.sheet_name', 'Sheet1');
        $numberingSheet = (string) config('google.request_number_sheet', $defaultSheet);

        $systems = is_array($accessRequest->systems) ? $accessRequest->systems : [];
        $tabs = [];
        foreach ($systems as $sys) {
            $sys = trim((string) $sys);
            if ($sys === '') {
                continue;
            }
            $tabs[] = (string) ($sheetBySystem[$sys] ?? $otherSheet);
        }

        if ($tabs === []) {
            $tabs = [$defaultSheet, $otherSheet];
        }

        $tabs[] = $numberingSheet;
        $tabs = array_values(array_unique(array_filter(array_map('trim', $tabs))));

        foreach ($tabs as $sheetName) {
            try {
                $this->deleteMatchingRowsInSheet($spreadsheetId, $sheetName, $requestNumber);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Deletes every data row where column "Request Number" equals {@see $requestNumber}.
     * Rows are removed from highest index first so indices stay valid within one batch update.
     */
    private function deleteMatchingRowsInSheet(string $spreadsheetId, string $sheetName, string $requestNumber): void
    {
        $quotedSheet = $this->apiSheetTitle($sheetName);

        try {
            $rows = Sheets::spreadsheet($spreadsheetId)
                ->sheet($quotedSheet)
                ->range('A1:AZ5000')
                ->all();
        } catch (\Throwable $e) {
            report($e);

            return;
        }

        if ($rows === [] || ! is_array($rows[0] ?? null)) {
            return;
        }

        $header = array_map(
            static fn ($cell) => trim((string) $cell),
            $rows[0]
        );

        $requestNumberCol = array_search('Request Number', $header, true);
        if ($requestNumberCol === false) {
            return;
        }

        $toDelete = [];
        for ($i = 1; $i < \count($rows); $i++) {
            $row = is_array($rows[$i] ?? null) ? $rows[$i] : [];
            if (trim((string) ($row[$requestNumberCol] ?? '')) !== $requestNumber) {
                continue;
            }
            $toDelete[] = $i;
        }

        if ($toDelete === []) {
            return;
        }

        rsort($toDelete, SORT_NUMERIC);

        $sheetProps = Sheets::spreadsheet($spreadsheetId)
            ->sheet($quotedSheet)
            ->sheetProperties();

        $sheetId = (int) ($sheetProps->sheetId ?? -1);
        if ($sheetId < 0) {
            return;
        }

        $requests = [];
        foreach ($toDelete as $startIndex) {
            $requests[] = [
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => $sheetId,
                        'dimension' => 'ROWS',
                        'startIndex' => $startIndex,
                        'endIndex' => $startIndex + 1,
                    ],
                ],
            ];
        }

        $service = Sheets::spreadsheet($spreadsheetId)->getService();
        $body = new BatchUpdateSpreadsheetRequest([
            'requests' => $requests,
        ]);

        $service->spreadsheets->batchUpdate($spreadsheetId, $body);
    }
}
