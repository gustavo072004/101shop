<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        BitacoraService::registrar(
            accion: 'INICIO DE SESIÓN',
            modulo: 'Autenticación',
            descripcion: 'Inicio de sesión exitoso'
        );

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $usuario = $request->user();
        if ($usuario) {
            BitacoraService::registrar(
                accion: 'CIERRE DE SESIÓN',
                modulo: 'Autenticación',
                descripcion: 'Cierre de sesión de usuario',
                idUsuario: $usuario->id_usuario,
                usuarioNombre: trim($usuario->nombre_completo . ' (' . $usuario->username . ')')
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
