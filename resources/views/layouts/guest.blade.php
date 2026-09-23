<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '101 Shop') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
<div class="grid min-h-screen lg:grid-cols-2">
<section class="hidden bg-[#0b1f3a] p-12 text-white lg:flex lg:flex-col lg:justify-between">
<div class="flex items-center gap-4"><span class="grid h-14 w-14 place-items-center rounded-2xl bg-blue-500 text-lg font-black">101</span><div><h1 class="text-2xl font-bold">101 Shop</h1><p class="text-sm text-blue-200">San Vicente, El Salvador</p></div></div>
<div class="max-w-xl"><p class="mb-4 text-sm font-bold uppercase tracking-[0.2em] text-blue-300">Gestión empresarial</p><h2 class="text-4xl font-bold leading-tight">Control organizado para las operaciones de 101 Shop.</h2><p class="mt-5 max-w-lg text-base leading-7 text-blue-100">Acceso seguro para el personal autorizado del negocio.</p></div><p class="text-xs text-blue-300">Sistema informático web · 101 Shop</p>
</section>
<section class="flex items-center justify-center p-5 sm:p-10"><div class="w-full max-w-md"><div class="mb-8 lg:hidden"><div class="flex items-center gap-3"><span class="grid h-12 w-12 place-items-center rounded-xl bg-[#0b1f3a] text-sm font-black text-white">101</span><div><h1 class="text-xl font-bold text-[#0b1f3a]">101 Shop</h1><p class="text-xs text-slate-500">Sistema de gestión</p></div></div></div><div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">{{ $slot }}</div></div></section>
</div></body></html>
