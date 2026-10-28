<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationPdfController extends Controller
{
    public function show(): View|RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status === 'Draft') {
            return redirect()->route('student.dashboard')
                ->with('info', 'You can print or download a copy once your application has been submitted.');
        }

        return view('shared.application_print', [
            'student' => $student,
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'showAdminFields' => false,
            'backUrl' => route('student.dashboard'),
            'pageTitlePrefix' => 'My Application',
            'downloadUrl' => route('student.application_pdf.download'),
        ]);
    }

    public function download(): Response|RedirectResponse
    {
        /** @var Student $student */
        $student = Auth::guard('student')->user();

        if ($student->status === 'Draft') {
            return redirect()->route('student.dashboard')
                ->with('info', 'You can print or download a copy once your application has been submitted.');
        }

        $pdf = Pdf::loadView('shared.application_pdf_dompdf', [
            'student' => $student,
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'showAdminFields' => false,
            'pageTitlePrefix' => 'My Application',
        ])->setPaper('a4');

        return $pdf->download('application-'.($student->application_number ?: $student->id).'.pdf');
    }
}
