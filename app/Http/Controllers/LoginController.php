<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();

            if (!$user->activo) {
                Auth::logout();
                return back()->with('error', 'Su cuenta se encuentra suspendida temporalmente. Contacte a soporte.');
            }

            $user->update([
                'ultimo_login' => now()
            ]);

            $request->session()->regenerate();

            // RESTAURACIÓN DEL CARRITO: Si el usuario tenía un carrito guardado, lo devolvemos a la sesión
            if (Cache::has('carrito_user_' . $user->id)) {
                $carritoRestaurado = Cache::get('carrito_user_' . $user->id);
                session()->put('carrito', $carritoRestaurado);
            }

            return redirect()->intended('/')->with('success', '¡Bienvenido de vuelta, ' . $user->username . '!');
        }

        return back()->withErrors([
            'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }
}