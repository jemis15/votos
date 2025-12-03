<div class="flex flex-col gap-6">
    <x-auth-header title="Inicia sesión en tu cuenta"
        description="Ingrese su DNI y contraseña a continuación para iniciar sesión" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="user" label="DNI" required autofocus />

        <!-- Password -->
        <div class="relative">
            <flux:input wire:model="password" label="Contraseña" type="password" required
                autocomplete="current-password" placeholder="Contraseña" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        {{-- <flux:checkbox wire:model="remember" :label="__('Remember me')" /> --}}

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                Ingresar
            </flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>¿No tienes una cuenta?</span>
            <flux:link :href="route('register')" wire:navigate>Registrate</flux:link>
        </div>
    @endif
</div>
