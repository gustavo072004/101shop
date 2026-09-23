<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-semibold text-blue-600">Recuperación de credenciales</p>
        <h2 class="mt-1 text-2xl font-bold text-[#0b1f3a]">Recuperar contraseña</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Ingrese el correo registrado. Si corresponde a una cuenta activa, enviaremos un enlace de recuperación.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="label-form">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="150"
                   autocomplete="email" required autofocus
                   class="input-form @error('email') input-error @enderror"
                   placeholder="usuario@correo.com">
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary-101 w-full">Enviar enlace de recuperación</button>
        <a href="{{ route('login') }}" class="block text-center text-sm font-semibold text-blue-600">
            Volver al inicio de sesión
        </a>
    </form>
</x-guest-layout>
