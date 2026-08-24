<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(Request $request) {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email'=>'The credentials do not match our records.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard.home'));
    }

    public function register(Request $request) {
        $data = $request->validate([
            'name'=>'required|string|max:120',
            'email'=>'required|email|max:190|unique:users,email',
            'password'=>'required|string|min:8|confirmed',
        ]);
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('dashboard.home');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}