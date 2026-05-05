<?php

namespace App\Jobs;

use App\Mail\AccessRequestSubmitted;
use App\Services\AccessRequestGoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HandleAccessRequestSubmissionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $validated
     */
    public function __construct(
        private array $validated,
        private string $timestamp
    ) {
    }

    public function tries(): int
    {
        return 3;
    }

    public function backoff(): array
    {
        return [15, 60, 180];
    }

    public function handle(AccessRequestGoogleSheetsService $sheetsService): void
    {
        // 1) Write to Google Sheets (can be slow/flaky, so it's queued).
        try {
            $sheetsService->submitToSheets($this->validated, $this->timestamp);
        } catch (\Throwable $e) {
            Log::error('Google Sheets submission failed for access request.', [
                'request_number' => (string) ($this->validated['request_number'] ?? ''),
                'email' => (string) ($this->validated['email'] ?? ''),
                'message' => $e->getMessage(),
            ]);
            report($e);
        }

        // 2) Queue email notifications (already asynchronous, but keep it in the same job for faster controller response).
        try {
            $itEmail = (string) env('IT_DEPARTMENT_EMAIL', '');

            $systemsRequested = is_array($this->validated['systems'] ?? null)
                ? implode(', ', (array) $this->validated['systems'])
                : '';
            $systemsText = $systemsRequested !== '' ? $systemsRequested : 'No system selected';

            $name = (string) ($this->validated['full_name'] ?? '');
            $requestNumber = (string) ($this->validated['request_number'] ?? '');

            if ($itEmail !== '') {
                Mail::to($itEmail)->queue(new AccessRequestSubmitted(
                    type: 'it',
                    name: $name,
                    systems: $systemsText,
                    requestNumber: $requestNumber,
                ));
            }

            $userEmail = (string) ($this->validated['email'] ?? '');
            if ($userEmail !== '') {
                Mail::to($userEmail)->queue(new AccessRequestSubmitted(
                    type: 'user',
                    name: $name,
                    systems: $systemsText,
                    requestNumber: $requestNumber,
                ));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

