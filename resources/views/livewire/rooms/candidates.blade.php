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

        <flux:button href="{{ route('candidates.create') }}?event_id={{ $event->id }}" icon="plus" class="ml-auto">
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
                        <td class="py-2 px-3">{{ $candidate->pivot }}</td>
                        <td class="py-2 px-3">
                            <div class="flex gap-x-2">
                                @if ($candidate->cargo_id === null && $event->elections->contains('status', 'created'))
                                    <form
                                        action="{{ route('elections.candidates.toggle', $event->elections->firstWhere('status', 'created')) }}"
                                        method="post">
                                        @csrf
                                        <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                        <flux:button type="submit">
                                            {{ $event->elections->firstWhere('status', 'created')->candidates->contains($candidate->id) ? 'Quitar' : 'Agregar' }}
                                        </flux:button>
                                    </form>
                                @endif

                                <flux:modal.trigger name="delete-candidate-{{ $candidate->id }}">
                                    <flux:button variant="danger" icon="trash" />
                                </flux:modal.trigger>

                                <flux:modal name="delete-candidate-{{ $candidate->id }}" class="md:w-96">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">Eliminar candidato</flux:heading>
                                            <flux:text class="mt-2">
                                                Estas a punto de eliminar al candidato {{ $candidate->name }}. <br>
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
