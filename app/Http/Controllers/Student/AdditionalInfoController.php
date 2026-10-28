<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\RowParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdditionalInfoController extends Controller
{
    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();
        $locked = $student->status !== 'Draft';

        $referees = $student->referees ?: [
            ['name' => '', 'address' => '', 'occupation' => '', 'telephone' => '', 'years_known' => ''],
            ['name' => '', 'address' => '', 'occupation' => '', 'telephone' => '', 'years_known' => ''],
        ];

        return view('student.additional_info', [
            'pageTitle' => 'Other Information & Declaration',
            'active' => 'additional_info',
            'student' => $student,
            'locked' => $locked,
            'referees' => $referees,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.additional_info.edit');
        }

        $prevScholarshipFlag = $request->input('prev_scholarship_flag', '');
        $healthConditionFlag = $request->input('health_condition_flag', '');
        $legalChargeFlag = $request->input('legal_charge_flag', '');
        $declarationSignature = trim((string) $request->input('declaration_signature', ''));
        $declarationDate = trim((string) $request->input('declaration_date', ''));
        $declarationAgreed = $request->boolean('declaration_agreed');

        $referees = RowParser::fromRequest($request, [
            'ref_name' => 'name', 'ref_address' => 'address', 'ref_occupation' => 'occupation',
            'ref_telephone' => 'telephone', 'ref_years_known' => 'years_known',
        ]);
        $referees = array_slice($referees, 0, 2);

        if ($declarationAgreed && $declarationSignature === '') {
            return back()->withInput()->withErrors(['declaration_signature' => 'Please type your name as your signature to confirm the declaration.']);
        }

        $toFlag = fn ($v) => $v === '' ? null : ($v === '1' ? 1 : 0);

        $student->forceFill([
            'prev_scholarship_flag' => $toFlag($prevScholarshipFlag),
            'prev_scholarship_details' => trim((string) $request->input('prev_scholarship_details', '')) ?: null,
            'health_condition_flag' => $toFlag($healthConditionFlag),
            'health_condition_details' => trim((string) $request->input('health_condition_details', '')) ?: null,
            'legal_charge_flag' => $toFlag($legalChargeFlag),
            'legal_charge_details' => trim((string) $request->input('legal_charge_details', '')) ?: null,
            'referees' => $referees,
            'declaration_signature' => $declarationSignature ?: null,
            'declaration_date' => $declarationDate ?: null,
            'declaration_agreed' => $declarationAgreed,
        ])->save();

        return redirect()->route('student.additional_info.edit')->with('success', 'Other information saved.');
    }
}
