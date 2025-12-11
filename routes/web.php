<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ClassController;
use App\Http\Controllers\WaliKelas\IncomeController;
use App\Http\Controllers\WaliKelas\ReportController;
use App\Http\Controllers\WaliKelas\ExpenseController;
use App\Http\Controllers\WaliKelas\MyClassController;
use App\Http\Controllers\WaliKelas\ProfileController;
use App\Http\Controllers\SuperAdmin\StudentController;
use App\Http\Controllers\WaliKelas\DashboardController;
use App\Http\Controllers\Pengelola\IncomeRecapController;
use App\Http\Controllers\WaliKelas\DailyIncomeController;
use App\Http\Controllers\Pengelola\DisbursementController;
use App\Http\Controllers\Pengelola\ExpenseRecapController;
use App\Http\Controllers\Pengelola\ReportController as PengelolaReportController;
use App\Http\Controllers\Pengelola\ProfileController as PengelolaProfileController;
use App\Http\Controllers\SuperAdmin\ProfileController as SuperAdminProfileController;
use App\Http\Controllers\Pengelola\DashboardController as PengelolaDashboard;
use App\Http\Controllers\SuperAdmin\DashboardController as AdminDashboard;
use App\Http\Controllers\SuperAdmin\IncomeController as AdminIncome;
use App\Http\Controllers\SuperAdmin\ExpenseController as AdminExpense;
use App\Http\Controllers\SuperAdmin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\SuperAdmin\ReportController as AdminReport;

Route::get('/api/chart-data', [App\Http\Controllers\WelcomeController::class, 'chartData'])->name('api.chart');
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Dashboard umum setelah login
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    $user = Auth::user();
    
    if ($user->roles && $user->roles->isNotEmpty()) {
        $role = $user->roles->first()->code; // Ambil kode role

        switch ($role) {
            case 'super-admin':
                return redirect()->route('superadmin.dashboard');
            case 'guru': // Wali Kelas
                return redirect()->route('walikelas.dashboard');
            case 'pengelola': // REVISI: Tambah role 'pengelola'
                return redirect()->route('pengelola.dashboard');
            case 'kurikulum': // (Biarkan jika masih dipakai)
                // return redirect()->route('kurikulum.dashboard');
                 Auth::logout();
                 return redirect('/login')->with('error', 'Role Kurikulum belum memiliki dashboard.');
            default:
                Auth::logout();
                return redirect('/login')->with('error', 'Role Anda tidak valid.');
        }
    } else {
        Auth::logout();
        return redirect('/login')->with('error', 'Anda tidak memiliki peran/role yang ditetapkan.');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// Grup Route untuk Super Admin
Route::middleware(['auth', 'role:super-admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('students', StudentController::class);
    Route::get('profile', [SuperAdminProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [SuperAdminProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [SuperAdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/avatar', [SuperAdminProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('classes/{class}/students', [ClassController::class, 'addStudent'])->name('classes.addStudent');
    Route::delete('classes/{class}/students/{student}', [ClassController::class, 'removeStudent'])->name('classes.removeStudent');
    Route::resource('incomes', AdminIncome::class)->except(['show']);
    Route::resource('expenses', AdminExpense::class)->except(['show']);
    Route::resource('expenses', AdminExpenseController::class)->except(['show']);
    Route::get('reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::resource('disbursements', \App\Http\Controllers\Superadmin\DisbursementController::class);
    Route::get('disbursements/{disbursement}/pdf', [\App\Http\Controllers\SuperAdmin\DisbursementController::class, 'downloadPDF'])->name('disbursements.pdf');
});

// Grup Route untuk Wali Kelas
Route::middleware(['auth', 'role:guru'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-class', [MyClassController::class, 'show'])->name('my-class.show');
    Route::post('/my-class/add-student', [MyClassController::class, 'addStudent'])->name('my-class.add-student');
    Route::delete('/my-class/remove-student/{student}', [MyClassController::class, 'removeStudent'])->name('my-class.remove-student');
    Route::resource('incomes', IncomeController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'downloadPDF'])->name('reports.pdf');
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

// Grup Route untuk Pengelola Keuangan
Route::middleware(['auth', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    Route::get('/dashboard', [PengelolaDashboard::class, 'index'])->name('dashboard');
    Route::get('profile', [PengelolaProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [PengelolaProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [PengelolaProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('profile/avatar', [PengelolaProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('reports', [PengelolaReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [PengelolaReportController::class, 'downloadPDF'])->name('reports.pdf');
    Route::resource('disbursements', \App\Http\Controllers\Pengelola\DisbursementController::class);
    Route::get('disbursements/{disbursement}/pdf', [\App\Http\Controllers\Pengelola\DisbursementController::class, 'downloadPDF'])->name('disbursements.pdf');
    Route::get('income-recap', [IncomeRecapController::class, 'index'])->name('income-recap.index');
    Route::get('income-recap/pdf', [IncomeRecapController::class, 'downloadPDF'])->name('income-recap.pdf');
    Route::get('expense-recap', [ExpenseRecapController::class, 'index'])->name('expense-recap.index');
    Route::get('expense-recap/pdf', [ExpenseRecapController::class, 'downloadPDF'])->name('expense-recap.pdf');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';