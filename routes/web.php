<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ZReportController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\SalonSettingsController;
use App\Http\Controllers\TempPdfController;
use App\Http\Controllers\PendingChangeController;
use App\Http\Controllers\ClientInsightsController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PlatformAdminController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'must_change_password', 'onboarding'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('onboarding.')->prefix('onboarding')->group(function () {
        Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
        Route::get('/{step}', [OnboardingController::class, 'show'])->name('show');
        Route::post('/{step}', [OnboardingController::class, 'store'])->name('store');
    });

    Route::resource('appointments', AppointmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/flag', [CustomerController::class, 'flag'])->name('customers.flag');
    Route::resource('products', ProductController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('todos', TodoController::class);

    // Managing staff (accounts, roles, working hours) is an owner-only capability.
    Route::middleware(['owner'])->group(function () {
        Route::resource('staff', StaffController::class)->except(['show']);

        Route::get('/approvals', [PendingChangeController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{pendingChange}/approve', [PendingChangeController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{pendingChange}/reject', [PendingChangeController::class, 'reject'])->name('approvals.reject');

        Route::get('/client-insights', [ClientInsightsController::class, 'index'])->name('client-insights.index');

        // Closing a day is a permanent fiscal action — owner/super_admin only.
        Route::get('/z-report', [ZReportController::class, 'index'])->name('z-report.index');
        Route::get('/z-report/print', [ZReportController::class, 'print'])->name('z-report.print');
        Route::post('/z-report/close', [ZReportController::class, 'close'])->name('z-report.close');
    });

    Route::resource('service-categories', ServiceCategoryController::class)->except(['show']);
    Route::resource('payments', PaymentController::class)->except(['edit', 'update']);
    Route::get('/payments/{payment}/print', [PaymentController::class, 'print'])->name('payments.print');

    Route::get('/settings/salon', [SalonSettingsController::class, 'edit'])->name('salon-settings.edit');
    Route::put('/settings/salon', [SalonSettingsController::class, 'update'])->name('salon-settings.update');
    Route::post('/settings/salon/sms-test', [SalonSettingsController::class, 'sendTestSms'])->name('salon-settings.sms-test');

    Route::get('/temp-pdf/{token}', [TempPdfController::class, 'show'])->name('temp-pdf.show');

    // Super admin — platform management
    Route::middleware(['super_admin'])->group(function () {
        Route::resource('salons', SalonController::class);
        Route::post('/salons/{salon}/reset-onboarding', [SalonController::class, 'resetOnboarding'])->name('salons.reset-onboarding');
        Route::resource('platform-admins', PlatformAdminController::class)->only(['index', 'create', 'store']);
        Route::post('/platform-admins/{admin}/unlock', [PlatformAdminController::class, 'unlock'])->name('platform-admins.unlock');
        Route::post('/staff/{staff}/unlock', [StaffController::class, 'unlock'])->name('staff.unlock');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profile/working-hours', [ProfileController::class, 'updateWorkingHours'])->name('profile.working-hours.update');
});

require __DIR__.'/auth.php';
