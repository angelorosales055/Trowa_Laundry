<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function showLogin(): \Illuminate\View\View
    {
        // Using a single-file view name for the prototype
        return view('auth_login');
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        $loginInput = trim($credentials['username']);
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $loginInput, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'customer') {
                if (! Auth::user()->email_verified_at) {
                    session(['pending_verification_user_id' => Auth::id()]);
                    return redirect()->route('customer.verify');
                }
                return redirect()->route('customer.portal');
            }

            return Redirect::intended('/dashboard');
        }

        return back()->withErrors(['username' => 'The provided credentials do not match our records.'])->withInput();
    }

    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
