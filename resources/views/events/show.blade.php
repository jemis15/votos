<x-layouts.app title="Editar evento">
    @session('success')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endsession

    <div class="flex items-center justify-between gap-x-2">
        <h1 class="text-xl">
            Detalle del evento <b>{{ $event->name }}</b>
            @if ($event->is_open)
                <flux:badge color="green">Abierto</flux:badge>
            @else
                <flux:badge color="gray">Cerrado</flux:badge>
            @endif
        </h1>
        <div class="space-x-2">
            @if ($event->is_open)
                <flux:button href="{{ route('events.live', $event->id) }}" icon="signal" color="red" variant="primary">
                    Live</flux:button>
            @endif

            <flux:button href="{{ route('events.edit', $event->id) }}" icon="pencil-square">Editar evento</flux:button>

            <flux:modal.trigger name="open-event">
                <flux:button icon="{{ $event->is_open ? 'lock-closed' : 'lock-open' }}">
                    {{ $event->is_open ? 'Cerrar' : 'Iniciar' }}</flux:button>
            </flux:modal.trigger>

            <flux:modal name="open-event">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Abrir sala</flux:heading>
                    </div>
                    <div class="flex">
                        <flux:spacer />

                        <form action="{{ route('events.toggle-status', $event->id) }}" method="post">
                            @csrf
                            <flux:button type="submit" variant="primary">Abrir sala</flux:button>
                        </form>
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>

    <div class="mt-6">
        <h2 class="text-xl font-medium">Elecciones</h2>

        <div class="overflow-x-auto mt-5">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="pb-3 px-3">Cargo</th>
                        <th class="pb-3 w-px text-right">Accion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($event->elections as $election)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-3">
                                {{ $election->cargo }}
                                @if ($election->status === 'open')
                                    <flux:badge color="lime">Abierto</flux:badge>
                                @elseIf($election->status === 'closed')
                                    <flux:badge color="gray">Cerrado</flux:badge>
                                @endif
                            </td>
                            <td class="py-2 w-px">
                                <div class="flex gap-x-2">
                                    <form action="{{ route('elections.toggle-status', $election->id) }}" method="post">
                                        @csrf
                                        <flux:button type="submit">Empezar</flux:button>
                                    </form>

                                    <flux:modal.trigger name="destroy-election-{{ $election->id }}">
                                        <flux:button variant="danger" icon="trash" />
                                    </flux:modal.trigger>

                                    <flux:modal name="destroy-election-{{ $election->id }}">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">Eliminar elecion</flux:heading>
                                                <flux:text class="mt-2">Esta accion no es reversible.</flux:text>
                                            </div>
                                            <div class="flex">
                                                <flux:spacer />
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
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-between items-center mt-6">
        <h2 class="text-xl font-medium">Cargos</h2>
        <flux:button href="{{ route('cargos.create') }}?event_id={{ $event->id }}" icon="plus">Agregar <span
                class="hidden sm:inline">cargo</span></flux:button>
    </div>
    <div class="overflow-x-auto mt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="pb-3 px-3">Nombre</th>
                    <th class="pb-3 w-px text-right">Accion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($cargos as $cargo)
                    @php
                        $election = $event->elections->where('cargo_id', $cargo->id)->first();
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            {{ $cargo->name }}

                            @if ($election?->status === 'open')
                                <flux:badge color="lime">Iniciado</flux:badge>
                            @endif
                        </td>
                        <td class="py-2 w-px">
                            <div class="flex gap-x-2">
                                @if ($event->is_open && !$election?->is_open)
                                    <form action="{{ route('events.cargos.start', $cargo->id) }}" method="post">
                                        @csrf
                                        <flux:button type="submit">Iniciar</flux:button>
                                    </form>
                                @endif
                                <flux:button href="{{ route('cargos.edit', $cargo->id) }}">Editar</flux:button>
                                <flux:button href="{{ route('cargos.delete', $cargo->id) }}" variant="danger"
                                    icon="trash" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        @foreach ($errors->all() as $item)
            <div>{{ $item }}</div>
        @endforeach
    </div>

    <div class="flex items-center mt-6">
        <h2 class="text-xl font-medium">Candidatos</h2>

        <flux:modal.trigger name="upload-candidates">
            <flux:button class="ml-auto">Importar</flux:button>
        </flux:modal.trigger>

        <flux:modal name="upload-candidates" class="min-w-sm">
            <form method="post" action="{{ route('candidates.import', $event->id) }}" enctype="multipart/form-data">
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
        </flux:modal>

        <flux:button href="{{ route('candidates.create') }}?event_id={{ $event->id }}" icon="plus"
            class="ml-3">Agregar <span class="hidden sm:inline">candidato</span></flux:button>
    </div>
    <div class="overflow-x-auto mt-5">
        <table class="w-full mt-4">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-3 text-left">Nombre</th>
                    <th class="py-2 px-3 text-left">Documento</th>
                    <th class="py-2 px-3 text-left">Cargo</th>
                    <th class="py-2 px-3 text-left">Elejible</th>
                    <th class="py-2 px-3 text-left">Foto</th>
                    <th class="py-2 px-3 text-left w-px">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($candidates as $candidate)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">{{ $candidate->name }}</td>
                        <td class="py-2 px-3">{{ $candidate->identification }}</td>
                        <td class="py-2 px-3">{{ $candidate->cargo?->name }}</td>
                        <td class="py-2 px-3">
                            @if ($candidate->eligible)
                                <flux:badge color="lime">Elegible</flux:badge>
                            @else
                                <flux:badge color="gray">No elegible</flux:badge>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            @if ($candidate->photo_url)
                                <img src="{{ $candidate->photo_url }}" alt="Foto" width="50">
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                @if ($candidate->cargo_id === null)
                                    <form action="{{ route('candidates.toggle-elegible', $candidate->id) }}"
                                        method="post">
                                        @csrf
                                        <flux:button type="submit">Elegible</flux:button>
                                    </form>
                                @endif
                                <flux:button href="{{ route('candidates.edit', $candidate->id) }}"
                                    icon="pencil-square" />
                                <flux:button href="{{ route('candidates.delete', $candidate->id) }}"
                                    icon="trash" />
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex items-center mt-6">
        <h2 class="text-xl font-medium">Votantes</h2>

        <flux:modal.trigger name="upload-voters">
            <flux:button class="ml-auto">Importar</flux:button>
        </flux:modal.trigger>

        <flux:modal name="upload-voters" class="min-w-sm">
            <form method="post" action="{{ route('voters.import', $event->id) }}" enctype="multipart/form-data">
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
                <tr class="border-b">
                    <th class="pb-3 px-3">Nombre</th>
                    <th class="pb-3 px-3">Document</th>
                    <th class="pb-3 px-3 w-px">Accion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($voters as $voter)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            {{ $voter->name }}
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
                                            <form action="{{ route('voters.delete', [$event->id, $voter->id]) }}"
                                                method="POST">
                                                @csrf
                                                @method('delete')
                                                <flux:button type="submit" variant="danger">Si eliminar votante
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

</x-layouts.app>
