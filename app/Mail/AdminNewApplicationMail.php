<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('New Scholarship Application Submitted - '.$this->student->application_number)
            ->view('emails.admin_new_application', ['student' => $this->student]);
    }
}
