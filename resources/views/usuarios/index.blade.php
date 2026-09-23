<x-app-layout>

    <x-slot name="header">

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
        >

            <div>

                <h1 class="page-title">
                    Personal operativo
                </h1>

                <p class="page-subtitle">
                    Registro, actualización, baja y
                    reactivación de colaboradores de 101 Shop.
                </p>

            </div>

            <a
                href="{{ route('usuarios.create') }}"
                class="btn-primary-101"
            >
                Registrar colaborador
            </a>

        </div>

    </x-slot>

    <div class="mb-5 flex flex-wrap gap-2">

        <a
            href="{{
                route(
                    'usuarios.index',
                    ['estado' => 'activos']
                )
            }}"
            class="{{
                $estado === 'activos'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Activos
        </a>

        <a
            href="{{
                route(
                    'usuarios.index',
                    ['estado' => 'inactivos']
                )
            }}"
            class="{{
                $estado === 'inactivos'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Inactivos
        </a>

        <a
            href="{{
                route(
                    'usuarios.index',
                    ['estado' => 'todos']
                )
            }}"
            class="{{
                $estado === 'todos'
                    ? 'btn-primary-101'
                    : 'btn-secondary-101'
            }}"
        >
            Todos
        </a>

    </div>

    <div class="card-panel overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr>

                        <th class="table-th">
                            Colaborador
                        </th>

                        <th class="table-th">
                            Contacto
                        </th>

                        <th class="table-th">
                            Usuario
                        </th>

                        <th class="table-th">
                            Rol
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

                    @forelse($usuarios as $usuario)

                        <tr class="hover:bg-slate-50/70">

                            <td
                                class="table-td font-semibold text-slate-900"
                            >
                                {{ $usuario->nombre_completo }}
                            </td>

                            <td class="table-td">

                                <p>
                                    {{
                                        substr(
                                            $usuario->telefono,
                                            0,
                                            4
                                        )
                                    }}-{{
                                        substr(
                                            $usuario->telefono,
                                            4
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-0.5 text-xs text-slate-500"
                                >
                                    {{ $usuario->email }}
                                </p>

                            </td>

                            <td
                                class="table-td font-medium"
                            >
                                {{ $usuario->username }}
                            </td>

                            <td class="table-td">
                                {{ $usuario->rol?->nombre }}
                            </td>

                            <td class="table-td">

                                <span
                                    class="{{
                                        $usuario->estado
                                            ? 'badge-active'
                                            : 'badge-inactive'
                                    }}"
                                >

                                    {{
                                        $usuario->estado
                                            ? 'Activo'
                                            : 'Inactivo'
                                    }}

                                </span>

                            </td>

                            <td class="table-td">

                                <div
                                    class="flex justify-end gap-2"
                                >

                                    @if($usuario->estado)

                                        <a
                                            href="{{
                                                route(
                                                    'usuarios.edit',
                                                    $usuario
                                                )
                                            }}"
                                            class="btn-secondary-101 !px-3 !py-2 text-xs"
                                        >
                                            Editar
                                        </a>

                                        @if(
                                            auth()->id()
                                            !== $usuario->id_usuario
                                        )

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'usuarios.destroy',
                                                        $usuario
                                                    )
                                                }}"
                                                class="js-confirm-action"
                                                data-title="Dar de baja colaborador"
                                                data-message="¿Está seguro de dar de baja a {{ $usuario->nombre_completo }}?"
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

                                            <span
                                                class="self-center text-xs font-medium text-slate-400"
                                            >
                                                Sesión actual
                                            </span>

                                        @endif

                                    @else

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'usuarios.reactivar',
                                                    $usuario
                                                )
                                            }}"
                                            class="js-confirm-action"
                                            data-title="Reactivar colaborador"
                                            data-message="¿Desea reactivar a {{ $usuario->nombre_completo }}?"
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
                                colspan="6"
                                class="px-6 py-12 text-center text-sm text-slate-500"
                            >
                                No hay colaboradores para mostrar.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>