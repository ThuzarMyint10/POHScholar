<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        return view('student.documents', [
            'pageTitle' => 'Certificates & Documents',
            'active' => 'documents',
            'student' => $student,
            'locked' => $student->status !== 'Draft',
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'maxDocuments' => config('poh.max_student_documents'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status !== 'Draft') {
            return redirect()->route('student.documents.index');
        }

        $currentCount = $student->documents()->count();
        $max = config('poh.max_student_documents');

        if ($currentCount >= $max) {
            return back()->withErrors(['document' => "You have already attached the maximum of {$max} documents. Remove one before adding another."]);
        }

        $request->validate([
            'document' => ['required', 'file', 'mimes:pdf', 'max:8192'],
            'label' => ['nullable', 'string', 'max:150'],
        ]);

        $file = $request->file('document');
        $storedPath = $file->store('certificates/'.$student->id, 'public');

        StudentDocument::query()->create([
            'student_id' => $student->id,
            'label' => $request->input('label') ?: null,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'file_size' => $file->getSize(),
            'uploaded_at' => now(),
        ]);

        return redirect()->route('student.documents.index')->with('success', 'Document uploaded.');
    }

    public function destroy(StudentDocument $document): RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($document->student_id !== $student->id || $student->status !== 'Draft') {
            return redirect()->route('student.documents.index');
        }

        Storage::disk('public')->delete($document->stored_path);
        $document->delete();

        return redirect()->route('student.documents.index')->with('success', 'Document removed.');
    }
}
