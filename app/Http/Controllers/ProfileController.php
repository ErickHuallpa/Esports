<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
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
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $persona = $user->persona;
        // ✅ Todas las validaciones están en UpdateProfileRequest.
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
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Tus datos de perfil han sido actualizados correctamente.']);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Tu contraseña ha sido cambiada por seguridad.']);
        }
        return redirect()->back()->with('success', 'Tu contraseña ha sido cambiada por seguridad.');
    }
}