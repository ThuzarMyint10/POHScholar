<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentPdfController extends Controller
{
    public function show(Student $student): View
    {
        return view('shared.application_print', [
            'student' => $student,
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'showAdminFields' => true,
            'backUrl' => route('admin.students.show', $student->id),
            'pageTitlePrefix' => 'Application',
            'downloadUrl' => route('admin.students.pdf_download', $student->id),
        ]);
    }

    public function download(Student $student): Response
    {
        $pdf = Pdf::loadView('shared.application_pdf_dompdf', [
            'student' => $student,
            'documents' => $student->documents()->orderBy('uploaded_at')->get(),
            'showAdminFields' => true,
            'pageTitlePrefix' => 'Application',
        ])->setPaper('a4');

        return $pdf->download('application-'.($student->application_number ?: $student->id).'.pdf');
    }
}
