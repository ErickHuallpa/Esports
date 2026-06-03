<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterAdminRequest;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
    public function store(RegisterAdminRequest $request)
    {
        // ✅ Todas las validaciones están en RegisterAdminRequest.
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

    public function manual()
    {
        abort_if(auth()->user()->rol->nombre !== 'admin', 403, 'No tienes permisos para ver esta página.');
        return view('admin.manual');
    }

    public function descargarManual()
    {
        abort_if(auth()->user()->rol->nombre !== 'admin', 403, 'No tienes permisos para ver esta página.');
        
        $pdf = Pdf::loadView('admin.manual_pdf')->setPaper('letter', 'portrait');
        return $pdf->download('Manual_Usuario_Master_ESports.pdf');
    }
}