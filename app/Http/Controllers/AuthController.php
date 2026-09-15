<?php

namespace App\Http\Controllers;

use App\Models\DocumentReport;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([...$data, 'role' => 'citizen']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('citizen.dashboard')->with('status', 'Bienvenue sur ATLost.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors(['email' => 'Ces identifiants ne correspondent pas à nos données.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        return redirect()->intended($user->isAdmin()
            ? route('admin.dashboard')
            : route('citizen.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Vous êtes déconnecté.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
        ]);
        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        $request->user()->update($data);

        return back()->with('status', 'Votre profil a été mis à jour.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate(['current_password' => ['required'], 'password' => ['required', 'min:8', 'confirmed']]);
        abort_unless(Hash::check($data['current_password'], $request->user()->password), 422, 'Mot de passe actuel incorrect.');
        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Votre mot de passe a été modifié.');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return back()->with('status', __($status));
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset($data, function (User $user, string $password): void {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function citizenDashboard(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        $reportCount = $user->documentReports()->count();
        $resolvedCount = $user->documentReports()->where('status', 'resolved')->count();
        $pendingCount = $user->documentReports()->whereIn('status', ['pending', 'approved'])->count();
        $searchCount = $user->documentSearches()->count();
        $alertCount = $user->notifications()->whereNull('read_at')->count();

        return view('dashboard.citizen', [
            'reportCount' => $reportCount,
            'resolvedCount' => $resolvedCount,
            'pendingCount' => $pendingCount,
            'searchCount' => $searchCount,
            'alertCount' => $alertCount,
            'notifications' => $user->notifications()->latest()->limit(2)->get(),
            'recentReports' => $user->documentReports()->latest()->limit(5)->get(),
            'documentList' => $user->documentReports()->latest()->limit(3)->get(),
            'progressPercent' => $reportCount > 0 ? (int) round(($resolvedCount / $reportCount) * 100) : 0,
        ]);
    }

    public function adminDashboard(): View
    {
        $pendingCount = DocumentReport::where('status', 'pending')->count();
        $approvedCount = DocumentReport::where('status', 'approved')->count();
        $resolvedCount = DocumentReport::where('status', 'resolved')->count();
        $citizenCount = User::where('role', 'citizen')->count();
        $totalReports = DocumentReport::count();

        return view('dashboard.admin', [
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'toProcessCount' => $pendingCount + $approvedCount,
            'resolvedCount' => $resolvedCount,
            'citizenCount' => $citizenCount,
            'recentReports' => DocumentReport::latest()->limit(5)->get(),
            'returns48hPercent' => $totalReports > 0 ? (int) round(($resolvedCount / $totalReports) * 100) : 0,
        ]);
    }

    public function notifications(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        /** @var User $user */
        $user = $user;

        $notifications = $user->notifications()->latest()->get();
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return view('dashboard.notifications', compact('notifications'));
    }

    public function deleteNotification(UserNotification $userNotification): RedirectResponse
    {
        abort_unless($userNotification->user_id === Auth::id(), 403);
        $userNotification->delete();

        return back()->with('status', 'Notification supprimée.');
    }

    public function showNotification(UserNotification $userNotification): View
    {
        abort_unless($userNotification->user_id === Auth::id(), 403);
        $userNotification->update(['read_at' => $userNotification->read_at ?: now()]);
        if (! $userNotification->action_url && preg_match('/#(\d+)/', $userNotification->message, $matches)) {
            $userNotification->action_url = route('reports.show', (int) $matches[1]);
        }

        return view('dashboard.notification-show', compact('userNotification'));
    }

    public function team(): View
    {
        $users = User::query()->latest()->get();

        return view('dashboard.section', [
            'section' => 'team',
            'users' => $users,
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        abort_if($request->user()?->id === $user->id, 422, 'Vous ne pouvez pas modifier votre propre rôle.');

        $data = $request->validate([
            'role' => ['required', 'in:citizen,admin'],
        ]);

        $user->update(['role' => $data['role']]);

        return redirect()->route('admin.team')->with('status', 'Le rôle de l’utilisateur a été mis à jour.');
    }
}
