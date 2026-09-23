<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('bitacora.index') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-700 mb-1">
                    ← Volver a la bitácora
                </a>
                <h1 class="page-title">Detalle de auditoría #{{ $bitacora->id_bitacora }}</h1>
                <p class="page-subtitle">Información técnica registrada para este evento.</p>
            </div>
            <div>
                @php
                    $accionEstilos = match($bitacora->accion) {
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
                <span class="inline-flex rounded-full border px-3 py-1 text-sm font-bold {{ $accionEstilos }}">
                    {{ $bitacora->accion }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Ficha de Resumen del Evento --}}
        <div class="card-panel p-6 lg:col-span-1 space-y-4">
            <h2 class="text-base font-bold text-[#0b1f3a] border-b border-slate-100 pb-3">Información del evento</h2>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha y hora</p>
                <p class="mt-1 text-sm font-semibold text-slate-800">
                    {{ $bitacora->created_at->format('d/m/Y h:i:s A') }}
                </p>
                <p class="text-xs text-slate-400">({{ $bitacora->created_at->diffForHumans() }})</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Usuario responsable</p>
                <p class="mt-1 text-sm font-bold text-slate-900">{{ $bitacora->usuario_nombre }}</p>
                @if($bitacora->usuario)
                    <p class="text-xs text-blue-600 font-medium">Rol: {{ $bitacora->usuario->rol?->nombre }}</p>
                @endif
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Módulo</p>
                <span class="mt-1 inline-flex rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                    {{ $bitacora->modulo }}
                </span>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Descripción</p>
                <p class="mt-1 text-sm text-slate-700 leading-relaxed">{{ $bitacora->descripcion }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dirección IP</p>
                <p class="mt-1 font-mono text-xs text-slate-700">{{ $bitacora->ip ?? 'No registrada' }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Navegador / Cliente</p>
                <p class="mt-1 text-xs text-slate-500 break-words leading-snug">{{ $bitacora->user_agent ?? 'No disponible' }}</p>
            </div>
        </div>

        {{-- Detalle de los datos modificados / creados --}}
        <div class="card-panel p-6 lg:col-span-2 space-y-6">
            <h2 class="text-base font-bold text-[#0b1f3a] border-b border-slate-100 pb-3">Detalle de valores y cambios</h2>

            @if(isset($bitacora->detalles['antes']) && isset($bitacora->detalles['despues']))
                {{-- Caso Modificación con Antes y Después --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left font-bold text-slate-600 pb-2">Campo</th>
                                <th class="text-left font-bold text-rose-600 pb-2">Valor anterior</th>
                                <th class="text-left font-bold text-emerald-600 pb-2">Valor nuevo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bitacora->detalles['despues'] as $campo => $nuevoValor)
                                @php
                                    $anteriorValor = $bitacora->detalles['antes'][$campo] ?? null;
                                    $haCambiado = $anteriorValor !== $nuevoValor;
                                @endphp
                                <tr class="{{ $haCambiado ? 'bg-amber-50/40' : '' }}">
                                    <td class="py-2.5 font-semibold text-slate-800">
                                        {{ ucfirst(str_replace('_', ' ', $campo)) }}
                                    </td>
                                    <td class="py-2.5 font-mono text-xs text-rose-700">
                                        @if(is_bool($anteriorValor))
                                            {{ $anteriorValor ? 'true (activo)' : 'false (inactivo)' }}
                                        @elseif(is_null($anteriorValor) || $anteriorValor === '')
                                            <span class="text-slate-400 italic">nulo / vacío</span>
                                        @else
                                            {{ is_array($anteriorValor) ? json_encode($anteriorValor) : $anteriorValor }}
                                        @endif
                                    </td>
                                    <td class="py-2.5 font-mono text-xs text-emerald-700">
                                        @if(is_bool($nuevoValor))
                                            {{ $nuevoValor ? 'true (activo)' : 'false (inactivo)' }}
                                        @elseif(is_null($nuevoValor) || $nuevoValor === '')
                                            <span class="text-slate-400 italic">nulo / vacío</span>
                                        @else
                                            {{ is_array($nuevoValor) ? json_encode($nuevoValor) : $nuevoValor }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(!empty($bitacora->detalles))
                {{-- Caso Creación, Baja u otros datos estructurados --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left font-bold text-slate-600 pb-2">Propiedad</th>
                                <th class="text-left font-bold text-slate-600 pb-2">Valor registrado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($bitacora->detalles as $clave => $valor)
                                <tr>
                                    <td class="py-2.5 font-semibold text-slate-800">
                                        {{ ucfirst(str_replace('_', ' ', $clave)) }}
                                    </td>
                                    <td class="py-2.5 font-mono text-xs text-slate-700">
                                        @if(is_bool($valor))
                                            {{ $valor ? 'true' : 'false' }}
                                        @elseif(is_null($valor) || $valor === '')
                                            <span class="text-slate-400 italic">nulo / vacío</span>
                                        @else
                                            {{ is_array($valor) ? json_encode($valor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $valor }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-500 italic">Este evento no cuenta con datos adicionales estructurados.</p>
            @endif

            {{-- JSON crudo para depuración / auditoría técnica --}}
            @if(!empty($bitacora->detalles))
                <details class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <summary class="cursor-pointer text-xs font-bold text-slate-600 hover:text-slate-800">
                        Ver carga útil JSON cruda
                    </summary>
                    <pre class="mt-2 overflow-x-auto rounded bg-slate-900 p-3 text-xs text-emerald-400 font-mono">{{ json_encode($bitacora->detalles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </details>
            @endif
        </div>
    </div>
</x-app-layout>
