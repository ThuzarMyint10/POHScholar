<?php

namespace App\Mail;

use App\Models\AcademicResult;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcademicResultReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Student $student, public AcademicResult $period)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Reminder: Academic Result Update Due - POH Scholarships Scheme')
            ->view('emails.academic_result_reminder', ['student' => $this->student, 'period' => $this->period]);
    }
}
