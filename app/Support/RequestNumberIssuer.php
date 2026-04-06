<?php

namespace App\Support;

use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Illuminate\Support\Facades\Cache;
use Revolution\Google\Sheets\Facades\Sheets;

/**
 * Issues sequential request numbers: REQ-MSP-{YEAR}-{NNN}.
 * Counter is per calendar year (cache TTL until end of year; new year => new key).
 */
final class RequestNumberIssuer
{
    public static function prefixForYear(int $year): string
    {
        return 'REQ-MSP-'.$year.'-';
    }

    public static function currentPrefix(): string
    {
        return self::prefixForYear((int) now()->year);
    }

    /**
     * @deprecated Use currentPrefix(); kept for call sites expecting "prefix()".
     */
    public static function prefix(): string
    {
        return self::currentPrefix();
    }

    public static function cacheKeyForYear(int $year): string
    {
        return 'access_request_counter:msp:'.$year;
    }

    public static function cacheKey(): string
    {
        return self::cacheKeyForYear((int) now()->year);
    }

    public static function apiSheetTitle(string $title): string
    {
        $escaped = str_replace("'", "''", trim($title));

        return "'".$escaped."'";
    }

    private static function ensureSheetTabExists(string $spreadsheetId, string $sheetTitle): void
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
    }

    /**
     * Highest numeric suffix seen in the numbering sheet for the given year (REQ-MSP-YYYY-NNN).
     */
    private static function maxIncrementFromNumberingSheet(string $spreadsheetId, string $sheetName, int $year): int
    {
        $prefix = self::prefixForYear($year);

        self::ensureSheetTabExists($spreadsheetId, $sheetName);

        try {
            $values = Sheets::spreadsheet($spreadsheetId)
                ->sheet(self::apiSheetTitle($sheetName))
                ->range('B:B')
                ->all();
        } catch (\Throwable $e) {
            report($e);
            $values = [];
        }

        $max = 0;
        foreach ($values as $row) {
            $cell = is_array($row) ? (string) ($row[0] ?? '') : (string) $row;
            $cell = trim($cell);

            if ($cell === '' || strcasecmp($cell, 'Request Number') === 0) {
                continue;
            }

            if (! str_starts_with($cell, $prefix)) {
                continue;
            }

            $suffix = substr($cell, strlen($prefix));
            if (preg_match('/^(\d+)$/', $suffix, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $max;
    }

    public static function isValidIssuedFormat(string $value): bool
    {
        $value = trim($value);

        return (bool) preg_match('/^REQ-MSP-\d{4}-\d{1,9}$/', $value);
    }

    /**
     * Reserve the next request number for the current calendar year.
     */
    public static function reserveNext(): string
    {
        $year = (int) now()->year;
        $spreadsheetId = config('google.spreadsheet_id') ?? env('GOOGLE_SPREADSHEET_ID');
        $numberingSheet = (string) config('google.request_number_sheet', config('google.sheet_name', 'Sheet1'));

        $key = self::cacheKeyForYear($year);
        $ttl = now()->endOfYear();

        $shouldInitFromSheets = is_string($spreadsheetId) && $spreadsheetId !== '' && class_exists(Sheets::class);

        if (! Cache::has($key) && $shouldInitFromSheets) {
            $initialMax = self::maxIncrementFromNumberingSheet($spreadsheetId, $numberingSheet, $year);
            Cache::add($key, $initialMax, $ttl);
        }

        Cache::add($key, 0, $ttl);

        $n = Cache::increment($key);
        if ($n === null || $n < 1) {
            Cache::put($key, 1, $ttl);
            $n = 1;
        }

        return self::prefixForYear($year).str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }
}
