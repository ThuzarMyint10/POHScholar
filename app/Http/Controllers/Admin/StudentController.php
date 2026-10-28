<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AcademicResultReminderMail;
use App\Mail\AcceptanceMail;
use App\Models\AcademicResult;
use App\Models\Scholar;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Services\AiRankingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    private const STATUSES = ['Draft', 'Submitted', 'Under Review', 'Approved', 'Graduated', 'Discontinued', 'Rejected'];

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', '');
        $search = trim((string) $request->query('q', ''));

        $query = Student::query()->with('program');

        if (in_array($statusFilter, self::STATUSES, true)) {
            $query->where('status', $statusFilter);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $like = '%'.$search.'%';
                $q->where('full_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('application_number', 'like', $like)
                    ->orWhere('district', 'like', $like);
            });
        }

        if ($statusFilter === 'Under Review') {
            $query->orderByDesc('ai_rank_composite_score');
        }

        $students = $query->orderByRaw("(status = 'Draft') asc")
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->get();

        $overdueIds = AcademicResult::query()
            ->whereNull('submitted_at')
            ->where('due_date', '<', now()->toDateString())
            ->distinct()
            ->pluck('student_id')
            ->flip();

        return view('admin.students.index', [
            'pageTitle' => 'Student Applications',
            'active' => 'students',
            'students' => $students,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'statuses' => self::STATUSES,
            'overdueIds' => $overdueIds,
            'overdueCount' => $overdueIds->count(),
            'underReviewCount' => Student::query()->where('status', 'Under Review')->count(),
        ]);
    }

    public function runAiRanking(AiRankingService $ranker): RedirectResponse
    {
        $students = Student::query()->where('status', 'Under Review')->get();
        $successes = 0;
        $failures = 0;

        foreach ($students as $student) {
            try {
                $this->applyRanking($student, $ranker);
                $successes++;
            } catch (\Throwable $e) {
                $failures++;
            }
        }

        $message = "AI ranking completed for {$successes} application(s).";
        if ($failures > 0) {
            $message .= " {$failures} application(s) could not be ranked.";
        }

        return redirect()->route('admin.students.index', ['status' => 'Under Review'])->with(
            $failures > 0 ? 'error' : 'success',
            $message
        );
    }

    public function runAiRankingForStudent(Student $student, AiRankingService $ranker): RedirectResponse
    {
        try {
            $this->applyRanking($student, $ranker);

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', 'AI ranking completed.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.students.show', $student->id)
                ->with('error', 'AI ranking failed: '.$e->getMessage());
        }
    }

    private function applyRanking(Student $student, AiRankingService $ranker): void
    {
        $result = $ranker->rank($student);

        $student->forceFill([
            'ai_rank_family_score' => $result['family_background_score'],
            'ai_rank_academic_score' => $result['academic_result_score'],
            'ai_rank_economic_score' => $result['economic_status_score'],
            'ai_rank_essay_score' => $result['essay_score'],
            'ai_rank_composite_score' => $result['composite_score'],
            'ai_rank_summary' => $result['summary'],
            'ai_ranked_at' => now(),
        ])->save();
    }

    public function bulkRemind(): RedirectResponse
    {
        $overdue = AcademicResult::query()->with('student')
            ->whereNull('submitted_at')
            ->where('due_date', '<', now()->toDateString())
            ->get();

        $sent = 0;
        foreach ($overdue as $period) {
            if (! $period->student) {
                continue;
            }
            try {
                Mail::to($period->student->email)->send(new AcademicResultReminderMail($period->student, $period));
                $period->forceFill(['reminder_sent_at' => now(), 'reminder_count' => $period->reminder_count + 1])->save();
                $sent++;
            } catch (\Throwable $e) {
                // Skip and continue with the rest.
            }
        }

        $message = $sent > 0 ? "Sent {$sent} reminder email(s) to overdue students." : 'No overdue academic result submissions found.';

        return redirect()->route('admin.students.index')->with('success', $message);
    }

    public function show(Student $student): View
    {
        return view('admin.students.show', [
            'pageTitle' => 'Student: '.($student->full_name ?: $student->email),
            'active' => 'students',
            'student' => $student,
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'periods' => $student->academicResults()->orderBy('period_number')->get(),
            'today' => now()->toDateString(),
            'statuses' => self::STATUSES,
            'adminRanks' => $student->section_rank_admin ?: [],
            'mrsPohRanks' => $student->section_rank_mrs_poh ?: [],
        ]);
    }

    public function updateStatus(Request $request, Student $student): RedirectResponse
    {
        $status = $request->input('status', $student->status);

        if (! in_array($status, self::STATUSES, true)) {
            return redirect()->route('admin.students.show', $student->id);
        }

        $wasApproved = $student->status === 'Approved';

        DB::transaction(function () use ($student, $status): void {
            $student->forceFill([
                'status' => $status,
                'is_active' => in_array($status, ['Graduated', 'Discontinued'], true) ? false : ($status === 'Approved' ? true : $student->is_active),
            ])->save();

            if (in_array($status, ['Approved', 'Graduated', 'Discontinued'], true)) {
                $scholar = Scholar::query()->firstOrNew(['student_id' => $student->id]);
                $scholar->forceFill([
                    'full_name' => $student->full_name ?: $student->email,
                    'program_id' => $student->program_id,
                    'school_or_institute' => $student->school_or_institute,
                    'district' => $student->district,
                    'year_enrolled' => is_numeric($student->grade_or_year) ? (int) $student->grade_or_year : null,
                    'status' => $status === 'Graduated' ? 'Graduated' : ($status === 'Discontinued' ? 'Discontinued' : 'Active'),
                ]);
                if ($status === 'Graduated') {
                    $scholar->is_featured = false;
                }
                $scholar->save();
            } elseif ($status === 'Rejected') {
                Scholar::query()->where('student_id', $student->id)->delete();
            }
        });

        if (in_array($status, ['Graduated', 'Discontinued'], true)) {
            return redirect()->route('admin.students.show', $student->id)
            ->with('success', 'Student status updated, Scholar Register synchronized, and account access disabled.');
        }

        if ($status === 'Approved' && ! $wasApproved) {
            $student->forceFill(['accepted_at' => now()])->save();

            if ($student->academicResults()->count() === 0) {
                AcademicResult::query()->create([
                    'student_id' => $student->id,
                    'period_number' => 1,
                    'due_date' => now()->addMonths(6)->toDateString(),
                ]);
            }

            $student->refresh();

            try {
                Mail::to($student->email)->send(new AcceptanceMail($student));
            } catch (\Throwable $e) {
                // Application state is saved regardless of email delivery.
            }

            return redirect()->route('admin.students.show', $student->id)
                ->with('success', 'Application accepted - the candidate has been emailed to complete their Final Information.');
        }

        if ($status === 'Rejected') {
            return redirect()->route('admin.students.show', $student->id)->with('success', 'Status updated to Rejected.');
        }

        return redirect()->route('admin.students.show', $student->id)->with('success', 'Status updated.');
    }

    public function updateAccountAccess(Request $request, Student $student): RedirectResponse
    {
        $active = $request->input('is_active') === '1';
        $student->forceFill(['is_active' => $active])->save();

        $message = $active
            ? 'Student account enabled.'
            : 'Student account disabled - they will be logged out and unable to sign in.';

        return redirect()->route('admin.students.show', $student->id)->with('success', $message);
    }

    public function sendReminder(Request $request, Student $student): RedirectResponse
    {
        $period = $student->academicResults()->find($request->input('period_id'));

        if ($period && ! $period->submitted_at) {
            try {
                Mail::to($student->email)->send(new AcademicResultReminderMail($student, $period));
                $period->forceFill(['reminder_sent_at' => now(), 'reminder_count' => $period->reminder_count + 1])->save();

                return redirect()->route('admin.students.show', $student->id)
                    ->with('success', 'Reminder email sent for Period '.$period->period_number.'.');
            } catch (\Throwable $e) {
                return redirect()->route('admin.students.show', $student->id)
                    ->with('error', 'Could not send the reminder email. Check your mail settings.');
            }
        }

        return redirect()->route('admin.students.show', $student->id);
    }

    public function updateEvaluation(Request $request, Student $student): RedirectResponse
    {
        $score = trim((string) $request->input('evaluation_score', ''));
        $notes = trim((string) $request->input('evaluation_notes', ''));
        $scoreValue = $score === '' ? null : max(0, min(5, (float) $score));

        $student->forceFill([
            'evaluation_score' => $scoreValue,
            'evaluation_notes' => $notes,
            'evaluated_by' => Auth::guard('admin')->id(),
            'evaluated_at' => now(),
        ])->save();

        return redirect()->route('admin.students.show', $student->id)->with('success', 'Evaluation saved.');
    }

    public function updateMrsPoh(Request $request, Student $student): RedirectResponse
    {
        $remarks = trim((string) $request->input('mrs_poh_remarks', ''));

        $student->forceFill([
            'mrs_poh_remarks' => $remarks,
            'mrs_poh_updated_at' => now(),
        ])->save();

        return redirect()->route('admin.students.show', $student->id)->with('success', "Mrs. Poh's remarks saved.");
    }

    public function updateSectionRanks(Request $request, Student $student): RedirectResponse
    {
        $mergeRanks = function (?array $existing, array $submitted): array {
            $existing = $existing ?: [];
            foreach ($submitted as $key => $value) {
                $value = trim((string) $value);
                if ($value === '') {
                    unset($existing[$key]);
                } else {
                    $clamped = round(max(0, min(5, (float) $value)), 1);
                    $existing[$key] = rtrim(rtrim(sprintf('%.1f', $clamped), '0'), '.');
                }
            }

            return $existing;
        };

        $newAdminRanks = $mergeRanks($student->section_rank_admin, (array) $request->input('rank_admin', []));
        $newMrsPohRanks = $mergeRanks($student->section_rank_mrs_poh, (array) $request->input('rank_mrs_poh', []));

        $student->forceFill([
            'section_rank_admin' => $newAdminRanks,
            'section_rank_mrs_poh' => $newMrsPohRanks,
        ])->save();

        return redirect()->route('admin.students.show', $student->id)->with('success', 'Section ranks saved.');
    }

    /**
     * Stream a student's attached document inline for admin viewing, without
     * depending on the public/storage symlink.
     */
    public function viewDocument(Student $student, StudentDocument $document)
    {
        abort_unless($document->student_id === $student->id, 404);
        abort_unless(Storage::disk('public')->exists($document->stored_path), 404);

        return Storage::disk('public')->response(
            $document->stored_path,
            $document->original_filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function viewResultFile($studentId, $periodId)
    {
        $period = AcademicResult::where('student_id', $studentId)
            ->where('id', $periodId)
            ->firstOrFail();

        abort_unless($period->result_file, 404);

        // Check both disks so old (public) and new (private) uploads both work
        foreach (['public', 'local'] as $disk) {
            if (Storage::disk($disk)->exists($period->result_file)) {
                return Storage::disk($disk)->response($period->result_file);
            }
        }

        abort(404, 'Result file not found on disk.');
    }

}