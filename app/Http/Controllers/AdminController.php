<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Muestra el formulario de registro inicial
    public function create()
    {
        // Bloqueo de seguridad: Si ya existe un administrador en el sistema, redirige al home
        $existeAdmin = User::whereHas('rol', function ($query) {
            $query->where('nombre', 'admin');
        })->exists();

        if ($existeAdmin) {
            return redirect('/')->with('error', 'El sistema ya ha sido configurado previamente.');
        }

        return view('admin.register');
    }

    // Procesa el registro e inserta en ambas tablas
    public function store(Request $request)
    {
        // Validamos todos los campos requeridos para Persona y User
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'required|string|max:20|unique:personas,ci',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'email' => 'required|string|email|max:150|unique:users,email',
            'username' => 'required|string|max:80|unique:users,username',
            'password' => 'required|string|min:6|confirmed', // Requiere campo password_confirmation
        ]);

        // Buscamos el rol de administrador en la tabla roles
        $rolAdmin = Rol::where('nombre', 'admin')->first();

        if (!$rolAdmin) {
            return back()->with('error', 'Error crítico: El rol "admin" no está registrado en la base de datos.');
        }

        // Iniciamos una transacción para asegurar la integridad de los datos
        DB::beginTransaction();

        try {
            // 1. Insertar en la tabla 'personas'
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);

            // 2. Insertar en la tabla 'users' enlazando la persona y el rol
            $user = User::create([
                'persona_id' => $persona->id,
                'rol_id' => $rolAdmin->id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password), // Encriptación obligatoria
                'activo' => true,
                'ultimo_login' => now(),
            ]);

            DB::commit();

            // Auto-loguear al administrador recién creado
            Auth::login($user);

            return redirect('/')->with('success', '¡Sistema configurado correctamente! Bienvenido, ' . $user->username);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al configurar el administrador: ' . $e->getMessage());
        }
    }
}