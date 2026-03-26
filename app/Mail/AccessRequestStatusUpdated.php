<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessRequestStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $requestNumber,
        public string $status, // approved|rejected|pending
        public string $systems,
        public ?string $remarks,
        public string $adminLabel,
    ) {
    }

    public function build(): self
    {
        $statusLabel = ucfirst($this->status);
        $requestNumber = $this->requestNumber !== '' ? $this->requestNumber : 'N/A';

        $base = match ($this->status) {
            'approved' => "Your request {$requestNumber} has been approved.",
            'rejected' => "Your request {$requestNumber} has been rejected.",
            default => "Your request {$requestNumber} has been updated to {$statusLabel}.",
        };

        $greeting = $this->name !== '' ? "Hi {$this->name}," : '';
        $systemsPart = $this->systems !== '' ? "Systems: {$this->systems}." : '';
        $remarksPart = $this->remarks !== null && trim($this->remarks) !== '' ? "Remarks: {$this->remarks}" : '';

        $messageText = trim(implode(' ', array_filter([
            $greeting,
            $base,
            "Actioned by: {$this->adminLabel}.",
            $systemsPart,
            $remarksPart,
        ])));

        return $this
            ->subject("Access Request {$requestNumber}: {$statusLabel}")
            ->view('emails.access-request-status-updated', [
                'messageText' => $messageText,
            ]);
    }
}

