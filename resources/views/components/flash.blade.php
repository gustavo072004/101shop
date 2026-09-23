@if(session('success'))

    <div
        id="flash-message"
        class="hidden"
        data-type="success"
        data-title="Operación realizada"
        data-message="{{ session('success') }}"
    ></div>

@elseif(session('error'))

    <div
        id="flash-message"
        class="hidden"
        data-type="error"
        data-title="No se pudo realizar la operación"
        data-message="{{ session('error') }}"
    ></div>

@endif