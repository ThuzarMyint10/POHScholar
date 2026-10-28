<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Application Received - '.$this->student->application_number)
            ->view('emails.application_confirmation', ['student' => $this->student]);
    }
}
