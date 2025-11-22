<div>
    <flux:input label="Buscar" wire:model.live.debounce.500ms="search" />

    @if (!empty($users))
        <ul class="mt-4 bg-white shadow rounded-lg divide-y">
            @forelse($users as $user)
                <li wire:key="search-user-{{ $user->id }}" class="p-2 hover:bg-gray-100 cursor-pointer"
                    wire:click="handleSelectUser({{ $user->id }})">
                    {{ $user->name }} <br>
                    {{-- <div wire:click="handleSelectUser(1,1)">Agregar</div> --}}
                    <span class="text-sm text-gray-500">DNI: {{ $user->identification }}</span>

                    @if (in_array($user->id, $userInEvent))
                        <span class="text-xs bg-gray-300 text-gray-700 px-2 py-1 rounded">En sala</span>
                    @else
                        <span class="text-xs bg-blue-500 text-white px-2 py-1 rounded">Agregar</span>
                    @endif

                    <div wire:loading wire:target="handleSelectUser({{ $user->id }})">
                        Agregando...
                    </div>
                </li>
            @empty
                <li class="p-2 text-gray-500">No se encontraron resultados.</li>
            @endforelse
        </ul>
    @endif

</div>
