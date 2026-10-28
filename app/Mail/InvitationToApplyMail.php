<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationToApplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $name)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Congratulations - Apply for a POH Scholarship')
            ->view('emails.invitation_to_apply', ['name' => $this->name]);
    }
}
