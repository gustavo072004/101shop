<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()->load('rol')]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $antes = [
            'nombres'   => $user->nombres,
            'apellidos' => $user->apellidos,
            'telefono'  => $user->telefono,
            'email'     => $user->email,
            'username'  => $user->username,
        ];

        $user->update($request->validated());

        $despues = [
            'nombres'   => $user->nombres,
            'apellidos' => $user->apellidos,
            'telefono'  => $user->telefono,
            'email'     => $user->email,
            'username'  => $user->username,
        ];

        BitacoraService::registrar(
            accion: 'MODIFICAR',
            modulo: 'Perfil',
            descripcion: "El usuario actualizó su información de perfil",
            detalles: [
                'id_usuario' => $user->id_usuario,
                'antes'      => $antes,
                'despues'    => $despues,
            ]
        );

        return Redirect::route('profile.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}
