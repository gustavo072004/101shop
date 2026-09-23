<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda    = trim((string) $request->input('q', ''));
        $modulo      = trim((string) $request->input('modulo', ''));
        $accion      = trim((string) $request->input('accion', ''));
        $fechaDesde  = $request->input('fecha_desde');
        $fechaHasta  = $request->input('fecha_hasta');

        $query = Bitacora::with('usuario')->orderByDesc('created_at');

        if ($busqueda !== '') {
            $term = '%' . mb_strtolower($busqueda) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(usuario_nombre) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(descripcion) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(COALESCE(ip, \'\')) LIKE ?', [$term]);
            });
        }

        if ($modulo !== '' && $modulo !== 'todos') {
            $query->where('modulo', $modulo);
        }

        if ($accion !== '' && $accion !== 'todas') {
            $query->where('accion', $accion);
        }

        if (! empty($fechaDesde)) {
            $query->whereDate('created_at', '>=', $fechaDesde);
        }

        if (! empty($fechaHasta)) {
            $query->whereDate('created_at', '<=', $fechaHasta);
        }

        $bitacoras = $query->paginate(20)->withQueryString();

        $modulosDisponibles = Bitacora::distinct()
            ->orderBy('modulo')
            ->pluck('modulo')
            ->filter()
            ->values();

        $accionesDisponibles = Bitacora::distinct()
            ->orderBy('accion')
            ->pluck('accion')
            ->filter()
            ->values();

        return view('bitacoras.index', compact(
            'bitacoras',
            'modulosDisponibles',
            'accionesDisponibles',
            'busqueda',
            'modulo',
            'accion',
            'fechaDesde',
            'fechaHasta'
        ));
    }

    public function show(Bitacora $bitacora): View
    {
        $bitacora->load('usuario.rol');

        return view('bitacoras.show', compact('bitacora'));
    }
}
