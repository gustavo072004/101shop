<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // El proyecto utiliza "usuarios" y "roles", no la tabla "users"
        // estándar de Breeze. Si la BD importada ya existe, esta migración
        // respeta sus datos y solamente completa lo necesario para autenticación.
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id_rol');
                $table->string('nombre', 50)->unique();
            });

            DB::table('roles')->insert([
                ['nombre' => 'Administrador'],
                ['nombre' => 'Vendedor'],
            ]);
        }

        if (! Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->bigIncrements('id_usuario');
                $table->unsignedBigInteger('id_rol');
                $table->string('nombres', 100);
                $table->string('apellidos', 100);
                $table->string('telefono', 8);
                $table->string('username', 50);
                $table->string('email', 150);
                $table->string('password', 255);
                $table->rememberToken();
                $table->boolean('estado')->default(true);
                $table->timestamps();

                $table->foreign('id_rol')->references('id_rol')->on('roles');
            });

            DB::statement('CREATE UNIQUE INDEX uq_usuarios_email_ci ON usuarios (LOWER(TRIM(email)))');
            DB::statement('CREATE UNIQUE INDEX uq_usuarios_username_ci ON usuarios (LOWER(TRIM(username)))');
        } elseif (! Schema::hasColumn('usuarios', 'remember_token')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        // No eliminamos tablas de negocio existentes al hacer rollback.
    }
};
