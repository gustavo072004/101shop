<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bitacoras')) {
            Schema::create('bitacoras', function (Blueprint $table) {
                $table->bigIncrements('id_bitacora');
                $table->unsignedBigInteger('id_usuario')->nullable();
                $table->string('usuario_nombre', 150);
                $table->string('accion', 50);
                $table->string('modulo', 50);
                $table->string('descripcion', 255);
                $table->json('detalles')->nullable();
                $table->string('ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('id_usuario')
                    ->references('id_usuario')
                    ->on('usuarios')
                    ->nullOnDelete();
            });

            // Índices para optimizar filtros y ordenación de la bitácora
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bitacoras_created_at ON bitacoras (created_at DESC)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bitacoras_modulo ON bitacoras (modulo)');
            DB::statement('CREATE INDEX IF NOT EXISTS idx_bitacoras_accion ON bitacoras (accion)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacoras');
    }
};
