// routes/web.php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    VerificationPortalController,
    Employee\DashboardController      as EmpDashboard,
    Employee\ProfileController        as EmpProfile,
    Employee\HistoryController        as EmpHistory,
    Employee\DisputeController        as EmpDispute,
    Employer\DashboardController      as EmployerDashboard,
    Employer\ProfileController        as EmployerProfile,
    Employer\EmploymentRecordController,
    Employer\FeedbackController,
    Employer\SearchController,
    Admin\DashboardController         as AdminDashboard,
    Admin\UserController              as AdminUser,
    Admin\EmployerVerificationController,
    Admin\DisputeController           as AdminDispute,
    Admin\ReportController,
    Admin\SkillController,
};

// ── Public ────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Auth (Breeze) ─────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Authenticated ─────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Employee ─────────────────────────────────────────────
    Route::prefix('employee')->name('employee.')->middleware('account.type:employee')
        ->group(function () {
            Route::get('/dashboard',         [EmpDashboard::class, 'index'])->name('dashboard');
            Route::get('/profile',           [EmpProfile::class,   'show'])->name('profile.show');
            Route::get('/profile/edit',      [EmpProfile::class,   'edit'])->name('profile.edit');
            Route::put('/profile',           [EmpProfile::class,   'update'])->name('profile.update');
            Route::get('/history',           [EmpHistory::class,   'index'])->name('history.index');
            Route::post('/history/{record}/confirm', [EmpHistory::class, 'confirm'])->name('history.confirm');
            Route::post('/history/{record}/dispute', [EmpDispute::class, 'store'])->name('dispute.store');
            Route::get('/disputes',          [EmpDispute::class,   'index'])->name('dispute.index');
        });

    // ── Employer ─────────────────────────────────────────────
    Route::prefix('employer')->name('employer.')->middleware('account.type:employer')
        ->group(function () {
            Route::get('/dashboard',         [EmployerDashboard::class, 'index'])->name('dashboard');
            Route::get('/profile',           [EmployerProfile::class,   'show'])->name('profile.show');
            Route::get('/profile/edit',      [EmployerProfile::class,   'edit'])->name('profile.edit');
            Route::put('/profile',           [EmployerProfile::class,   'update'])->name('profile.update');

            // Employment Records
            Route::get('/records',           [EmploymentRecordController::class, 'index'])->name('records.index');
            Route::get('/records/create',    [EmploymentRecordController::class, 'create'])->name('records.create');
            Route::post('/records',          [EmploymentRecordController::class, 'store'])->name('records.store');
            Route::get('/records/{record}',  [EmploymentRecordController::class, 'show'])->name('records.show');
            Route::get('/records/{record}/edit', [EmploymentRecordController::class, 'edit'])->name('records.edit');
            Route::put('/records/{record}',  [EmploymentRecordController::class, 'update'])->name('records.update');
            Route::post('/records/{record}/close', [EmploymentRecordController::class, 'close'])->name('records.close');

            // Feedback
            Route::get('/records/{record}/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
            Route::post('/records/{record}/feedback',       [FeedbackController::class, 'store'])->name('feedback.store');
            Route::get('/feedback/{feedback}/edit',         [FeedbackController::class, 'edit'])->name('feedback.edit');
            Route::put('/feedback/{feedback}',              [FeedbackController::class, 'update'])->name('feedback.update');

            // Search / Verification Portal (employer access)
            Route::get('/search',            [SearchController::class, 'index'])->name('search.index');
            Route::get('/search/results',    [SearchController::class, 'results'])->name('search.results');
            Route::get('/search/{employee}', [SearchController::class, 'show'])->name('search.show');
        });

    // ── Admin ─────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('account.type:admin')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

            // Users
            Route::resource('users', AdminUser::class);
            Route::post('/users/{user}/toggle-active', [AdminUser::class, 'toggleActive'])->name('users.toggle');

            // Employers
            Route::get('/employers',                  [EmployerVerificationController::class, 'index'])->name('employers.index');
            Route::get('/employers/{employer}',       [EmployerVerificationController::class, 'show'])->name('employers.show');
            Route::post('/employers/{employer}/verify',  [EmployerVerificationController::class, 'verify'])->name('employers.verify');
            Route::post('/employers/{employer}/reject',  [EmployerVerificationController::class, 'reject'])->name('employers.reject');
            Route::post('/employers/{employer}/suspend', [EmployerVerificationController::class, 'suspend'])->name('employers.suspend');

            // Employees
            Route::get('/employees',              [AdminUser::class, 'employees'])->name('employees.index');
            Route::get('/employees/{employee}',   [AdminUser::class, 'showEmployee'])->name('employees.show');
            Route::post('/employees/{employee}/verify', [AdminUser::class, 'verifyEmployee'])->name('employees.verify');

            // Disputes
            Route::get('/disputes',             [AdminDispute::class, 'index'])->name('disputes.index');
            Route::get('/disputes/{dispute}',   [AdminDispute::class, 'show'])->name('disputes.show');
            Route::post('/disputes/{dispute}/resolve',  [AdminDispute::class, 'resolve'])->name('disputes.resolve');
            Route::post('/disputes/{dispute}/dismiss',  [AdminDispute::class, 'dismiss'])->name('disputes.dismiss');

            // Reports
            Route::get('/reports',          [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export',   [ReportController::class, 'export'])->name('reports.export');

            // Skills
            Route::resource('skills', SkillController::class);
        });
});