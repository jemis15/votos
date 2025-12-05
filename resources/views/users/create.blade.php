<x-layouts.app title="Crear usuario">
    <flux:heading size="xl">Crear usuario</flux:heading>

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="max-w-sm mt-5">
        @csrf

        <div class="space-y-5">
            <flux:input label="Nombre" name="name" value="{{ old('name') }}" badge="Requerido" />

            <flux:input label="Documento (DNI)" name="identification" value="{{ old('identification') }}"
                badge="Requerido" />

            <flux:input label="Correo" name="email" value="{{ old('email') }}" badge="Requerido" autocomplete="email"
                placeholder="email@example.com" />

            <flux:input type="password" label="Contraseña"
                description="Si no ingresa una contraseña, se utilizará por defecto su número de DNI." name="password"
                value="{{ old('password') }}" viewable />

            <flux:input label="Instancia" name="instance" value="{{ old('instance') }}" placeholder="JUPEC SATIPO" />

            <flux:input type="date" label="Fecha nacimiento" name="birthdate" value="{{ old('birthdate') }}" />

            <flux:input type="file" name="profile_photo_path" label="Foto" accept="image/*" />
        </div>

        <flux:button class="mt-5" href="{{ route('users') }}">Cancelar</flux:button>
        <flux:button class="mt-5" variant="primary" type="submit">Guardar</flux:button>
    </form>
</x-layouts.app>
