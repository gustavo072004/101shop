<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministradorInicialSeeder extends Seeder
{
    public function run(): void
    {
        $rol = Role::firstOrCreate(['nombre' => 'Administrador']);
        Role::firstOrCreate(['nombre' => 'Vendedor']);

        $email = config('inicial.admin_email');
        $password = config('inicial.admin_password');
        if (! $email || ! $password) {
            $this->command?->warn('Sin ADMIN_EMAIL / ADMIN_PASSWORD: no se crea un administrador.');
            return;
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
            throw new \RuntimeException('ADMIN_EMAIL debe ser válido y ADMIN_PASSWORD debe tener al menos 12 caracteres.');
        }
        User::firstOrCreate(
            ['email' => mb_strtolower(trim($email))],
            [
                'id_rol' => $rol->id_rol,
                'nombres' => 'Administrador',
                'apellidos' => 'Principal',
                'telefono' => '70000000',
                'username' => config('inicial.admin_username'),
                'password' => $password,
                'estado' => true,
            ]
        );

    }
}
