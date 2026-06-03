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
        ], [
            'login.required' => 'Debes ingresar tu usuario o correo electrónico.',
            'password.required' => 'La contraseña es obligatoria.',
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
                return back()->withInput()->with('error_toast', 'Su cuenta se encuentra suspendida temporalmente. Contacte a soporte.');
            }

            $user->update([
                'ultimo_login' => now()
            ]);

            $request->session()->regenerate();

            if (Cache::has('carrito_user_' . $user->id)) {
                $carritoRestaurado = Cache::get('carrito_user_' . $user->id);
                session()->put('carrito', $carritoRestaurado);
            }

            // Comprobar fortaleza de contraseña para notificar en el home
            $pass = $request->password;
            $score = 0;
            if (strlen($pass) >= 8) $score++;
            if (preg_match('/[A-Z]/', $pass)) $score++;
            if (preg_match('/[0-9]/', $pass)) $score++;
            if (preg_match('/[@#$!%*?&_.\-+=^]/', $pass)) $score++;
            
            if ($score < 3) {
                session()->put('weak_password_notice', true);
            }

            return redirect()->intended('/')->with('success_toast', '¡Bienvenido de vuelta, ' . $user->username . '!');
        }

        return back()
            ->withInput($request->except('password'))
            ->with('error_toast', 'Las credenciales proporcionadas no son correctas.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')->with('info_toast', 'Sesión cerrada correctamente. ¡Vuelve pronto!');
    }
}