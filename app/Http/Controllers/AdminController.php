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
    public function create()
    {
        $existeAdmin = User::whereHas('rol', function ($query) {
            $query->where('nombre', 'admin');
        })->exists();
        if ($existeAdmin) {
            return redirect('/')->with('error', 'El sistema ya ha sido configurado previamente.');
        }
        return view('admin.register');
    }
    public function store(Request $request)
    {
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
        $rolAdmin = Rol::where('nombre', 'admin')->first();
        if (!$rolAdmin) {
            return back()->with('error', 'Error crítico: El rol "admin" no está registrado en la base de datos.');
        }
        DB::beginTransaction();
        try {
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);
            $user = User::create([
                'persona_id' => $persona->id,
                'rol_id' => $rolAdmin->id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'activo' => true,
                'ultimo_login' => now(),
            ]);
            DB::commit();
            Auth::login($user);
            return redirect('/')->with('success', '¡Sistema configurado correctamente! Bienvenido, ' . $user->username);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al configurar el administrador: ' . $e->getMessage());
        }
    }
}