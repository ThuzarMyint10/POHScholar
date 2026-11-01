<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\RowParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AcademicRecordController extends Controller
{
    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $locked = $student->status !== 'Draft';

        $academicAwards = $student->academic_awards ?: [['year_of_award' => '', 'name_of_award' => '']];
        $financialAwards = $student->financial_awards ?: [['name_of_award' => '', 'tenure_of_award' => '', 'value_of_award' => '']];

        return view('student.academic_record', [
            'pageTitle' => 'Academic Record',
            'active' => 'academic_record',
            'student' => $student,
            'locked' => $locked,
            'academicAwards' => $academicAwards,
            'financialAwards' => $financialAwards,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.academic_record.edit');
        }

        $data = $request->validate([
            'secondary_school' => ['required', 'string', 'max:200'],
            'al_college' => ['required', 'string', 'max:200'],
            'ol_grade_a' => ['required', 'integer', 'min:0'],
            'ol_grade_b' => ['required', 'integer', 'min:0'],
            'ol_grade_c' => ['required', 'integer', 'min:0'],
            'ol_grade_s' => ['required', 'integer', 'min:0'],
            'ol_grade_wf' => ['required', 'integer', 'min:0'],
            'ol_national_ranking' => ['required', 'string', 'max:100'],
            'al_grade_a' => ['required', 'integer', 'min:0'],
            'al_grade_b' => ['required', 'integer', 'min:0'],
            'al_grade_c' => ['required', 'integer', 'min:0'],
            'al_grade_s' => ['required', 'integer', 'min:0'],
            'al_grade_wf' => ['required', 'integer', 'min:0'],
            'al_zscore' => ['required', 'string', 'max:30'],
            'al_national_ranking' => ['required', 'string', 'max:100'],
        ]);

        $academicAwards = RowParser::fromRequest($request, [
            'award_year' => 'year_of_award',
            'award_name' => 'name_of_award',
        ]);
        $financialAwards = RowParser::fromRequest($request, [
            'fin_award_name' => 'name_of_award',
            'fin_award_tenure' => 'tenure_of_award',
            'fin_award_value' => 'value_of_award',
        ]);

        $student->forceFill(array_merge($data, [
            'academic_awards' => $academicAwards,
            'financial_awards' => $financialAwards,
        ]))->save();

        return redirect()->route('student.academic_record.edit')->with('success', 'Academic record saved.');
    }
}
