<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\QuickApplication;
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

        $profileComplete = $student->profileComplete();
        $essayComplete = str_word_count(trim((string) $student->essay)) >= 100;
        $documentsPresent = $docCount > 0;
        $academicRecordComplete = $student->academicRecordComplete();
        $familyBackgroundPresent = $student->familyBackgroundComplete();
        $activitiesComplete = $student->activitiesComplete();
        $declarationComplete = $student->declarationComplete();

        $canSubmit = $student->missingSubmissionSections() === [] && $student->status === 'Draft';

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
            'activitiesComplete' => $activitiesComplete,
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

        $missingSections = $student->missingSubmissionSections();
        if ($missingSections !== []) {
            return redirect()->route('student.dashboard');
        }

        $nicOrBirthCert = strtoupper(trim((string) $student->nic_or_birth_cert));
        $duplicateStudent = Student::query()
            ->where('id', '!=', $student->id)
            ->whereRaw('UPPER(TRIM(nic_or_birth_cert)) = ?', [$nicOrBirthCert])
            ->exists();
        $duplicateQuickApplication = QuickApplication::query()
            ->whereRaw('UPPER(TRIM(nic_or_birth_cert)) = ?', [$nicOrBirthCert])
            ->exists();
        if ($duplicateStudent || $duplicateQuickApplication) {
            return redirect()->route('student.profile.edit')
                ->withErrors(['nic_or_birth_cert' => 'NRIC number cannot be the same as another application.']);
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
