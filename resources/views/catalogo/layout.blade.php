<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Catálogo') · 101 Shop</title>
    <link rel="stylesheet" href="{{ asset('css/catalogo.css') }}">
</head>
<body>
<header class="header"><div class="header-inner">
    <a class="brand" href="{{ route($privado ? 'catalogo.index' : 'publico.catalogo.index') }}"><span class="brand-icon">101</span><span><strong>101 Shop</strong><small>{{ $privado ? 'Catálogo privado' : 'Encuentra tu próximo producto' }}</small></span></a>
    <nav aria-label="Navegación principal"><a class="nav-active" href="{{ route($privado ? 'catalogo.index' : 'publico.catalogo.index') }}">Catálogo</a>
    @if($privado)<a href="{{ route('dashboard') }}">Panel privado</a><form method="POST" action="{{ route('logout') }}">@csrf<button>Cerrar sesión</button></form>@endif
    </nav>
</div></header>
<main class="container">@yield('content')</main>
<footer class="footer">101 Shop · {{ $privado ? 'Información interna · Acceso autorizado' : 'Consulta disponibilidad y condiciones por WhatsApp' }}</footer>
</body>
</html>
