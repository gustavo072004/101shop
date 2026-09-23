<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 150);
            $table->string('marca', 80)->nullable();
            $table->string('modelo', 80)->nullable();
            $table->text('descripcion');
            $table->unsignedBigInteger('id_categoria');
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('precio_ingreso', 12, 2)->nullable();
            $table->decimal('valor_real', 12, 2)->nullable();
            $table->decimal('valor_final', 12, 2);
            $table->boolean('estado')->default(true);
            $table->boolean('publicado')->default(false)->index();
            $table->text('imagen_datos')->nullable();
            $table->string('imagen_tipo', 30)->nullable();
            $table->timestamps();
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias_producto');
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
