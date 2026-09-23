<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombres' => preg_replace('/\s+/u', ' ', trim((string) $this->nombres)),
            'apellidos' => preg_replace('/\s+/u', ' ', trim((string) $this->apellidos)),
            'telefono' => preg_replace('/\D/', '', (string) $this->telefono),
            'email' => mb_strtolower(trim((string) $this->email)),
            'username' => mb_strtolower(trim((string) $this->username)),
        ]);
    }

    public function rules(): array
    {
        $idUsuario = $this->user()->id_usuario;

        return [
            'nombres' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s\'\-]*$/u'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s\'\-]*$/u'],
            'telefono' => ['required', 'regex:/^[2567][0-9]{7}$/'],
            'email' => [
                'required', 'string', 'email:rfc', 'max:150',
                function (string $attribute, mixed $value, \Closure $fail) use ($idUsuario) {
                    if (DB::table('usuarios')->whereRaw('LOWER(email) = LOWER(?)', [$value])->where('id_usuario', '<>', $idUsuario)->exists()) {
                        $fail('El correo electrónico ya está registrado por otro usuario.');
                    }
                },
            ],
            'username' => [
                'required', 'string', 'min:4', 'max:50', 'regex:/^[a-z0-9][a-z0-9._-]*$/',
                function (string $attribute, mixed $value, \Closure $fail) use ($idUsuario) {
                    if (DB::table('usuarios')->whereRaw('LOWER(username) = LOWER(?)', [$value])->where('id_usuario', '<>', $idUsuario)->exists()) {
                        $fail('El nombre de usuario ya está registrado por otro usuario.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.regex' => 'Los nombres solo pueden contener letras, espacios, apóstrofes y guiones.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras, espacios, apóstrofes y guiones.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'Ingrese un número salvadoreño válido de 8 dígitos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.regex' => 'El usuario solo puede contener letras minúsculas, números, punto, guion y guion bajo.',
        ];
    }
}
