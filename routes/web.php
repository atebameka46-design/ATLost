<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentReportController;
use App\Http\Controllers\DocumentSearchController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/marketplace', [DocumentSearchController::class, 'marketplace'])->name('marketplace');
Route::get('/documents/{documentReport}', [DocumentReportController::class, 'show'])->name('reports.show');

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = $user;

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'citizen.dashboard');
    }

    return view('welcome');
});
Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['fr', 'en'], true), 404);

    session()->put('locale', $locale);
    app()->setLocale($locale);

    return redirect()->back();
})->name('language.switch');
Route::post('/reports', [DocumentReportController::class, 'store'])->name('reports.store');

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
    Route::get('/notifications', [AuthController::class, 'notifications'])->name('notifications');
    Route::get('/assistant', [AiChatController::class, 'index'])->name('assistant');
    Route::post('/assistant', [AiChatController::class, 'message'])->name('assistant.message');
    Route::delete('/assistant', [AiChatController::class, 'reset'])->name('assistant.reset');
    Route::get('/notifications/{userNotification}', [AuthController::class, 'showNotification'])->name('notifications.show');
    Route::delete('/notifications/{userNotification}', [AuthController::class, 'deleteNotification'])->name('notifications.destroy');
    Route::view('/profile', 'profile')->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::get('/dashboard', function () {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'citizen.dashboard');
    })->name('dashboard');
    Route::get('/citizen/dashboard', [AuthController::class, 'citizenDashboard'])
        ->middleware('role:citizen')->name('citizen.dashboard');
    Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard'])
        ->middleware('role:admin')->name('admin.dashboard');
    Route::get('/admin/team', [AuthController::class, 'team'])
        ->middleware('role:admin')->name('admin.team');
    Route::patch('/admin/users/{user}/role', [AuthController::class, 'updateUserRole'])
        ->middleware('role:admin')->name('admin.users.update-role');
    Route::view('/admin/reports', 'dashboard.section', ['section' => 'reports'])
        ->middleware('role:admin')->name('admin.reports');
    Route::view('/admin/analytics', 'dashboard.section', ['section' => 'analytics'])
        ->middleware('role:admin')->name('admin.analytics');
    Route::get('/citizen/documents', [DocumentReportController::class, 'index'])
        ->middleware('role:citizen')->name('citizen.documents');
    Route::get('/citizen/documents/create', [DocumentReportController::class, 'create'])->middleware('role:citizen')->name('reports.create');
    Route::post('/documents/{documentReport}/claim', [DocumentReportController::class, 'claim'])->name('reports.claim');
    Route::post('/claims/{documentClaim}/accept', [DocumentReportController::class, 'acceptClaim'])->name('claims.accept');
    Route::patch('/claims/{documentClaim}/appointment', [DocumentReportController::class, 'appointment'])->name('claims.appointment');
    Route::post('/claims/{documentClaim}/pay', [DocumentReportController::class, 'pay'])->name('claims.pay');
    Route::delete('/claims/{documentClaim}', [DocumentReportController::class, 'cancelClaim'])->name('claims.cancel');
    Route::get('/citizen/documents/{documentReport}/edit', [DocumentReportController::class, 'edit'])->middleware('role:citizen')->name('reports.edit');
    Route::put('/citizen/documents/{documentReport}', [DocumentReportController::class, 'updateDetails'])->middleware('role:citizen')->name('reports.update-details');
    Route::delete('/reports/{documentReport}', [DocumentReportController::class, 'destroy'])->name('reports.destroy');
    Route::patch('/admin/reports/{documentReport}', [DocumentReportController::class, 'update'])->middleware('role:admin')->name('admin.reports.update');
    Route::get('/citizen/search', [DocumentSearchController::class, 'index'])->middleware('role:citizen')->name('citizen.search');
    Route::post('/citizen/search', [DocumentSearchController::class, 'store'])->middleware('role:citizen')->name('searches.store');
    Route::patch('/citizen/searches/{documentSearch}', [DocumentSearchController::class, 'update'])->middleware('role:citizen')->name('searches.update');
    Route::post('/citizen/search-alerts', [DocumentSearchController::class, 'alert'])->middleware('role:citizen')->name('search-alerts.store');
    Route::delete('/citizen/searches/{documentSearch}', [DocumentSearchController::class, 'destroy'])->middleware('role:citizen')->name('searches.destroy');
    Route::get('/citizen/activity', function () {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        $activity = collect()
            ->merge($user->documentReports()->latest()->get()->map(function ($report) {
                return [
                    'title' => $report->status === 'resolved' ? 'Document restitué' : 'Signalement de document',
                    'icon' => $report->status === 'resolved' ? 'fa-check' : 'fa-file-lines',
                    'description' => $report->document_type.' · '.$report->location,
                    'meta' => $report->statusLabel().' · '.$report->created_at->format('d/m/Y H:i'),
                    'link' => route('reports.show', $report),
                    'created_at' => $report->created_at->timestamp,
                ];
            }))
            ->merge($user->documentSearches()->latest()->get()->map(function ($search) {
                return [
                    'title' => 'Recherche sauvegardée',
                    'icon' => 'fa-magnifying-glass',
                    'description' => $search->query.($search->document_type ? ' · '.$search->document_type : ''),
                    'meta' => 'Recherche · '.$search->created_at->format('d/m/Y H:i'),
                    'link' => route('citizen.search'),
                    'created_at' => $search->created_at->timestamp,
                ];
            }))
            ->sortByDesc('created_at')
            ->values();

        return view('dashboard.section', [
            'section' => 'activity',
            'activityTimeline' => $activity,
        ]);
    })->middleware('role:citizen')->name('citizen.activity');
});
