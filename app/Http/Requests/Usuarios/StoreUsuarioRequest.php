<?php

namespace App\Http\Requests\Usuarios;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
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
        return [
            'id_rol' => ['required', 'integer', Rule::exists('roles', 'id_rol')],
            'nombres' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s\'\-]*$/u'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\pL][\pL\s\'\-]*$/u'],
            'telefono' => ['required', 'regex:/^[2567][0-9]{7}$/'],
            'email' => [
                'required', 'string', 'email:rfc', 'max:150',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (DB::table('usuarios')->whereRaw('LOWER(email) = LOWER(?)', [$value])->exists()) {
                        $fail('El correo electrónico ya está registrado.');
                    }
                },
            ],
            'username' => [
                'required', 'string', 'min:4', 'max:50', 'regex:/^[a-z0-9][a-z0-9._-]*$/',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (DB::table('usuarios')->whereRaw('LOWER(username) = LOWER(?)', [$value])->exists()) {
                        $fail('El nombre de usuario ya está registrado.');
                    }
                },
            ],
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:72',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id_rol.required' => 'Debe seleccionar un rol.',
            'id_rol.exists' => 'El rol seleccionado no es válido.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.regex' => 'Los nombres solo pueden contener letras, espacios, apóstrofes y guiones.',
            'nombres.min' => 'Los nombres deben tener al menos 2 caracteres.',
            'nombres.max' => 'Los nombres no pueden superar los 100 caracteres.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras, espacios, apóstrofes y guiones.',
            'apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres.',
            'apellidos.max' => 'Los apellidos no pueden superar los 100 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'Ingrese un número salvadoreño válido de 8 dígitos.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.min' => 'El nombre de usuario debe tener al menos 4 caracteres.',
            'username.max' => 'El nombre de usuario no puede superar los 50 caracteres.',
            'username.regex' => 'El usuario solo puede contener letras minúsculas, números, punto, guion y guion bajo.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede superar los 72 caracteres.',
            'password.regex' => 'La contraseña debe incluir mayúsculas, minúsculas y números.',
        ];
    }
}
