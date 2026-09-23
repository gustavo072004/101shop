<nav x-data="{ open: false }" class="sticky top-0 z-40 h-16 border-b border-blue-950/20 bg-[#0b1f3a] text-white shadow-sm">
    <div class="mx-auto flex h-full max-w-[1600px] items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-500 text-sm font-black">101</span>
            <div class="leading-tight"><div class="text-lg font-bold tracking-wide">101 Shop</div><div class="text-xs text-blue-200">Sistema de gestión</div></div>
        </a>
        <div class="hidden items-center gap-4 sm:flex">
            <div class="text-right leading-tight"><p class="text-sm font-semibold">{{ auth()->user()->nombre_completo }}</p><p class="text-xs text-blue-200">{{ auth()->user()->rol?->nombre }}</p></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-lg border border-white/20 px-3 py-2 text-sm font-semibold transition hover:bg-white/10">Cerrar sesión</button></form>
        </div>
        <button @click="open = !open" class="rounded-lg p-2 hover:bg-white/10 sm:hidden" aria-label="Abrir menú"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg></button>
    </div>
    <div x-show="open" x-cloak class="border-t border-white/10 bg-[#0b1f3a] px-4 py-3 sm:hidden">
        <a href="{{ route('dashboard') }}" class="mobile-menu-link">Inicio</a>
        <a href="{{ route('catalogo.index') }}" class="mobile-menu-link">Catálogo privado</a>
        @if(auth()->user()->esAdministrador())<a class="mobile-menu-link" href="{{ route('marcas.index') }}">Marcas</a><a href="{{ route('productos.index') }}" class="mobile-menu-link">Productos</a><a href="{{ route('categorias.index') }}" class="mobile-menu-link">Categorías</a><a href="{{ route('usuarios.index') }}" class="mobile-menu-link">Personal operativo</a><a href="{{ route('bitacora.index') }}" class="mobile-menu-link">Bitácora de auditoría</a>@endif
        <a href="{{ route('profile.edit') }}" class="mobile-menu-link">Mi perfil</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="mobile-menu-link w-full text-left">Cerrar sesión</button></form>
    </div>
</nav>
