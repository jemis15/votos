<x-layouts.app title="Eventos">
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl" level="1">Eventos</flux:heading>
        <flux:button href="{{ route('events.create') }}">Crear evento</flux:button>
    </div>

    @session('success')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endsession

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b dark:border-b-gray-700">
                    <td class="pb-3 px-3">Nombre</td>
                    <td class="pb-3 px-3">Estado</td>
                    <td class="pb-3 px-3 w-px">Accion</td>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($events as $event)
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                        <td class="py-2 px-3">
                            <a wire:navigate href="{{route("events.show", $event->id)}}" class="hover:underline">
                                {{ $event->name }}
                            </a>
                        </td>
                        <td class="py-2 px-3">
                            @if ($event->is_open)
                            <flux:badge color="green">Abierto</flux:badge>
                                @else
                                <flux:badge color="gray">Cerrado</flux:badge>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                <flux:button href="{{ route('events.edit', $event->id) }}">Editar</flux:button>
                                <flux:button href="{{ route('events.delete', $event->id) }}" variant="danger" icon="trash" />

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
