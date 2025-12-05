<x-layouts.app title="Editar usuario">
    <h1 class="text-xl mb-4">Editar usuario</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="max-w-sm">
        @csrf
        @method('PUT')

        <div class="space-y-5">
            <flux:input label="Nombre" name="name" value="{{ old('name', $user->name) }}" badge="Requerido" />

            <flux:input label="Documento (DNI)" name="identification"
                value="{{ old('identification', $user->identification) }}" badge="Requerido" />

            <flux:input label="Correo" name="email" value="{{ old('email', $user->email) }}" badge="Requerido"
                autocomplete="email" placeholder="email@example.com" />

            <flux:input type="password" label="Contraseña" description="Ingrese aquí si desea cambiar su contraseña."
                name="password" value="{{ old('password') }}" viewable />

            <flux:input label="Instancia" name="instance" value="{{ old('instance', $user->instance) }}" placeholder="JUPEC SATIPO" />

            <flux:input type="date" label="Fecha nacimiento" name="birthdate" value="{{ old('birthdate', $user->birthdate) }}" />

            <flux:input type="file" name="profile_photo_path" label="Foto" accept="image/*" />
        </div>

        @if ($user->profile_photo_path)
            <img src="{{ $user->image_url }}" alt="Foto actual" class="mt-5 max-w-32">
        @endif

        <flux:button class="mt-5" href="{{ route('users') }}">Cancelar</flux:button>
        <flux:button class="mt-5" variant="primary" type="submit">Actualizar</flux:button>
    </form>
</x-layouts.app>
