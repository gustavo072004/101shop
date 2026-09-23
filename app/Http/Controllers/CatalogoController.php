<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => 'nullable|string|max:150', 'categoria' => 'nullable|integer|min:1']);
        $privado = $request->routeIs('catalogo.*');
        $query = Producto::with('categoria');
        if (! $privado) {
            $query->where('publicado', true)->where('estado', true)->whereHas('categoria', fn ($q) => $q->where('estado', true));
        }
        if ($search = trim($filters['q'] ?? '')) {
            $query->where(function ($q) use ($search) {
                foreach (['nombre', 'codigo', 'marca', 'descripcion'] as $field) {
                    $q->orWhereRaw('LOWER('.$field.') LIKE ?', ['%'.mb_strtolower($search).'%']);
                }
            });
        }
        if ($category = ($filters['categoria'] ?? null)) {
            $query->where('id_categoria', $category);
        }
        return view('catalogo.index', [
            'productos' => $query->orderBy('id_producto')->paginate(12)->withQueryString(),
            'categorias' => CategoriaProducto::when(! $privado, fn ($q) => $q->where('estado', true))->orderBy('nombre')->get(),
            'privado' => $privado,
        ]);
    }

    public function show(Request $request, Producto $producto): View
    {
        $privado = $request->routeIs('catalogo.*');
        $producto->load('categoria');
        abort_unless($privado || $producto->esPublico(), 404);
        return view('catalogo.show', compact('producto', 'privado'));
    }
    public function imagen(Request $request, Producto $producto)
    {
        abort_unless($producto->esPublico() || ($request->user()?->estado && ($producto->estado || $request->user()?->esAdministrador())), 404);
        if (!$producto->imagen_datos) return response()->file(public_path('images/producto.svg'));
        return response(base64_decode($producto->imagen_datos), 200, [
            'Content-Type'=>$producto->imagen_tipo,
            'X-Content-Type-Options'=>'nosniff',
            'Cache-Control'=>'private, no-store',
        ]);
    }
}
