<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FinalInfoController extends Controller
{
    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        return view('student.final_info', [
            'pageTitle' => 'Final Information',
            'active' => 'final_info',
            'student' => $student,
            'unlocked' => (bool) $student->accepted_at,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if (! $student->accepted_at) {
            return redirect()->route('student.final_info.edit');
        }

        $data = $request->validate([
            'bank_account_name' => ['required', 'string', 'max:200'],
            'bank_name' => ['required', 'string', 'max:200'],
            'bank_branch' => ['nullable', 'string', 'max:200'],
            'bank_account_number' => ['required', 'string', 'max:60'],
            'emergency_contact_name' => ['required', 'string', 'max:200'],
            'emergency_contact_phone' => ['required', 'digits_between:1,15'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
        ]);

        $student->forceFill(array_merge($data, ['final_info_updated_at' => now()]))->save();

        return redirect()->route('student.final_info.edit')->with('success', 'Final Information saved. You can update these details again at any time.');
    }
}
