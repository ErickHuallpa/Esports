<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'activo' => true])) {
            $request->session()->regenerate();
                        $user = Auth::user();
            $user->ultimo_login = now();
            $user->save();

            return redirect()->intended('/')->with('success', 'Bienvenido de nuevo, ' . $user->username);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden o la cuenta está inactiva.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}