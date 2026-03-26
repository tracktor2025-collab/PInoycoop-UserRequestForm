<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Revolution\Google\Sheets\Facades\Sheets;

class AccessRequestGoogleSheetsService
{
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

    private function ensureSheetTabExists(string $spreadsheetId, string $sheetTitle): void
    {
        // Avoid expensive spreadsheets->get calls on every submission.
        $cacheKey = 'google:sheets:tab_exists:' . md5($spreadsheetId . ':' . $sheetTitle);
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
        $headerOkCacheKey = 'google:sheets:header_ok:' . md5($spreadsheetId . ':' . $sheetName . ':' . $headerHash);

        $headerOk = Cache::get($headerOkCacheKey, false) === true;
        $shouldAutoResize = false;

        if (! $headerOk) {
            $endCol = $this->columnLetter(\count($headerRow));
            $firstRow = Sheets::spreadsheet($spreadsheetId)
                ->sheet($this->apiSheetTitle($sheetName))
                ->range('A1:' . $endCol . '1')
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

                $shouldAutoResize = true;
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
    }

    private function buildSheetPayloadForSystem(string $system, array $validated, string $timestamp): array
    {
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
            $timestamp,
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
            return [
                'header' => array_merge($commonHeader, ['Core Roles', 'Status']),
                'row' => array_merge($commonRow, [$coreRoles, 'Pending']),
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

    public function submitToSheets(array $validated, string $timestamp): void
    {
        $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID', '');
        if ($spreadsheetId === '' || ! class_exists(\Revolution\Google\Sheets\Facades\Sheets::class)) {
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
}

