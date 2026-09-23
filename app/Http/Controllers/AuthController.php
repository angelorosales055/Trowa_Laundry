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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

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
