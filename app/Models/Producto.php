<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $primaryKey = 'id_producto';
    protected $fillable = ['id_marca', 'codigo', 'nombre', 'marca', 'modelo', 'descripcion', 'id_categoria', 'stock', 'precio_ingreso', 'valor_real', 'valor_final', 'estado', 'publicado', 'imagen_datos', 'imagen_tipo'];
    protected $hidden = ['precio_ingreso', 'valor_real', 'valor_final', 'imagen_datos', 'imagen_tipo'];
    protected $casts = ['estado'=>'boolean', 'publicado'=>'boolean', 'stock'=>'integer', 'precio_ingreso'=>'decimal:2', 'valor_real'=>'decimal:2', 'valor_final'=>'decimal:2'];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaProducto::class, 'id_categoria', 'id_categoria');
    }

    public function esPublico(): bool
    {
        return $this->estado && $this->publicado && $this->categoria?->estado;
    }

    public function whatsappUrl(): ?string
    {
        $phone = preg_replace('/\D/', '', (string) config('catalogo.whatsapp'));
        if (! preg_match('/^[1-9][0-9]{7,14}$/', $phone)) return null;
        $message = "Hola, buen día. Estoy interesado en el producto {$this->nombre}, código {$this->codigo}. Me gustaría recibir más información sobre su disponibilidad, precio y condiciones. Gracias.";
        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }
}
