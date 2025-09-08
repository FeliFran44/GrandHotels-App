<x-guest-layout>
    <div class="card shadow-lg auth-card">
        <div class="auth-header">
            <i class="bi bi-shield-lock-fill"></i>
            <h2 class="mt-3">GHL Seguridad</h2>
            <p class="text-muted">Por favor, inicie sesión para continuar</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" class="form-control form-control-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" class="form-control form-control-lg" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>