<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'is_active' => true,
        ];

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => __('auth.active_user_not_found'),
            ]);
        }

        $user = Auth::user();

        if (! $user->role->canLogin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withInput($request->only('email'))->withErrors([
                'email' => __('auth.washer_login_forbidden'),
            ]);
        }

        $request->session()->regenerate();

        if ($user->locale) {
            $request->session()->put('locale', $user->locale);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
