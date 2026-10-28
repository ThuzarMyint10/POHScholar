<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Congratulations! Your Scholarship Application Has Been Accepted - '.$this->student->application_number)
            ->view('emails.acceptance', ['student' => $this->student]);
    }
}
