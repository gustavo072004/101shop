<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '101 Shop') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    @include('layouts.navigation')
    <div class="mx-auto flex min-h-[calc(100vh-64px)] max-w-[1600px]">
        <aside class="hidden w-64 shrink-0 border-r border-slate-200 bg-white lg:block">
            <nav class="sticky top-16 space-y-1 p-4">
                <p class="px-3 pb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Menú principal</p>
                <a href="{{ route('catalogo.index') }}" class="menu-link">Catálogo privado</a>
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'menu-link-active' : '' }}">Inicio</a>
                @if(auth()->user()->esAdministrador())<a class="menu-link" href="{{ route('marcas.index') }}">Marcas</a><a href="{{ route('productos.index') }}" class="menu-link">Productos</a>
                    <a href="{{ route('categorias.index') }}" class="menu-link {{ request()->routeIs('categorias.*') ? 'menu-link-active' : '' }}">Categorías</a>
                    <a href="{{ route('usuarios.index') }}" class="menu-link {{ request()->routeIs('usuarios.*') ? 'menu-link-active' : '' }}">Personal operativo</a>
                    <a href="{{ route('bitacora.index') }}" class="menu-link {{ request()->routeIs('bitacora.*') ? 'menu-link-active' : '' }}">Bitácora de auditoría</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="menu-link {{ request()->routeIs('profile.*') ? 'menu-link-active' : '' }}">Mi perfil</a>
            </nav>
        </aside>
        <main class="min-w-0 flex-1 p-4 sm:p-6 lg:p-8">
            @if (isset($header))<div class="mb-6">{{ $header }}</div>@endif
            <x-flash />
            {{ $slot }}
        </main>
    </div>
</body>
</html>
