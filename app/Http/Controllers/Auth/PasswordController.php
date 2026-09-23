<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:72',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
            ],
        ], [
            'current_password.required' => 'Ingrese su contraseña actual.',
            'current_password.current_password' => 'La contraseña actual es incorrecta.',
            'password.required' => 'Ingrese la nueva contraseña.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La nueva contraseña no puede superar los 72 caracteres.',
            'password.regex' => 'La nueva contraseña debe incluir mayúsculas, minúsculas y números.',
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        BitacoraService::registrar(
            accion: 'CAMBIO DE CONTRASEÑA',
            modulo: 'Perfil',
            descripcion: 'El usuario actualizó su contraseña de acceso',
            detalles: ['cambio_password' => true]
        );

        return back()->with('status', 'password-updated');
    }
}
