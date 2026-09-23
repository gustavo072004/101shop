@extends('catalogo.layout')
@section('content')
<section class="hero"><div><p class="eyebrow">101 SHOP / CATÁLOGO {{ $privado ? 'PRIVADO' : 'PÚBLICO' }}</p><h1>Encuentra el producto<br>que va contigo.</h1><p>Explora, compara y consulta. Tu próxima elección empieza aquí.</p></div><span class="hero-number">101<span>Una nueva forma de elegir.</span></span></section>
<section class="catalog-section" aria-labelledby="catalog-title">
<div class="section-heading"><div><h2 id="catalog-title">Nuestros productos</h2><p>{{ $productos->total() }} {{ $productos->total() === 1 ? 'resultado' : 'resultados' }} disponibles en el catálogo</p></div></div>
<form class="filters" method="GET" action="{{ route($privado ? 'catalogo.index' : 'publico.catalogo.index') }}">
    <div class="search-field"><label for="q">Buscar producto</label><input id="q" name="q" type="search" maxlength="150" placeholder="Nombre, marca o código…" value="{{ request('q') }}"></div>
    <div><label for="categoria">Categoría</label><select id="categoria" name="categoria"><option value="">Todas las categorías</option>@foreach($categorias as $categoria)<option value="{{ $categoria->id_categoria }}" @selected((string)request('categoria') === (string)$categoria->id_categoria)>{{ $categoria->nombre }}</option>@endforeach</select></div>
    <button type="submit" class="search-button">Buscar</button><a class="clear" href="{{ route($privado ? 'catalogo.index' : 'publico.catalogo.index') }}">Limpiar</a>
</form>
@if($errors->any())<p role="alert">{{ $errors->first() }}</p>@endif
<div class="grid">
@forelse($productos as $producto)
<article class="card">
    <a class="image-link" href="{{ route($privado ? 'catalogo.show' : 'publico.catalogo.show', $producto) }}"><img src="{{ route('productos.imagen',$producto) }}" alt="{{ $producto->nombre }} {{ $producto->modelo }}" loading="lazy"><span class="year">{{ $producto->modelo }}</span></a>
    <div class="card-body"><p class="category">{{ $producto->categoria->nombre }}</p><h3><a href="{{ route($privado ? 'catalogo.show' : 'publico.catalogo.show', $producto) }}">{{ $producto->nombre }}</a></h3><p class="code">Código {{ $producto->codigo }}</p><p class="specs">{{ $producto->descripcion }}</p><div class="price-line"><strong class="price">$ {{ number_format($producto->valor_final, 2) }}</strong><span class="status">{{ $producto->stock > 0 ? 'Disponible' : 'Agotado' }}</span></div>
    @if($privado && auth()->user()->esAdministrador())<p class="internal">Ingreso: $ {{ ($producto->precio_ingreso === null ? 'Pendiente' : number_format($producto->precio_ingreso,2)) }} · Real: $ {{ ($producto->valor_real === null ? 'Pendiente' : number_format($producto->valor_real,2)) }}</p>@endif
    <a class="detail-link" href="{{ route($privado ? 'catalogo.show' : 'publico.catalogo.show', $producto) }}">Ver detalles <span aria-hidden="true">↗</span></a>@include('catalogo.whatsapp')
    </div>
</article>
@empty
<div class="empty"><h3>No encontramos productos</h3><p>Prueba con otro nombre, marca o categoría.</p></div>
@endforelse
</div>
@if($productos->hasPages())<nav class="pagination" aria-label="Páginas del catálogo">@if($productos->previousPageUrl())<a href="{{ $productos->previousPageUrl() }}">← Anterior</a>@endif<span>Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>@if($productos->nextPageUrl())<a href="{{ $productos->nextPageUrl() }}">Siguiente →</a>@endif</nav>@endif
</section>
@endsection
