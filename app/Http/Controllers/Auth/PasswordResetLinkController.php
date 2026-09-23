<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => ['required', 'string', 'email:rfc', 'max:150'],
        ], [
            'email.required' => 'Ingrese el correo electrónico registrado.',
            'email.email' => 'Ingrese un correo electrónico válido.',
        ]);

        // "estado" forma parte de las credenciales del Password Broker para
        // impedir recuperación de cuentas dadas de baja.
        $status = Password::sendResetLink([
            'email' => $request->email,
            'estado' => true,
        ]);

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Ya se solicitó un enlace recientemente. Espere un minuto e intente de nuevo.',
                ]);
        }

        // Respuesta intencionalmente genérica: no revela si un correo existe.
        return back()->with(
            'status',
            'Si el correo pertenece a una cuenta activa, recibirá un enlace para restablecer la contraseña.'
        );
    }
}
