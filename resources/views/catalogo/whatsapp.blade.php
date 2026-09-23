@if($privado)
<form method="POST" action="{{ route('catalogo.vender', $producto) }}">
@csrf
<button class="sell-button" type="submit" @disabled($producto->stock < 1 || !$producto->estado || !$producto->categoria?->estado)>Vender ahora</button>
<p class="code">Stock: {{ $producto->stock }}</p>
</form>
@elseif($url = $producto->whatsappUrl())
<a class="whatsapp" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="Consultar {{ $producto->nombre }} por WhatsApp">
<svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M20.52 3.48A11.82 11.82 0 0 0 12.1 0C5.54 0 .2 5.34.2 11.9c0 2.1.55 4.15 1.6 5.96L.1 24l6.3-1.65a11.9 11.9 0 0 0 5.69 1.45h.01c6.56 0 11.9-5.34 11.9-11.9 0-3.18-1.24-6.17-3.48-8.42ZM12.1 21.8a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98 1-3.65-.24-.37A9.86 9.86 0 0 1 2.2 11.9C2.2 6.44 6.64 2 12.1 2c2.64 0 5.13 1.03 7 2.9A9.84 9.84 0 0 1 22 11.9c0 5.46-4.44 9.9-9.9 9.9Zm5.43-7.42c-.3-.15-1.76-.87-2.03-.97-.28-.1-.48-.15-.68.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.9-.8-1.5-1.78-1.68-2.08-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.59-.49-.51-.67-.52h-.57c-.2 0-.52.08-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.87 1.22 3.07c.15.2 2.1 3.2 5.08 4.48.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2-1.41.25-.69.25-1.29.18-1.41-.08-.13-.28-.2-.57-.35Z"/></svg>
Consultar por WhatsApp</a>
@else
<span class="contact-unavailable">Consultas temporalmente no disponibles</span>
@endif

