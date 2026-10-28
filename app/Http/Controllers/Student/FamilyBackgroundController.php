<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\RowParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FamilyBackgroundController extends Controller
{
    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $locked = $student->status !== 'Draft';

        $familyMembers = $student->family_members ?: [
            ['name' => '', 'relationship' => 'Father', 'date_of_birth' => '', 'citizenship' => '', 'marital_status' => '', 'staying_with_applicant' => '', 'occupation' => '', 'employer_or_school' => '', 'monthly_income' => ''],
            ['name' => '', 'relationship' => 'Mother', 'date_of_birth' => '', 'citizenship' => '', 'marital_status' => '', 'staying_with_applicant' => '', 'occupation' => '', 'employer_or_school' => '', 'monthly_income' => ''],
        ];
        $employmentRecords = $student->employment_records ?: [
            ['company_name' => '', 'designation' => '', 'gross_monthly_income' => '', 'period_from' => '', 'period_to' => '', 'years_months' => '', 'job_description' => '', 'staff_reporting' => ''],
        ];

        return view('student.family_background', [
            'pageTitle' => 'Family & Background',
            'active' => 'family_background',
            'student' => $student,
            'locked' => $locked,
            'familyMembers' => $familyMembers,
            'employmentRecords' => $employmentRecords,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.family_background.edit');
        }

        $familyMembers = RowParser::fromRequest($request, [
            'fam_name' => 'name',
            'fam_relationship' => 'relationship',
            'fam_dob' => 'date_of_birth',
            'fam_citizenship' => 'citizenship',
            'fam_marital_status' => 'marital_status',
            'fam_staying_with' => 'staying_with_applicant',
            'fam_occupation' => 'occupation',
            'fam_employer_or_school' => 'employer_or_school',
            'fam_income' => 'monthly_income',
        ]);
        $employmentRecords = RowParser::fromRequest($request, [
            'emp_company' => 'company_name',
            'emp_designation' => 'designation',
            'emp_income' => 'gross_monthly_income',
            'emp_from' => 'period_from',
            'emp_to' => 'period_to',
            'emp_years_months' => 'years_months',
            'emp_job_description' => 'job_description',
            'emp_staff_reporting' => 'staff_reporting',
        ]);

        $student->forceFill([
            'family_members' => $familyMembers,
            'employment_records' => $employmentRecords,
        ])->save();

        return redirect()->route('student.family_background.edit')->with('success', 'Family & background information saved.');
    }
}
