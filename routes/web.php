<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Member;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Members
    Route::resource('members', Admin\MemberController::class);

    // Membership Plans
    Route::resource('plans', Admin\MembershipPlanController::class)->except(['show']);

    // Memberships
    Route::get('/memberships', [Admin\MembershipController::class, 'index'])->name('memberships.index');
    Route::get('/memberships/create', [Admin\MembershipController::class, 'create'])->name('memberships.create');
    Route::post('/memberships', [Admin\MembershipController::class, 'store'])->name('memberships.store');
    Route::delete('/memberships/{membership}', [Admin\MembershipController::class, 'destroy'])->name('memberships.destroy');

    // Gym Services
    Route::resource('services', Admin\GymServiceController::class)->except(['show']);

    // Availed Services (Read-only - automatically populated from approvals)
    Route::get('/availed-services', [Admin\AvailedServiceController::class, 'index'])->name('availed-services.index');

    // Billing
    Route::get('/billing', [Admin\BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{billing}', [Admin\BillingController::class, 'show'])->name('billing.show');
    Route::put('/billing/{billing}', [Admin\BillingController::class, 'update'])->name('billing.update');
    Route::get('/billing/{billing}/receipt', [Admin\BillingController::class, 'receipt'])->name('billing.receipt');

    // Attendance
    Route::get('/attendance', [Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [Admin\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [Admin\AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // Approvals
    Route::get('/approvals', [Admin\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{availedService}/approve', [Admin\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{availedService}/reject', [Admin\ApprovalController::class, 'reject'])->name('approvals.reject');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [Admin\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/memberships', [Admin\ReportController::class, 'memberships'])->name('reports.memberships');
    Route::get('/reports/attendance', [Admin\ReportController::class, 'attendanceReport'])->name('reports.attendance');
});

// Member Routes
Route::prefix('member')->name('member.')->middleware(['auth', 'member'])->group(function () {
    Route::get('/dashboard', [Member\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [Member\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [Member\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Member\ProfileController::class, 'update'])->name('profile.update');

    // Membership
    Route::get('/membership', [Member\MembershipController::class, 'index'])->name('membership.index');

    // Services
    Route::get('/services/available', [Member\ServiceController::class, 'available'])->name('services.available');
    Route::post('/services/request', [Member\ServiceController::class, 'request'])->name('services.request');
    Route::get('/services/availed', [Member\ServiceController::class, 'availed'])->name('services.availed');
    Route::get('/services/{availedService}/edit', [Member\ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{availedService}', [Member\ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{availedService}', [Member\ServiceController::class, 'destroy'])->name('services.destroy');

    // Attendance
    Route::get('/attendance', [Member\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [Member\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [Member\AttendanceController::class, 'checkOut'])->name('attendance.check-out');

    // Payments
    Route::get('/payments', [Member\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [Member\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/process', [Member\PaymentController::class, 'processPayment'])->name('payments.process');
    Route::post('/payments/process-multiple', [Member\PaymentController::class, 'processMultiplePayments'])->name('payments.process-multiple');
    Route::get('/payments/{payment}/receipt', [Member\PaymentController::class, 'receipt'])->name('payments.receipt');
});

require __DIR__.'/auth.php';
