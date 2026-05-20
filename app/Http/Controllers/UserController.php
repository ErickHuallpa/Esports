<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Mostrar lista de usuarios en Cards con filtro inteligente
    public function index(Request $request)
    {
        $query = User::with(['persona', 'rol'])->orderBy('id', 'desc');

        // Lógica del Filtro Inteligente
        if ($request->has('filtro_rol')) {
            if ($request->filtro_rol === 'todos') {
                // No aplicamos ningún where, mostramos todos
            } elseif (is_numeric($request->filtro_rol)) {
                // Si seleccionó un rol específico
                $query->where('rol_id', $request->filtro_rol);
            }
        } else {
            // COMPORTAMIENTO POR DEFECTO: Solo Personal y Cajeros
            $query->whereHas('rol', function($q) {
                $q->whereIn('nombre', ['personal', 'cajero']);
            });
        }

        $usuarios = $query->get();
        $roles = Rol::all(); 

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    // Registrar un nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'nullable|string|max:20|unique:personas,ci',
            'telefono' => 'nullable|string|max:20',
            'rol_id' => 'required|exists:roles,id',
            'email' => 'required|string|email|max:150|unique:users,email',
            'username' => 'required|string|max:80|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();
        try {
            $persona = Persona::create($request->only(['nombre', 'apellidos', 'ci', 'telefono']));

            User::create([
                'persona_id' => $persona->id,
                'rol_id' => $request->rol_id,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'activo' => true,
            ]);

            DB::commit();
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear usuario: ' . $e->getMessage())->withInput();
        }
    }

    // Actualizar datos del usuario
    public function update(Request $request, $id)
    {
        $user = User::with('persona')->findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'nullable|string|max:20|unique:personas,ci,' . $user->persona->id,
            'telefono' => 'nullable|string|max:20',
            'rol_id' => 'required|exists:roles,id',
            'email' => 'required|string|email|max:150|unique:users,email,' . $user->id,
            'username' => 'required|string|max:80|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6', // Contraseña opcional al editar
        ]);

        DB::beginTransaction();
        try {
            $user->persona->update($request->only(['nombre', 'apellidos', 'ci', 'telefono']));

            $userData = [
                'rol_id' => $request->rol_id,
                'email' => $request->email,
                'username' => $request->username,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            DB::commit();
            return back()->with('success', 'Datos del usuario actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // Cambiar estado Activo / Inactivo
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Medida de seguridad: Un administrador no puede darse de baja a sí mismo
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Acción denegada: No puedes dar de baja tu propia cuenta activa.');
        }

        $user->update(['activo' => !$user->activo]);
        
        $msg = $user->activo ? 'El usuario ha sido reactivado.' : 'El usuario fue dado de baja del sistema.';
        return back()->with('success', $msg);
    }
}