<x-layouts.app title="Editar evento">
    <div class="flex items-center justify-between gap-x-2">
        <h1 class="text-xl">Detalle del evento <b>{{ $event->name }}</b></h1>
        <div class="space-x-2">
            <flux:button href="{{ route('events.edit', $event->id) }}" icon="pencil-square">Editar evento</flux:button>
            <flux:button href="{{ route('events.edit', $event->id) }}" icon="lock-open">Iniciar</flux:button>
        </div>
    </div>

    <div class="flex justify-between items-center mt-6">
        <h2 class="text-xl font-medium">Roles</h2>
        <flux:button icon="plus">Agregar <span class="hidden sm:inline">rol</span></flux:button>
    </div>
    <div class="overflow-x-auto mt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <td class="pb-3 px-3">Nombre</td>
                    <td class="pb-3 px-3 w-px">Accion</td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            <a href="{{ route('events.show', $event->id) }}" class="hover:underline">
                                {{ $event->name }} {{ $event->is_open ? 'Abierto' : 'Cerrado' }}
                            </a>
                        </td>
                        <td class="py-2 px-3">{{ $event->is_open ? 'Abierto' : 'Cerrado' }}</td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                <flux:button href="{{ route('events.edit', $event->id) }}">Editar</flux:button>
                                <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger"
                                    icon="trash" />

                                {{-- <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger">Eliminar
                                </flux:button> --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center mt-6">
        <h2 class="text-xl font-medium">Candidatos</h2>
        <flux:button icon="plus" class="ml-auto">Importar</flux:button>
        <flux:button icon="plus" class="ml-3">Agregar <span class="hidden sm:inline">candidato</span></flux:button>
    </div>
    <div class="overflow-x-auto mt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <td class="pb-3 px-3">Nombre</td>
                    <td class="pb-3 px-3 w-px">Accion</td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            <a href="{{ route('events.show', $event->id) }}" class="hover:underline">
                                {{ $event->name }} {{ $event->is_open ? 'Abierto' : 'Cerrado' }}
                            </a>
                        </td>
                        <td class="py-2 px-3">{{ $event->is_open ? 'Abierto' : 'Cerrado' }}</td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                <flux:button href="{{ route('events.edit', $event->id) }}">Editar</flux:button>
                                <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger"
                                    icon="trash" />

                                {{-- <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger">Eliminar
                                </flux:button> --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center mt-6">
        <h2 class="text-xl font-medium">Votantes</h2>
        <flux:button icon="plus" class="ml-auto">Importar</flux:button>
        <flux:button icon="plus" class="ml-3">Agregar <span class="hidden sm:inline">candidato</span></flux:button>
    </div>
    <div class="overflow-x-auto mt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="pb-3 px-3">Nombre</th>
                    <th class="pb-3 px-3 w-px">Accion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            <a href="{{ route('events.show', $event->id) }}" class="hover:underline">
                                {{ $event->name }} {{ $event->is_open ? 'Abierto' : 'Cerrado' }}
                            </a>
                        </td>
                        <td class="py-2 px-3">{{ $event->is_open ? 'Abierto' : 'Cerrado' }}</td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                <flux:button href="{{ route('events.edit', $event->id) }}">Editar</flux:button>
                                <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger"
                                    icon="trash" />

                                {{-- <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger">Eliminar
                                </flux:button> --}}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</x-layouts.app>
