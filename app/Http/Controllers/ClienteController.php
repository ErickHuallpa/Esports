<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterClienteRequest;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('cliente.register');
    }
    public function store(RegisterClienteRequest $request)
    {
        // ✅ Todas las validaciones están en RegisterClienteRequest.
        // Los datos ya vienen sanitizados (nombres capitalizados, email en minúscula, CI en mayúscula).
        $rolCliente = Rol::where('nombre', 'cliente')->first();
        if (!$rolCliente) {
            return back()->with('error', 'Error: El rol "cliente" no se encuentra inicializado en la base de datos.');
        }
        DB::beginTransaction();
        try {
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
            ]);
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
            Auth::login($user);
            return redirect('/')->with('success', '¡Cuenta creada con éxito! Bienvenido a E-Sports.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error en el registro: ' . $e->getMessage());
        }
    }
}