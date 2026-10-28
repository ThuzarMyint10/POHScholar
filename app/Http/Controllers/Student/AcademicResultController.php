<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AcademicResultController extends Controller
{
    public function index(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $unlocked = (bool) $student->accepted_at;

        $openPeriod = null;
        $periods = collect();

        if ($unlocked) {
            $openPeriod = $student->academicResults()->whereNull('submitted_at')->orderBy('period_number')->first();
            $periods = $student->academicResults()->orderBy('period_number')->get();
        }

        return view('student.academic_results', [
            'pageTitle' => 'Academic Results',
            'active' => 'academic_results',
            'student' => $student,
            'unlocked' => $unlocked,
            'openPeriod' => $openPeriod,
            'periods' => $periods,
            'today' => now()->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if (! $student->accepted_at) {
            return redirect()->route('student.academic_results.index');
        }

        $openPeriod = $student->academicResults()->whereNull('submitted_at')->orderBy('period_number')->first();

        if (! $openPeriod) {
            return redirect()->route('student.academic_results.index');
        }

        $request->validate([
            'result_summary' => ['required', 'string'],
            'result_file' => ['nullable', 'file', 'mimes:pdf', 'max:8192'],
        ]);

        $resultFile = $openPeriod->result_file;
        if ($request->hasFile('result_file')) {
            $resultFile = $request->file('result_file')->store('academic-results/'.$student->id, 'public');
        }

        $openPeriod->forceFill([
            'submitted_at' => now(),
            'result_summary' => $request->input('result_summary'),
            'result_file' => $resultFile,
        ])->save();

        $nextDue = \Illuminate\Support\Carbon::parse($openPeriod->due_date)->addMonths(6)->toDateString();
        AcademicResult::query()->create([
            'student_id' => $student->id,
            'period_number' => $openPeriod->period_number + 1,
            'due_date' => $nextDue,
        ]);

        return redirect()->route('student.academic_results.index')
            ->with('success', 'Thank you - your academic result for Period '.$openPeriod->period_number.' has been submitted.');
    }
}
