<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuarios\StoreUsuarioRequest;
use App\Http\Requests\Usuarios\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(
        Request $request
    ): View {
        $estado = $request->string('estado')->toString();

        if (! in_array(
            $estado,
            ['activos', 'inactivos', 'todos'],
            true
        )) {
            $estado = 'activos';
        }

        $usuarios = User::with('rol')
            ->when(
                $estado === 'activos',
                fn ($query) =>
                    $query->where('estado', true)
            )
            ->when(
                $estado === 'inactivos',
                fn ($query) =>
                    $query->where('estado', false)
            )
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return view(
            'usuarios.index',
            compact('usuarios', 'estado')
        );
    }

    public function create(): View
    {
        $roles = Role::orderBy('nombre')->get();

        return view(
            'usuarios.create',
            compact('roles')
        );
    }

    public function store(
        StoreUsuarioRequest $request
    ): RedirectResponse {
        $usuario = null;
        DB::transaction(function () use ($request, &$usuario) {
            $usuario = User::create([
                ...$request
                    ->safe()
                    ->except(['password_confirmation']),

                'estado' => true,
            ]);
        });

        if ($usuario) {
            BitacoraService::registrar(
                accion: 'CREAR',
                modulo: 'Personal Operativo',
                descripcion: "Se registró el colaborador '{$usuario->nombre_completo}' ({$usuario->username})",
                detalles: [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre'     => $usuario->nombre_completo,
                    'username'   => $usuario->username,
                    'email'      => $usuario->email,
                    'telefono'   => $usuario->telefono,
                    'id_rol'     => $usuario->id_rol,
                ]
            );
        }

        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Colaborador registrado correctamente.'
            );
    }

    public function edit(
        User $usuario
    ): View|RedirectResponse {
        if (! $usuario->estado) {
            return redirect()
                ->route(
                    'usuarios.index',
                    ['estado' => 'inactivos']
                )
                ->with(
                    'error',
                    'No se puede modificar un colaborador que se encuentra inactivo.'
                );
        }

        $roles = Role::orderBy('nombre')->get();

        return view(
            'usuarios.edit',
            compact('usuario', 'roles')
        );
    }

    public function update(
        UpdateUsuarioRequest $request,
        User $usuario
    ): RedirectResponse {
        if (! $usuario->estado) {
            return redirect()
                ->route(
                    'usuarios.index',
                    ['estado' => 'inactivos']
                )
                ->with(
                    'error',
                    'No se puede modificar un colaborador que se encuentra inactivo.'
                );
        }

        $datos = $request
            ->safe()
            ->except(['password_confirmation']);

        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        /*
        |--------------------------------------------------------------------------
        | Protección del usuario autenticado
        |--------------------------------------------------------------------------
        |
        | El administrador que está utilizando el sistema no puede cambiarse
        | accidentalmente su propio rol desde este módulo.
        |
        */

        if (
            $request->user()->id_usuario
            === $usuario->id_usuario
        ) {
            $datos['id_rol'] = $usuario->id_rol;
        }

        $antes = [
            'nombres'         => $usuario->nombres,
            'apellidos'       => $usuario->apellidos,
            'telefono'        => $usuario->telefono,
            'email'           => $usuario->email,
            'username'        => $usuario->username,
            'id_rol'          => $usuario->id_rol,
            'cambio_password' => isset($datos['password']),
        ];

        DB::transaction(function () use (
            $usuario,
            $datos
        ) {
            $usuario->update($datos);
        });

        $despues = [
            'nombres'         => $usuario->nombres,
            'apellidos'       => $usuario->apellidos,
            'telefono'        => $usuario->telefono,
            'email'           => $usuario->email,
            'username'        => $usuario->username,
            'id_rol'          => $usuario->id_rol,
            'cambio_password' => isset($datos['password']),
        ];

        BitacoraService::registrar(
            accion: 'MODIFICAR',
            modulo: 'Personal Operativo',
            descripcion: "Se modificó el colaborador '{$usuario->nombre_completo}' ({$usuario->username})",
            detalles: [
                'id_usuario' => $usuario->id_usuario,
                'antes'      => $antes,
                'despues'    => $despues,
            ]
        );

        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Información del colaborador actualizada correctamente.'
            );
    }

    public function destroy(
        Request $request,
        User $usuario
    ): RedirectResponse {
        if (! $usuario->estado) {
            return redirect()
                ->route(
                    'usuarios.index',
                    ['estado' => 'inactivos']
                )
                ->with(
                    'error',
                    'El colaborador ya se encuentra dado de baja.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | No permitir darse de baja a sí mismo
        |--------------------------------------------------------------------------
        */

        if (
            $request->user()->id_usuario
            === $usuario->id_usuario
        ) {
            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'No puede darse de baja a sí mismo mientras tiene una sesión iniciada.'
                );
        }

        $usuario->update([
            'estado' => false,
        ]);

        BitacoraService::registrar(
            accion: 'DAR DE BAJA',
            modulo: 'Personal Operativo',
            descripcion: "Se dio de baja al colaborador '{$usuario->nombre_completo}' ({$usuario->username})",
            detalles: [
                'id_usuario' => $usuario->id_usuario,
                'username'   => $usuario->username,
            ]
        );

        return redirect()
            ->route(
                'usuarios.index',
                ['estado' => 'inactivos']
            )
            ->with(
                'success',
                'Colaborador dado de baja correctamente.'
            );
    }

    public function reactivar(
        User $usuario
    ): RedirectResponse {
        if ($usuario->estado) {
            return redirect()
                ->route('usuarios.index')
                ->with(
                    'error',
                    'El colaborador ya se encuentra activo.'
                );
        }

        $usuario->update([
            'estado' => true,
        ]);

        BitacoraService::registrar(
            accion: 'REACTIVAR',
            modulo: 'Personal Operativo',
            descripcion: "Se reactivó al colaborador '{$usuario->nombre_completo}' ({$usuario->username})",
            detalles: [
                'id_usuario' => $usuario->id_usuario,
                'username'   => $usuario->username,
            ]
        );

        return redirect()
            ->route(
                'usuarios.index',
                ['estado' => 'activos']
            )
            ->with(
                'success',
                'Colaborador reactivado correctamente.'
            );
    }
}