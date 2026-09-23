<x-guest-layout>
    <div class="mb-6">
        <p class="text-sm font-semibold text-blue-600">Recuperación de credenciales</p>
        <h2 class="mt-1 text-2xl font-bold text-[#0b1f3a]">Nueva contraseña</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">
            Use al menos 8 caracteres e incluya mayúsculas, minúsculas y números.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="label-form">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                   required readonly class="input-form @error('email') input-error @enderror">
            @error('email')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="label-form">Nueva contraseña</label>
            <input id="password" type="password" name="password" maxlength="72"
                   autocomplete="new-password" required
                   class="input-form @error('password') input-error @enderror">
            @error('password')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label-form">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" maxlength="72"
                   autocomplete="new-password" required class="input-form">
        </div>

        <button type="submit" class="btn-primary-101 w-full">Restablecer contraseña</button>
    </form>
</x-guest-layout>
