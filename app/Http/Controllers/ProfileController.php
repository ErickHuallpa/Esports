<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load('persona');
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = auth()->user();
        $persona = $user->persona;
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => ['required', 'string', 'max:20', Rule::unique('personas')->ignore($persona->id)],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            
            $rutaImagen = $request->file('foto_perfil')->store('perfiles', 'public');
            $user->foto_perfil = $rutaImagen;
        }
        $persona->update([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
        ]);
        $user->update([
            'username' => $request->username,
            'email' => $request->email,
        ]);
        return redirect()->back()->with('success', 'Tus datos de perfil han sido actualizados correctamente.');
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no coincide con nuestros registros.']);
        }
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return redirect()->back()->with('success', 'Tu contraseña ha sido cambiada por seguridad.');
    }
}