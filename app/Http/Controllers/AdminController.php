<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\User;
use App\Models\Administrador;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function create()
    {
        if (Administrador::exists()) {
            return redirect('/')->with('error', 'El administrador principal ya ha sido registrado.');
        }

        return view('admin.register');
    }
    public function store(Request $request)
    {
        if (Administrador::exists()) {
            return redirect('/');
        }
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'required|string|max:20|unique:personas',
            'email' => 'required|string|email|max:150|unique:users',
            'username' => 'required|string|max:80|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        DB::beginTransaction();

        try {
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
            ]);
            $user = User::create([
                'persona_id' => $persona->id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'tipo_usuario' => 'admin',
                'activo' => true,
            ]);
            Administrador::create([
                'usuario_id' => $user->id,
                'departamento' => 'Gerencia General',
                'nivel_acceso' => 'total',
            ]);
            DB::commit();

            return redirect('/')->with('success', 'Administrador principal configurado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al registrar el administrador: ' . $e->getMessage());
        }
    }
}