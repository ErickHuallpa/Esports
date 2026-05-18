<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    // Mostrar formulario de registro para compradores
    public function create()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('cliente.register');
    }

    // Procesar el registro transaccional de la Persona y el Usuario Cliente
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'nullable|string|max:20|unique:personas,ci',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'email' => 'required|string|email|max:150|unique:users,email',
            'username' => 'required|string|max:80|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $rolCliente = Rol::where('nombre', 'cliente')->first();

        if (!$rolCliente) {
            return back()->with('error', 'Error: El rol "cliente" no se encuentra inicializado en la base de datos.');
        }

        DB::beginTransaction();

        try {
            // 1. Crear registro civil en personas
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
            ]);

            // 2. Crear credenciales de acceso vinculadas al rol cliente
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

            // Iniciar sesión automáticamente
            Auth::login($user);

            return redirect('/')->with('success', '¡Cuenta creada con éxito! Bienvenido a E-Sports.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error en el registro: ' . $e->getMessage());
        }
    }
}