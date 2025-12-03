<x-layouts.app title="Editar evento">
    @session('success')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endsession

    @session('error')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endsession

    <div class="flex items-center justify-between gap-x-2">
        <flux:heading size="xl" level="1">
            {{ $event->name }}
            @if ($event->is_open)
                <flux:badge color="green">Abierto</flux:badge>
            @else
                <flux:badge color="gray">Cerrado</flux:badge>
            @endif
        </flux:heading>

        <div class="space-x-2">
            <flux:button href="{{ route('events.live', $event->id) }}" icon="signal" color="red" variant="primary"
                target="_blank">
                Live
            </flux:button>

            <flux:button href="{{ route('events.edit', $event->id) }}" icon="pencil-square">Editar evento</flux:button>

            <flux:modal.trigger name="open-event">
                <flux:button icon="{{ $event->is_open ? 'lock-closed' : 'lock-open' }}">
                    {{ $event->is_open ? 'Cerrar' : 'Iniciar' }}</flux:button>
            </flux:modal.trigger>

            <flux:modal name="open-event" class="w-86">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">
                            {{ $event->is_open ? 'Cerrar evento' : 'Iniciar evento' }}
                        </flux:heading>
                        <flux:text class="mt-2">
                            {{ $event->is_open
                                ? 'Estas a punto de cerrar el evento. Los votantes ya no podran votar.'
                                : 'Estas a punto de iniciar el evento. Los votantes podran empezar a votar.' }}
                        </flux:text>
                    </div>
                    <div class="flex gap-x-2">
                        <flux:spacer />

                        <flux:modal.close>
                            <flux:button variant="ghost">Cancelar</flux:button>
                        </flux:modal.close>
                        <form action="{{ route('events.toggle-status', $event->id) }}" method="post">
                            @csrf
                            <flux:button type="submit" variant="primary">
                                {{ $event->is_open ? 'Cerrar evento' : 'Iniciar evento' }}
                            </flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>

    <div x-data="{ showTab: 0, tabs: ['Elecciones', 'Cargos', 'Candidatos', 'Votantes'] }" class="mb-6">
        <nav class="border-b border-gray-200 dark:border-zinc-700 mb-6">
            <ul class="flex space-x-4">
                <template x-for="(tab, index) in tabs" :key="index">
                    <li>
                        <button @click="showTab = index"
                            :class="showTab === index ?
                                'border-indigo-500 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' :
                                'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            <span x-text="tab"></span>
                        </button>
                    </li>
                </template>
            </ul>
        </nav>

        <div x-show="showTab === 0">
            <div class="flex">
                <flux:heading size="xl" level="2">Elecciones</flux:heading>
                <flux:button href="{{ route('rooms.elections.create', $event->id) }}" icon="plus" class="ml-auto">
                    Agregar <span class="hidden sm:inline">eleccion</span>
                </flux:button>
            </div>

            <div class="overflow-x-auto mt-5">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b dark:border-b-zinc-700">
                            <th class="pb-3 px-3">Cargo</th>
                            <th class="pb-3 px-3">Elejibles</th>
                            <th class="pb-3 w-px"></th>
                            <th class="pb-3 w-px"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @php
                            $hasOpenElections = $event->elections->whereIn('status', ['open', 'created'])->count() > 0;
                        @endphp
                        @foreach ($event->elections as $election)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="py-2 px-3">
                                    {{ $election->cargo }}
                                    @if ($election->status === 'open')
                                        <flux:badge color="lime">Abierto</flux:badge>
                                    @elseIf($election->status === 'closed')
                                        <flux:badge color="gray">Cerrado</flux:badge>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @foreach ($election->candidates as $candidate)
                                        <div>{{ $candidate->name }}</div>
                                    @endforeach
                                </td>
                                <td class="px-2">
                                    @if ($election->status !== 'closed')
                                        <form action="{{ route('elections.toggle-status', $election->id) }}"
                                            method="post">
                                            @csrf
                                            <flux:button type="submit">
                                                {{ $election->status === 'open' ? 'Cerrar' : 'Iniciar' }}
                                            </flux:button>
                                        </form>
                                    @endif
                                </td>
                                <td class="px-2">
                                    <flux:modal.trigger class="ml-auto" name="destroy-election-{{ $election->id }}">
                                        <flux:button variant="danger" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="destroy-election-{{ $election->id }}">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Eliminar elecion</flux:heading>
                                                <flux:text class="mt-2">
                                                    Estas a punto de eliminar la eleccion para el cargo
                                                    {{ $election->cargo }}. <br>
                                                    Esta acccion no se puede revertir.
                                                </flux:text>
                                            </div>
                                            <div class="flex gap-x-2">
                                                <flux:spacer />

                                                <flux:modal.close>
                                                    <flux:button variant="ghost">Cancelar</flux:button>
                                                </flux:modal.close>
                                                <form action="{{ route('elections.destroy', $election->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <flux:button type="submit" variant="primary">Eliminar eleccion
                                                    </flux:button>
                                                </form>
                                            </div>
                                        </div>
                                    </flux:modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showTab === 1">
            <div class="flex justify-between items-center mt-6">
                <flux:heading size="xl" level="2">Cargos</flux:heading>
                <flux:button href="{{ route('cargos.create') }}?event_id={{ $event->id }}" icon="plus">
                    Agregar <span class="hidden sm:inline">cargo</span>
                </flux:button>
            </div>
            <div class="overflow-x-auto mt-5">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b dark:border-zinc-700">
                            <th class="pb-3 px-3">Nombre</th>
                            <th class="pb-3 w-px text-right">Accion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($cargos as $cargo)
                            @php
                                $election = $event->elections->where('cargo_id', $cargo->id)->first();
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="py-2 px-3">
                                    {{ $cargo->name }}

                                    @if ($election?->status === 'open')
                                        <flux:badge color="lime">Iniciado</flux:badge>
                                    @endif
                                </td>
                                <td class="py-2 w-px">
                                    <div class="flex gap-x-2">
                                        @if ($event->is_open && !$election?->is_open)
                                            {{-- <form action="{{ route('events.cargos.start', $cargo->id) }}" method="post"> --}}
                                            <form action="{{ route('elections.store') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="cargo_id" value="{{ $cargo->id }}">
                                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                                <flux:button type="submit">Iniciar</flux:button>
                                            </form>
                                        @endif
                                        <flux:button href="{{ route('cargos.edit', $cargo->id) }}">Editar
                                        </flux:button>
                                        <flux:button href="{{ route('cargos.delete', $cargo->id) }}" variant="danger"
                                            icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="showTab === 2">
            <div class="flex items-center mt-6">
                <flux:heading size="xl" level="2">Candidatos</flux:heading>

                {{-- <flux:modal.trigger name="upload-candidates">
                    <flux:button class="ml-auto">Importar</flux:button>
                </flux:modal.trigger>

                <flux:modal name="upload-candidates" class="min-w-sm">
                    <form method="post" action="{{ route('candidates.import', $event->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <flux:heading size="lg">Importar</flux:heading>
                            <flux:input name="file_candidates" label="Archivo" type="file" />
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">Importar</flux:button>
                            </div>
                        </div>
                    </form>
                </flux:modal> --}}

                <flux:button href="{{ route('candidates.create') }}?event_id={{ $event->id }}" icon="plus"
                    class="ml-auto">
                    Agregar <span class="hidden sm:inline">candidato</span></flux:button>
            </div>
            <div class="overflow-x-auto mt-5">
                <table class="w-full mt-4">
                    <thead>
                        <tr class="border-b dark:border-zinc-700">
                            <th class="py-2 px-3 text-left">Nombre</th>
                            <th class="py-2 px-3 text-left">Documento</th>
                            <th class="py-2 px-3 text-left">Cargo</th>
                            <th class="py-2 px-3 text-left w-px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($candidates as $candidate)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="py-2 px-3">
                                    <div class="flex gap-3 items-center">
                                        <flux:avatar src="{{ $candidate->image_url }}" name="{{ $candidate->name }}"
                                            initials:single />
                                        <span>{{ $candidate->name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3">{{ $candidate->identification }}</td>
                                <td class="py-2 px-3">{{ $cargos->find($candidate->pivot->cargo_id)?->name }}</td>
                                <td class="py-2 px-3">
                                    <div class="flex gap-x-2">
                                        @if ($candidate->cargo_id === null && $event->elections->contains('status', 'created'))
                                            <form
                                                action="{{ route('elections.candidates.toggle', $event->elections->firstWhere('status', 'created')) }}"
                                                method="post">
                                                @csrf
                                                <input type="hidden" name="candidate_id"
                                                    value="{{ $candidate->id }}">
                                                <flux:button type="submit">
                                                    {{ $event->elections->firstWhere('status', 'created')->candidates->contains($candidate->id) ? 'Quitar' : 'Agregar' }}
                                                </flux:button>
                                            </form>
                                        @endif

                                        <flux:button
                                            href="{{ route('rooms.candidates.edit', [$event->id, $candidate->id]) }}"
                                            icon="pencil-square" />

                                        <flux:modal.trigger name="delete-candidate-{{ $candidate->id }}">
                                            <flux:button variant="danger" icon="trash" />
                                        </flux:modal.trigger>

                                        <flux:modal name="delete-candidate-{{ $candidate->id }}" class="md:w-96">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">Eliminar candidato</flux:heading>
                                                    <flux:text class="mt-2">
                                                        Estas a punto de eliminar al candidato {{ $candidate->name }}.
                                                        <br>
                                                        Esta acccion no se puede revertir.
                                                    </flux:text>
                                                </div>
                                                <div class="flex justify-end gap-x-2">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">Cancelar</flux:button>
                                                    </flux:modal.close>
                                                    <form
                                                        action="{{ route('rooms.candidates.destroy', [$event->id, $candidate->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <flux:button type="submit" variant="danger">
                                                            Si eliminar candidato
                                                        </flux:button>
                                                    </form>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div x-show="showTab === 3">
            <div class="flex items-center mt-6">
                <flux:heading size="xl" level="2">Votantes</flux:heading>

                <flux:modal.trigger name="upload-voters">
                    <flux:button class="ml-auto">Importar</flux:button>
                </flux:modal.trigger>

                <flux:modal name="upload-voters" class="min-w-sm">
                    <form method="post" action="{{ route('voters.import', $event->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <flux:heading size="lg">Importar votantes</flux:heading>
                            <flux:input name="file_voters" label="Archivo" type="file" />
                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">Importar</flux:button>
                            </div>
                        </div>
                    </form>
                </flux:modal>

                <flux:button href="{{ route('voters.create') }}?event_id={{ $event->id }}" icon="plus"
                    class="ml-3">
                    Agregar <span class="hidden sm:inline">votante</span></flux:button>
            </div>
            <div class="overflow-x-auto mt-5">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b dark:border-zinc-700">
                            <th class="pb-3 px-3">Nombre</th>
                            <th class="pb-3 px-3">Document</th>
                            <th class="pb-3 px-3 w-px">Accion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @foreach ($voters as $voter)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                <td class="py-2 px-3">
                                    <div class="flex gap-3 items-center">
                                        <flux:avatar src="{{ $voter->image_url }}" name="{{ $voter->name }}"
                                            initials:single />
                                        <span>{{ $voter->name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 px-3">
                                    {{ $voter->identification }}
                                </td>
                                <td class="py-2 px-3">
                                    <div class="flex gap-x-2">
                                        <flux:modal.trigger name="delete-voter-{{ $voter->id }}">
                                            <flux:button variant="danger" icon="trash" />
                                        </flux:modal.trigger>

                                        <flux:modal name="delete-voter-{{ $voter->id }}" class="md:w-96">
                                            <div class="space-y-6">
                                                <div>
                                                    <flux:heading size="lg">Eliminar votante</flux:heading>
                                                    <flux:text class="mt-2">
                                                        Estas a punto de eliminar al votante {{ $voter->name }}. <br>
                                                        Esta acccion no se puede revertir.
                                                    </flux:text>
                                                </div>
                                                <div class="flex justify-end gap-x-2">
                                                    <flux:modal.close>
                                                        <flux:button variant="ghost">Cancelar</flux:button>
                                                    </flux:modal.close>
                                                    <form
                                                        action="{{ route('voters.delete', [$event->id, $voter->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <flux:button type="submit" variant="danger">Si eliminar
                                                            votante
                                                        </flux:button>
                                                    </form>
                                                </div>
                                            </div>
                                        </flux:modal>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        @foreach ($errors->all() as $item)
            <div>{{ $item }}</div>
        @endforeach
    </div>

</x-layouts.app>
