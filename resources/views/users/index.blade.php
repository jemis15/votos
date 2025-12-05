<x-layouts.app title="Usuarios">
    <div class="flex items-center mb-4">
        <h1 class="text-xl">Usuarios</h1>

        <flux:modal.trigger name="import-users">
            <flux:button class="ml-auto">Importar</flux:button>
        </flux:modal.trigger>

        <flux:modal name="import-users" class="md:w-96">
            <form action="{{ route('users.import') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Importar usuarios</flux:heading>
                    </div>
                    <flux:input label="Archivo exel" type="file" name="file_users" />
                    <div class="flex justify-end">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary" class="ml-2">Importar</flux:button>
                    </div>
                </div>
            </form>
        </flux:modal>

        <flux:button class="ml-3" href="{{ route('users.create') }}">Crear usuario</flux:button>
    </div>

    @foreach ($errors->all() as $error)
        <div class="shadow mb-3 text-red-500 py-2">{{ $error }}</div>
    @endforeach

    @session('success')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endsession

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b dark:border-neutral-700">
                    <td class="pb-3 px-3">Nombre</td>
                    <td class="pb-3 px-3">DNI</td>
                    <td class="pb-3 px-3">Correo</td>
                    <td class="pb-3 px-3">Tipo</td>
                    <td class="pb-3 px-3">Instancia</td>
                    <td class="pb-3 px-3 w-px">Accion</td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-x-2">
                                <flux:avatar src="{{ $user->image_url }}" name="{{ $user->name }}" initials:single />
                                <flux:heading size="lg">{{ $user->name }}</flux:heading>
                            </div>
                        </td>
                        <td class="py-2 px-3">{{ $user->identification }}</td>
                        <td class="py-2 px-3">{{ $user->email }}</td>
                        <td class="py-2 px-3">{{ $user->role }}</td>
                        <td class="py-2 px-3">{{ $user->instance }}</td>
                        <td class="py-2 px-3">
                            <flux:dropdown>
                                <flux:button icon:trailing="chevron-down">Opciones</flux:button>

                                <flux:menu>
                                    <flux:menu.item href="{{ route('users.edit', $user->id) }}" icon="pencil-square">
                                        Editar</flux:menu.item>
                                    <form action="{{ route('users.reset-password', $user->id) }}" method="post">
                                        @csrf
                                        <flux:menu.item as="button" type="submit" icon="lock-open">
                                            Resetar contraseña
                                        </flux:menu.item>
                                    </form>

                                    <flux:menu.item href="{{ route('users.delete', $user->id) }}" icon="trash"
                                        variant="danger">
                                        Eliminar
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
