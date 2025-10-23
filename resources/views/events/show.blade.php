<x-layouts.app title="Editar evento">
    @session('success')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endsession

    <div class="flex items-center justify-between gap-x-2">
        <h1 class="text-xl">Detalle del evento <b>{{ $event->name }}</b></h1>
        <div class="space-x-2">
            <flux:button href="{{ route('events.edit', $event->id) }}" icon="pencil-square">Editar evento</flux:button>
            <flux:button href="{{ route('events.edit', $event->id) }}" icon="lock-open">Iniciar</flux:button>
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
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-3">
                            {{ $cargo->name }}
                        </td>
                        <td class="py-2 w-px">
                            <div class="flex gap-x-2">
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
                    <th class="py-2 px-3 text-left">Evento</th>
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
                        <td class="py-2 px-3">{{ $candidate->event?->name }}</td>
                        <td class="py-2 px-3">
                            @if ($candidate->photo_url)
                                <img src="{{ $candidate->photo_url }}" alt="Foto" width="50">
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                <flux:button href="{{ route('candidates.edit', $candidate->id) }}"
                                    icon="pencil-square" />
                                <flux:button href="{{ route('candidates.delete', $candidate->id) }}" icon="trash" />
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

        <flux:button href="{{ route('voters.create') }}?event_id={{ $event->id }}" icon="plus" class="ml-3">
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
