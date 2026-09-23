<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashLegacyPasswords extends Command
{
    protected $signature = 'auth:hash-legacy-passwords';

    protected $description = 'Convierte a hash las contraseñas antiguas que todavía estén almacenadas en texto plano';

    public function handle(): int
    {
        $actualizadas = 0;

        DB::table('usuarios')
            ->select(['id_usuario', 'password'])
            ->orderBy('id_usuario')
            ->chunkById(100, function ($usuarios) use (&$actualizadas) {
                foreach ($usuarios as $usuario) {
                    $password = (string) $usuario->password;
                    $info = password_get_info($password);

                    if (($info['algoName'] ?? 'unknown') !== 'unknown') {
                        continue;
                    }

                    DB::table('usuarios')
                        ->where('id_usuario', $usuario->id_usuario)
                        ->update([
                            'password' => Hash::make($password),
                            'updated_at' => now(),
                        ]);

                    $actualizadas++;
                }
            }, 'id_usuario');

        $this->info("Contraseñas convertidas a hash: {$actualizadas}");

        return self::SUCCESS;
    }
}
