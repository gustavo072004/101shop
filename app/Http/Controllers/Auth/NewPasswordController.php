<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\BitacoraService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email:rfc', 'max:150'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'max:72',
                Rules\Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'password.required' => 'Ingrese la nueva contraseña.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.max' => 'La contraseña no puede superar los 72 caracteres.',
        ]);

        $status = Password::reset(
            [
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
                'estado' => true,
            ],
            function ($user) use ($request) {
                // El cast "hashed" del modelo User aplica el hash de forma segura.
                $user->forceFill([
                    'password' => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            BitacoraService::registrar(
                accion: 'RECUPERACIÓN DE CONTRASEÑA',
                modulo: 'Autenticación',
                descripcion: "Restablecimiento de contraseña mediante token para correo {$request->email}"
            );

            return redirect()
                ->route('login')
                ->with('status', 'Contraseña restablecida correctamente. Ya puede iniciar sesión.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'El enlace es inválido, venció o la cuenta ya no está activa. Solicite uno nuevo.',
            ]);
    }
}
