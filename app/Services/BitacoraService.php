<?php

namespace App\Services;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BitacoraService
{
    /**
     * Registra un evento en la bitácora de auditoría del sistema.
     *
     * @param string $accion Tipo de acción (CREAR, MODIFICAR, DAR DE BAJA, REACTIVAR, INICIO DE SESIÓN, etc.)
     * @param string $modulo Módulo afectado (Categorías, Personal Operativo, Perfil, Autenticación)
     * @param string $descripcion Resumen legible para humanos
     * @param array|null $detalles Atributos anteriores y nuevos, o metadatos de la operación
     * @param int|null $idUsuario ID opcional de usuario si no se toma de la sesión actual
     * @param string|null $usuarioNombre Nombre histórico opcional
     */
    public static function registrar(
        string $accion,
        string $modulo,
        string $descripcion,
        ?array $detalles = null,
        ?int $idUsuario = null,
        ?string $usuarioNombre = null
    ): ?Bitacora {
        try {
            /** @var User|null $usuario */
            $usuario = Auth::user();

            $idUsuarioFinal = $idUsuario ?? $usuario?->id_usuario;

            if ($usuarioNombre !== null) {
                $nombreFinal = $usuarioNombre;
            } elseif ($usuario) {
                $nombreFinal = trim($usuario->nombre_completo . ' (' . $usuario->username . ')');
            } else {
                $nombreFinal = 'Sistema';
            }

            return Bitacora::create([
                'id_usuario'     => $idUsuarioFinal,
                'usuario_nombre' => $nombreFinal,
                'accion'         => strtoupper(trim($accion)),
                'modulo'         => trim($modulo),
                'descripcion'    => trim($descripcion),
                'detalles'       => $detalles,
                'ip'             => request()?->ip(),
                'user_agent'     => request()?->userAgent(),
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            // Un fallo en la bitácora nunca debe detener la operación principal del usuario
            Log::warning('No se pudo registrar en bitácora: ' . $e->getMessage(), [
                'accion'      => $accion,
                'modulo'      => $modulo,
                'descripcion' => $descripcion,
            ]);

            return null;
        }
    }
}
