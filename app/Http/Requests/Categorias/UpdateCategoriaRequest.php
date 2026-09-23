<?php

namespace App\Http\Requests\Categorias;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nombre' => preg_replace('/\s+/u', ' ', trim((string) $this->nombre)),
            'descripcion' => filled($this->descripcion)
                ? preg_replace('/\s+/u', ' ', trim((string) $this->descripcion))
                : null,
        ]);
    }

    public function rules(): array
    {
        $categoria = $this->route('categoria');
        $idCategoria = is_object($categoria) ? $categoria->id_categoria : $categoria;

        return [
            'nombre' => [
                'required', 'string', 'min:2', 'max:80',
                'regex:/^[\pL\pN][\pL\pN\sÁÉÍÓÚáéíóúÑñÜü&().,\/-]*$/u',
                function (string $attribute, mixed $value, \Closure $fail) use ($idCategoria) {
                    if (DB::table('categorias_producto')
                        ->whereRaw('LOWER(TRIM(nombre)) = LOWER(TRIM(?))', [$value])
                        ->where('id_categoria', '<>', $idCategoria)
                        ->exists()) {
                        $fail('Ya existe otra categoría con ese nombre.');
                    }
                },
            ],
            'descripcion' => ['nullable', 'string', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.min' => 'El nombre debe contener al menos 2 caracteres.',
            'nombre.max' => 'El nombre no puede superar los 80 caracteres.',
            'nombre.regex' => 'El nombre contiene caracteres no permitidos.',
            'descripcion.max' => 'La descripción no puede superar los 250 caracteres.',
        ];
    }
}
