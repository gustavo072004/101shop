<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categorias\StoreCategoriaRequest;
use App\Http\Requests\Categorias\UpdateCategoriaRequest;
use App\Models\CategoriaProducto;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaProductoController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->string('estado')->toString();

        if (! in_array($estado, ['activas', 'inactivas', 'todas'], true)) {
            $estado = 'activas';
        }

        $categorias = CategoriaProducto::query()
            ->when(
                $estado === 'activas',
                fn ($query) => $query->where('estado', true)
            )
            ->when(
                $estado === 'inactivas',
                fn ($query) => $query->where('estado', false)
            )
            ->orderBy('nombre')
            ->get();

        return view(
            'categorias.index',
            compact('categorias', 'estado')
        );
    }

    public function create(): View
    {
        return view('categorias.create');
    }

    public function store(
        StoreCategoriaRequest $request
    ): RedirectResponse {
        $categoria = CategoriaProducto::create([
            ...$request->validated(),
            'estado' => true,
        ]);

        BitacoraService::registrar(
            accion: 'CREAR',
            modulo: 'Categorías',
            descripcion: "Se registró la categoría '{$categoria->nombre}'",
            detalles: [
                'id_categoria' => $categoria->id_categoria,
                'nombre'       => $categoria->nombre,
                'descripcion'  => $categoria->descripcion,
            ]
        );

        return redirect()
            ->route('categorias.index')
            ->with(
                'success',
                'Categoría registrada correctamente.'
            );
    }

    public function edit(
        CategoriaProducto $categoria
    ): View|RedirectResponse {
        if (! $categoria->estado) {
            return redirect()
                ->route(
                    'categorias.index',
                    ['estado' => 'inactivas']
                )
                ->with(
                    'error',
                    'No se puede modificar una categoría que se encuentra inactiva.'
                );
        }

        return view(
            'categorias.edit',
            compact('categoria')
        );
    }

    public function update(
        UpdateCategoriaRequest $request,
        CategoriaProducto $categoria
    ): RedirectResponse {
        if (! $categoria->estado) {
            return redirect()
                ->route(
                    'categorias.index',
                    ['estado' => 'inactivas']
                )
                ->with(
                    'error',
                    'No se puede modificar una categoría que se encuentra inactiva.'
                );
        }

        $antes = [
            'nombre'      => $categoria->nombre,
            'descripcion' => $categoria->descripcion,
        ];

        $categoria->update(
            $request->validated()
        );

        $despues = [
            'nombre'      => $categoria->nombre,
            'descripcion' => $categoria->descripcion,
        ];

        BitacoraService::registrar(
            accion: 'MODIFICAR',
            modulo: 'Categorías',
            descripcion: "Se modificó la categoría '{$categoria->nombre}'",
            detalles: [
                'id_categoria' => $categoria->id_categoria,
                'antes'        => $antes,
                'despues'      => $despues,
            ]
        );

        return redirect()
            ->route('categorias.index')
            ->with(
                'success',
                'Categoría actualizada correctamente.'
            );
    }

    public function destroy(
        CategoriaProducto $categoria
    ): RedirectResponse {
        if (! $categoria->estado) {
            return redirect()
                ->route(
                    'categorias.index',
                    ['estado' => 'inactivas']
                )
                ->with(
                    'error',
                    'La categoría ya se encuentra dada de baja.'
                );
        }

        $categoria->update([
            'estado' => false,
        ]);

        BitacoraService::registrar(
            accion: 'DAR DE BAJA',
            modulo: 'Categorías',
            descripcion: "Se dio de baja la categoría '{$categoria->nombre}'",
            detalles: [
                'id_categoria' => $categoria->id_categoria,
                'nombre'       => $categoria->nombre,
            ]
        );

        return redirect()
            ->route(
                'categorias.index',
                ['estado' => 'inactivas']
            )
            ->with(
                'success',
                'Categoría dada de baja correctamente.'
            );
    }

    public function reactivar(
        CategoriaProducto $categoria
    ): RedirectResponse {
        if ($categoria->estado) {
            return redirect()
                ->route('categorias.index')
                ->with(
                    'error',
                    'La categoría ya se encuentra activa.'
                );
        }

        $categoria->update([
            'estado' => true,
        ]);

        BitacoraService::registrar(
            accion: 'REACTIVAR',
            modulo: 'Categorías',
            descripcion: "Se reactivó la categoría '{$categoria->nombre}'",
            detalles: [
                'id_categoria' => $categoria->id_categoria,
                'nombre'       => $categoria->nombre,
            ]
        );

        return redirect()
            ->route(
                'categorias.index',
                ['estado' => 'activas']
            )
            ->with(
                'success',
                'Categoría reactivada correctamente.'
            );
    }
}