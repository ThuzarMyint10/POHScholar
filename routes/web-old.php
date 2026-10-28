<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Student\AcademicRecordController;
use App\Http\Controllers\Student\AcademicResultController;
use App\Http\Controllers\Student\ActivitiesController;
use App\Http\Controllers\Student\AdditionalInfoController;
use App\Http\Controllers\Student\ApplicationPdfController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\DocumentController;
use App\Http\Controllers\Student\EssayController;
use App\Http\Controllers\Student\FamilyBackgroundController;
use App\Http\Controllers\Student\FinalInfoController;
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/programs', [PageController::class, 'programs'])->name('programs');
Route::get('/apply', [PageController::class, 'showApply'])->name('apply');
Route::post('/apply', [PageController::class, 'storeApply'])->name('apply.store');
Route::get('/contact', [PageController::class, 'showContact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Admin authentication
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

Route::get('/student/auth/google', [GoogleAuthController::class, 'redirect'])->name('student.google.redirect');
Route::get('/student/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('student.google.callback');

require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Student authentication
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('login.store');
    Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [StudentAuthController::class, 'register'])->name('register.store');
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Student portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:student', 'student.active'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'show'])->name('dashboard');
    Route::post('/submit', [StudentDashboardController::class, 'submit'])->name('submit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/academic-record', [AcademicRecordController::class, 'edit'])->name('academic_record.edit');
    Route::put('/academic-record', [AcademicRecordController::class, 'update'])->name('academic_record.update');

    Route::get('/family-background', [FamilyBackgroundController::class, 'edit'])->name('family_background.edit');
    Route::put('/family-background', [FamilyBackgroundController::class, 'update'])->name('family_background.update');

    Route::get('/activities', [ActivitiesController::class, 'edit'])->name('activities.edit');
    Route::put('/activities', [ActivitiesController::class, 'update'])->name('activities.update');

    Route::get('/essay', [EssayController::class, 'edit'])->name('essay.edit');
    Route::put('/essay', [EssayController::class, 'update'])->name('essay.update');

    Route::get('/additional-info', [AdditionalInfoController::class, 'edit'])->name('additional_info.edit');
    Route::put('/additional-info', [AdditionalInfoController::class, 'update'])->name('additional_info.update');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/final-info', [FinalInfoController::class, 'edit'])->name('final_info.edit');
    Route::put('/final-info', [FinalInfoController::class, 'update'])->name('final_info.update');

    Route::get('/academic-results', [AcademicResultController::class, 'index'])->name('academic_results.index');
    Route::post('/academic-results', [AcademicResultController::class, 'store'])->name('academic_results.store');

    Route::get('/application-pdf', [ApplicationPdfController::class, 'show'])->name('application_pdf');
    Route::get('/application-pdf/download', [ApplicationPdfController::class, 'download'])->name('application_pdf.download');
});
