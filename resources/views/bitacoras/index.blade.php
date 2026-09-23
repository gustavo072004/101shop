<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="page-title">Bitácora de auditoría</h1>
                <p class="page-subtitle">Registro cronológico de actividades, modificaciones y operaciones del sistema.</p>
            </div>
            <div class="text-xs font-semibold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                Solo accesible por el Administrador
            </div>
        </div>
    </x-slot>

    {{-- Panel de Filtros --}}
    <div class="card-panel mb-6 p-5 sm:p-6">
        <form method="GET" action="{{ route('bitacora.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
            <div class="lg:col-span-2">
                <label for="q" class="label-form">Buscar</label>
                <input id="q" name="q" type="text" value="{{ $busqueda }}"
                       class="input-form" placeholder="Buscar por usuario, descripción o IP...">
            </div>

            <div>
                <label for="modulo" class="label-form">Módulo</label>
                <select id="modulo" name="modulo" class="input-form">
                    <option value="">Todos los módulos</option>
                    @foreach($modulosDisponibles as $m)
                        <option value="{{ $m }}" @selected($modulo === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="accion" class="label-form">Acción</label>
                <select id="accion" name="accion" class="input-form">
                    <option value="">Todas las acciones</option>
                    @foreach($accionesDisponibles as $a)
                        <option value="{{ $a }}" @selected($accion === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="fecha_desde" class="label-form">Desde</label>
                    <input id="fecha_desde" name="fecha_desde" type="date" value="{{ $fechaDesde }}" class="input-form">
                </div>
                <div class="flex-1">
                    <label for="fecha_hasta" class="label-form">Hasta</label>
                    <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ $fechaHasta }}" class="input-form">
                </div>
            </div>

            <div class="sm:col-span-2 lg:col-span-5 flex justify-end gap-2 pt-2 border-t border-slate-100">
                @if($busqueda !== '' || $modulo !== '' || $accion !== '' || $fechaDesde || $fechaHasta)
                    <a href="{{ route('bitacora.index') }}" class="btn-secondary-101 text-xs py-2">
                        Limpiar filtros
                    </a>
                @endif
                <button type="submit" class="btn-primary-101 text-xs py-2">
                    Filtrar resultados
                </button>
            </div>
        </form>
    </div>

    {{-- Tabla de Bitácora --}}
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="table-th">Fecha y hora</th>
                        <th class="table-th">Usuario</th>
                        <th class="table-th">Módulo</th>
                        <th class="table-th">Acción</th>
                        <th class="table-th">Descripción</th>
                        <th class="table-th">IP</th>
                        <th class="table-th text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacoras as $registro)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="table-td whitespace-nowrap text-xs font-medium text-slate-500">
                                {{ $registro->created_at->format('d/m/Y h:i A') }}
                            </td>
                            <td class="table-td whitespace-nowrap">
                                <span class="font-semibold text-slate-900 block text-xs sm:text-sm">
                                    {{ $registro->usuario_nombre }}
                                </span>
                                @if($registro->usuario)
                                    <span class="text-xs text-slate-400">
                                        {{ $registro->usuario->rol?->nombre }}
                                    </span>
                                @endif
                            </td>
                            <td class="table-td whitespace-nowrap">
                                <span class="inline-flex rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">
                                    {{ $registro->modulo }}
                                </span>
                            </td>
                            <td class="table-td whitespace-nowrap">
                                @php
                                    $accionEstilos = match($registro->accion) {
                                        'CREAR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'MODIFICAR' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'DAR DE BAJA' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'REACTIVAR' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'INICIO DE SESIÓN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'CIERRE DE SESIÓN' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'CAMBIO DE CONTRASEÑA', 'RECUPERACIÓN DE CONTRASEÑA' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $accionEstilos }}">
                                    {{ $registro->accion }}
                                </span>
                            </td>
                            <td class="table-td max-w-md text-xs sm:text-sm text-slate-700">
                                {{ $registro->descripcion }}
                            </td>
                            <td class="table-td whitespace-nowrap font-mono text-xs text-slate-400">
                                {{ $registro->ip ?? 'N/A' }}
                            </td>
                            <td class="table-td text-right whitespace-nowrap">
                                <a href="{{ route('bitacora.show', $registro->id_bitacora) }}"
                                   class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600 shadow-sm transition hover:border-blue-400 hover:text-blue-600">
                                    Ver detalle →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-base font-semibold text-slate-700">No se encontraron registros de auditoría</p>
                                <p class="mt-1 text-xs text-slate-400">Intente modificar los filtros o realice una nueva búsqueda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bitacoras->hasPages())
            <div class="border-t border-slate-100 px-4 py-3 sm:px-6">
                {{ $bitacoras->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
