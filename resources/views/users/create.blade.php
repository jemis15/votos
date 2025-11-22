<x-layouts.app title="Crear usuario">
    <h1 class="text-xl mb-4">Crear usuario</h1>
    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-3">
            <flux:input label="Nombre" name="name" value="{{ old('name') }}" />
            <flux:input label="Documento (DNI)" name="identification" value="{{ old('identification') }}" />
            <flux:input label="Correo" name="email" value="{{ old('email') }}" />
            <flux:field class="mt-5">
                <flux:label>Foto</flux:label>
                <flux:input name="profile_photo_path" accept="image/*" type="file" />
                <flux:error name="profile_photo_path" />
            </flux:field>
        </div>

        <flux:button class="mt-5" href="{{ route('users') }}">Cancelar</flux:button>
        <flux:button class="mt-5" variant="primary" type="submit">Guardar</flux:button>
    </form>
</x-layouts.app>
