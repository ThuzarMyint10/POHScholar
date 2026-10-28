<?php

// Protected admin-dashboard routes (student applications, quick applications,
// scholars, programs, invite candidate, news & events, messages, site
// settings, admin users). Required from routes/web.php, which already
// defines the unauthenticated admin.login / admin.login.store / admin.logout
// routes.

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InviteCandidateController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ScholarController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\StudentPdfController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Quick (public "apply now") applications.
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::put('/applications/{application}', [AdminApplicationController::class, 'update'])->name('applications.update');

    // Full student portal applications.
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::post('/students/bulk-remind', [AdminStudentController::class, 'bulkRemind'])->name('students.bulk_remind');
    Route::post('/students/ai-rank', [AdminStudentController::class, 'runAiRanking'])->name('students.ai_rank');
    Route::post('/students/{student}/ai-rank', [AdminStudentController::class, 'runAiRankingForStudent'])->name('students.ai_rank_one');
    Route::get('/students/{student}', [AdminStudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/documents/{document}/view', [AdminStudentController::class, 'viewDocument'])->name('students.documents.view');
    Route::get('/students/{student}/results/{period}/file', [AdminStudentController::class, 'viewResultFile'])->name('students.result_file');
    Route::get('/students/{student}/pdf', [StudentPdfController::class, 'show'])->name('students.pdf');
    Route::get('/students/{student}/pdf/download', [StudentPdfController::class, 'download'])->name('students.pdf_download');
    Route::put('/students/{student}/status', [AdminStudentController::class, 'updateStatus'])->name('students.update_status');
    Route::put('/students/{student}/account-access', [AdminStudentController::class, 'updateAccountAccess'])->name('students.update_account_access');
    Route::post('/students/{student}/send-reminder', [AdminStudentController::class, 'sendReminder'])->name('students.send_reminder');
    Route::put('/students/{student}/evaluation', [AdminStudentController::class, 'updateEvaluation'])->name('students.update_evaluation');
    Route::put('/students/{student}/mrs-poh', [AdminStudentController::class, 'updateMrsPoh'])->name('students.update_mrs_poh');
    Route::put('/students/{student}/section-ranks', [AdminStudentController::class, 'updateSectionRanks'])->name('students.update_section_ranks');

    // Scholars.
    Route::get('/scholars', [ScholarController::class, 'index'])->name('scholars.index');
    Route::get('/scholars/create', [ScholarController::class, 'create'])->name('scholars.create');
    Route::post('/scholars', [ScholarController::class, 'store'])->name('scholars.store');
    Route::get('/scholars/{scholar}/edit', [ScholarController::class, 'edit'])->name('scholars.edit');
    Route::put('/scholars/{scholar}', [ScholarController::class, 'update'])->name('scholars.update');
    Route::delete('/scholars/{scholar}', [ScholarController::class, 'destroy'])->name('scholars.destroy');

    // Programs.
    Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');

    // Countries and districts used by application address fields.
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses/countries', [AddressController::class, 'storeCountry'])->name('addresses.countries.store');
    Route::put('/addresses/countries/{country}', [AddressController::class, 'updateCountry'])->name('addresses.countries.update');
    Route::delete('/addresses/countries/{country}', [AddressController::class, 'destroyCountry'])->name('addresses.countries.destroy');
    Route::post('/addresses/countries/{country}/districts', [AddressController::class, 'storeDistrict'])->name('addresses.districts.store');
    Route::put('/addresses/districts/{district}', [AddressController::class, 'updateDistrict'])->name('addresses.districts.update');
    Route::delete('/addresses/districts/{district}', [AddressController::class, 'destroyDistrict'])->name('addresses.districts.destroy');

    // Invite a candidate (send invitation-to-apply email).
    Route::get('/invite-candidate', [InviteCandidateController::class, 'show'])->name('invite.show');
    Route::post('/invite-candidate', [InviteCandidateController::class, 'send'])->name('invite.send');

    // News & events.
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{newsEvent}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{newsEvent}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{newsEvent}', [NewsController::class, 'destroy'])->name('news.destroy');

    // Contact messages.
    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');

    // Site settings.
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Admin users (administrator role only - enforced in the controller).
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});