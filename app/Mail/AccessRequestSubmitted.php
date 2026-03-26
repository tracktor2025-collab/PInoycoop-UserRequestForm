<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessRequestSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $type, // 'it' | 'user'
        public string $name,
        public string $systems,
        public string $requestNumber,
    ) {
    }

    public function build(): self
    {
        if ($this->type === 'it') {
            return $this
                ->subject('New Access Request: ' . $this->requestNumber)
                ->view('emails.access-request-submitted', [
                    'messageText' => "New Access Request from {$this->name} for {$this->systems}.",
                ]);
        }

        return $this
            ->subject('We received your access request: ' . $this->requestNumber)
            ->view('emails.access-request-submitted', [
                'messageText' => "We received your request. Your reference number is {$this->requestNumber}.",
            ]);
    }
}

