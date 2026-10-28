<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\Student;
use App\Support\ApplicationNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        $docCount = $student->documents()->count();

        $profileComplete = filled($student->full_name) && filled($student->date_of_birth) && filled($student->gender)
            && filled($student->address) && filled($student->district) && filled($student->country) && filled($student->phone)
            && filled($student->school_or_institute) && filled($student->guardian_name) && filled($student->photo);
        $essayComplete = filled(trim((string) $student->essay));
        $documentsPresent = $docCount > 0;
        $academicRecordComplete = filled($student->secondary_school) || filled($student->al_college);
        $familyBackgroundPresent = filled($student->family_members);
        $declarationComplete = (bool) $student->declaration_agreed && filled($student->declaration_signature);

        $canSubmit = $profileComplete && $essayComplete && $declarationComplete && $student->status === 'Draft';

        $openPeriod = null;
        if ($student->accepted_at) {
            $openPeriod = $student->academicResults()->whereNull('submitted_at')->orderBy('period_number')->first();
        }

        return view('student.dashboard', [
            'pageTitle' => 'Dashboard',
            'active' => 'dashboard',
            'student' => $student,
            'docCount' => $docCount,
            'profileComplete' => $profileComplete,
            'essayComplete' => $essayComplete,
            'documentsPresent' => $documentsPresent,
            'academicRecordComplete' => $academicRecordComplete,
            'familyBackgroundPresent' => $familyBackgroundPresent,
            'declarationComplete' => $declarationComplete,
            'canSubmit' => $canSubmit,
            'openPeriod' => $openPeriod,
        ]);
    }

    public function submit(): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.dashboard')->with('info', 'Your application has already been submitted.');
        }

        $profileComplete = filled($student->full_name) && filled($student->date_of_birth) && filled($student->gender)
            && filled($student->address) && filled($student->district) && filled($student->country) && filled($student->phone)
            && filled($student->school_or_institute) && filled($student->guardian_name) && filled($student->photo);
        $essayComplete = filled(trim((string) $student->essay));
        $declarationComplete = (bool) $student->declaration_agreed && filled($student->declaration_signature);

        if (! $profileComplete || ! $essayComplete || ! $declarationComplete) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Please complete your profile, essay and the declaration before submitting.');
        }

        $applicationNumber = ApplicationNumberGenerator::next($student->id);

        $student->forceFill([
            'application_number' => $applicationNumber,
            'status' => 'Submitted',
            'submitted_at' => now(),
        ])->save();

        $sent = true;
        try {
            Mail::to($student->email)->send(new \App\Mail\ApplicationConfirmationMail($student));
            foreach (config('poh.admin_notification_emails') as $adminEmail) {
                Mail::to($adminEmail)->send(new \App\Mail\AdminNewApplicationMail($student));
            }
        } catch (\Throwable $e) {
            $sent = false;
        }

        if ($sent) {
            return redirect()->route('student.dashboard')->with('success',
                "Your application has been submitted! Your reference number is {$applicationNumber}. A confirmation email has been sent to your inbox.");
        }

        return redirect()->route('student.dashboard')->with('success',
            "Your application has been submitted! Your reference number is {$applicationNumber}. (We couldn't send a confirmation email right now, but your application is safely recorded.)");
    }
}
