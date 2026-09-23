<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categorias_producto')) {
            Schema::create('categorias_producto', function (Blueprint $table) {
                $table->bigIncrements('id_categoria');
                $table->string('nombre', 80);
                $table->string('descripcion', 250)->nullable();
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_producto');
    }
};
