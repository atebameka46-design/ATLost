<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', fn (string $token) => view('auth.reset-password', ['token' => $token]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::view('/profile', 'profile')->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/dashboard', fn () => auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('citizen.dashboard'))->name('dashboard');
    Route::get('/citizen/dashboard', [AuthController::class, 'citizenDashboard'])
        ->middleware('role:citizen')->name('citizen.dashboard');
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])
        ->middleware('role:admin')->name('admin.dashboard');
    Route::view('/citizen/documents', 'dashboard.section', ['section' => 'documents'])
        ->middleware('role:citizen')->name('citizen.documents');
    Route::view('/citizen/search', 'dashboard.section', ['section' => 'search'])
        ->middleware('role:citizen')->name('citizen.search');
    Route::view('/citizen/activity', 'dashboard.section', ['section' => 'activity'])
        ->middleware('role:citizen')->name('citizen.activity');
    Route::view('/admin/reports', 'dashboard.section', ['section' => 'reports'])
        ->middleware('role:admin')->name('admin.reports');
    Route::view('/admin/analytics', 'dashboard.section', ['section' => 'analytics'])
        ->middleware('role:admin')->name('admin.analytics');
    Route::view('/admin/team', 'dashboard.section', ['section' => 'team'])
        ->middleware('role:admin')->name('admin.team');
});
