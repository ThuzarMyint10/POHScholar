<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EssayController extends Controller
{
    private const MIN_WORDS = 100;

    private const MAX_WORDS = 800;

    public function edit(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        return view('student.essay', [
            'pageTitle' => 'My Essay',
            'active' => 'essay',
            'student' => $student,
            'locked' => $student->status !== 'Draft',
            'minWords' => self::MIN_WORDS,
            'maxWords' => self::MAX_WORDS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.essay.edit');
        }

        $essay = trim((string) $request->input('essay', ''));
        $wordCount = $essay === '' ? 0 : str_word_count($essay);

        if ($essay === '') {
            return back()->withErrors(['essay' => 'Please write your essay before saving.']);
        }
        if ($wordCount < self::MIN_WORDS) {
            return back()->withInput()->withErrors(['essay' => "Your essay is quite short ({$wordCount} words) - please write at least ".self::MIN_WORDS.' words.']);
        }
        if ($wordCount > self::MAX_WORDS) {
            return back()->withInput()->withErrors(['essay' => "Your essay is over the ".self::MAX_WORDS."-word limit ({$wordCount} words). Please shorten it."]);
        }

        $student->forceFill(['essay' => $essay, 'essay_updated_at' => now()])->save();

        return redirect()->route('student.essay.edit')->with('success', 'Essay saved.');
    }
}
