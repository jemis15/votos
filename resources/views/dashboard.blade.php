<x-layouts.app :title="__('Dashboard')">
    <h2 class="text-xl font-medium">Salas disponibles</h2>

    <ul class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(250px,1fr))] gap-4">
        @forelse ($rooms as $room)
            <li>
                <a href="{{ route('rooms.register-votes', $room->id) }}" wire:navigate>
                    <div class="border dark:border-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <flux:heading class="flex items-center gap-2">
                            {{ $room->name }}
                            @if ($room->is_open)
                                <flux:badge color="green" size="sm">Abierto</flux:badge>
                            @else
                                <flux:badge color="gray" size="sm">Cerrado</flux:badge>
                            @endif
                            <flux:icon name="arrow-right" class="ml-auto text-zinc-400" variant="micro" />
                        </flux:heading>
                    </div>
                </a>
            </li>
        @empty
            <li class="col-span-full text-center text-gray-500">
                No tienes salas disponibles.
            </li>
        @endforelse
    </ul>
</x-layouts.app>
