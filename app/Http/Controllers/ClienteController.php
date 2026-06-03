<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterClienteRequest;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login'); // Muestra la vista compartida de login/registro
    }

    public function store(RegisterClienteRequest $request)
    {
        // ✅ Si el código llega aquí, los datos pasaron las validaciones SÚPER ESTRICTAS.

        $rolCliente = Rol::where('nombre', 'cliente')->first();
        
        if (!$rolCliente) {
            return back()->withInput()->with('error_toast', 'Error del sistema: El rol "cliente" no está configurado.');
        }

        DB::beginTransaction();
        try {
            // Se registra la Persona
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'direccion' => null, // Puedes actualizar esto si agregas el campo en el form
            ]);

            // Se registra el User ligado a la Persona
            $user = User::create([
                'persona_id' => $persona->id,
                'rol_id' => $rolCliente->id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'activo' => true,
                'ultimo_login' => now(),
            ]);

            DB::commit();
            
            // Autenticamos al usuario inmediatamente
            Auth::login($user);
            
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

            return redirect()->route('home')->with('success_toast', '¡Cuenta creada con éxito! Bienvenido a E-Sports, ' . $user->nombre . '.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Error de Base de Datos en registro: ' . $e->getMessage());
            return back()->withInput()->with('error_toast', 'Error interno al registrar tu cuenta. El soporte técnico ya ha sido notificado.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error general en registro: ' . $e->getMessage());
            return back()->withInput()->with('error_toast', 'Ocurrió un error inesperado. Inténtalo de nuevo más tarde.');
        }
    }

    public function validarUnico(\Illuminate\Http\Request $request)
    {
        $errores = [];

        if ($request->has('username')) {
            $username = trim($request->username);
            if (User::where('username', $username)->exists()) {
                $errores['username'] = 'Este usuario ya está en uso.';
            }
        }

        if ($request->has('email')) {
            $email = strtolower(trim($request->email));
            if (User::where('email', $email)->exists()) {
                $errores['email'] = 'Este correo ya está registrado.';
            }
        }

        if ($request->has('ci')) {
            $ci = strtoupper(trim($request->ci));
            if (Persona::where('ci', $ci)->exists()) {
                $errores['ci'] = 'Este C.I. ya está registrado.';
            }
        }

        if ($request->has('telefono') && !empty($request->telefono)) {
            $telefono = trim($request->telefono);
            if (Persona::where('telefono', $telefono)->exists()) {
                $errores['telefono'] = 'Este teléfono ya está registrado.';
            }
        }

        return response()->json([
            'valido' => empty($errores),
            'errores' => $errores
        ]);
    }
}