<x-app-layout>

    <x-slot name="header">

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >

            <div>

                <h1 class="page-title">
                    Categorías de productos
                </h1>

                <p class="page-subtitle">
                    Registro, modificación, baja y consulta
                    de categorías del catálogo.
                </p>

            </div>

            <a
                href="{{ route('categorias.create') }}"
                class="btn-primary-101"
            >
                Nueva categoría
            </a>

        </div>

    </x-slot>

    <div class="mb-5 flex flex-wrap gap-2">

        <a
            href="{{ route(
                'categorias.index',
                ['estado' => 'activas']
            ) }}"
            class="{{
                $estado === 'activas'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Activas
        </a>

        <a
            href="{{ route(
                'categorias.index',
                ['estado' => 'inactivas']
            ) }}"
            class="{{
                $estado === 'inactivas'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Inactivas
        </a>

        <a
            href="{{ route(
                'categorias.index',
                ['estado' => 'todas']
            ) }}"
            class="{{
                $estado === 'todas'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Todas
        </a>

    </div>

    <div class="card-panel overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr>

                        <th class="table-th">
                            Nombre
                        </th>

                        <th class="table-th">
                            Descripción
                        </th>

                        <th class="table-th">
                            Estado
                        </th>

                        <th class="table-th text-right">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($categorias as $categoria)

                        <tr class="hover:bg-slate-50/70">

                            <td
                                class="table-td font-semibold text-slate-900"
                            >
                                {{ $categoria->nombre }}
                            </td>

                            <td class="table-td max-w-xl">

                                {{
                                    $categoria->descripcion
                                        ?: 'Sin descripción'
                                }}

                            </td>

                            <td class="table-td">

                                <span
                                    class="{{
                                        $categoria->estado
                                            ? 'badge-active'
                                            : 'badge-inactive'
                                    }}"
                                >

                                    {{
                                        $categoria->estado
                                            ? 'Activa'
                                            : 'Inactiva'
                                    }}

                                </span>

                            </td>

                            <td class="table-td">

                                <div
                                    class="flex justify-end gap-2"
                                >

                                    @if($categoria->estado)

                                        <a
                                            href="{{
                                                route(
                                                    'categorias.edit',
                                                    $categoria
                                                )
                                            }}"
                                            class="btn-secondary-101 !px-3 !py-2 text-xs"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'categorias.destroy',
                                                    $categoria
                                                )
                                            }}"
                                            class="js-confirm-action"
                                            data-title="Dar de baja categoría"
                                            data-message="¿Está seguro de dar de baja la categoría {{ $categoria->nombre }}?"
                                            data-confirm-text="Sí, dar de baja"
                                            data-icon="warning"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn-danger-101"
                                            >
                                                Dar de baja
                                            </button>

                                        </form>

                                    @else

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'categorias.reactivar',
                                                    $categoria
                                                )
                                            }}"
                                            class="js-confirm-action"
                                            data-title="Reactivar categoría"
                                            data-message="¿Desea reactivar la categoría {{ $categoria->nombre }}?"
                                            data-confirm-text="Sí, reactivar"
                                            data-icon="question"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn-primary-101 !px-3 !py-2 text-xs"
                                            >
                                                Reactivar
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="px-6 py-12 text-center text-sm text-slate-500"
                            >
                                No hay categorías para mostrar.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>