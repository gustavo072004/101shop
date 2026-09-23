<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->estado && $this->user()?->esAdministrador();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->codigo)) $this->merge(['codigo' => mb_strtoupper(trim($this->codigo))]);
        foreach (['nombre','marca','modelo'] as $field) {
            if (is_string($this->input($field))) $this->merge([$field => preg_replace('/\s+/u', ' ', trim($this->input($field)))]);
        }
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required','string','max:40','regex:/^[A-Z0-9][A-Z0-9._-]*$/',Rule::unique('productos','codigo')->ignore($this->route('producto'))],
            'nombre' => ['required','string','min:2','max:150'],
            'id_marca' => ['required','integer',Rule::exists('marcas','id_marca')->where('estado',true)],
            'modelo' => ['nullable','string','max:80'],
            'descripcion' => ['required','string','min:5','max:3000'],
            'id_categoria' => ['required','integer',Rule::exists('categorias_producto','id_categoria')->where('estado',true)],
            'stock' => ['required','integer','min:0','max:1000000'],
            'precio_ingreso' => ['required','numeric','decimal:0,2','min:0','max:9999999999.99'],
            'valor_real' => ['required','numeric','decimal:0,2','min:0','max:9999999999.99'],
            'valor_final' => ['required','numeric','decimal:0,2','min:0','max:9999999999.99'],
            'publicado' => ['required','boolean'],
            'imagen' => ['nullable','file','image','mimes:jpg,jpeg,png,webp','max:2048','dimensions:max_width=5000,max_height=5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'codigo.regex' => 'Use letras, números, puntos, guiones o guion bajo en el código.',
            'id_categoria.exists' => 'Seleccione una categoría activa.',
            'stock.integer' => 'Las existencias deben ser un número entero.',
            '*.min' => 'El valor de :attribute no es válido.',
            'numeric' => 'Ingrese un importe numérico válido.',
            'decimal' => 'El importe debe tener como máximo dos decimales.',
            'imagen.max' => 'La imagen no puede superar 2 MB.',
            'imagen.mimes' => 'Seleccione una imagen JPG, PNG o WebP.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
        ];
    }

    public function attributes(): array
    {
        return ['codigo'=>'código','id_categoria'=>'categoría','stock'=>'existencias','precio_ingreso'=>'precio de ingreso','valor_real'=>'valor real','valor_final'=>'valor final','descripcion'=>'descripción'];
    }
}
