<x-layouts.app title="Crear usuario">
    <flux:heading size="xl" level="1">Reporte de sala: {{ $room->name }}</flux:heading>

    <form action="{{ url()->current() }}" method="get" class="max-w-xs mt-5 flex gap-x-2">
        <flux:select name="election_id" value="{{ $currentElectionId }}" placeholder="Escoge una eleccion...">
            @foreach ($elections as $election)
                @if ($currentElectionId == $election->id)
                    <flux:select.option value="{{ $election->id }}" selected>
                        {{ $election->cargo }}
                    </flux:select.option>
                @else
                    <flux:select.option value="{{ $election->id }}">
                        {{ $election->cargo }}
                    </flux:select.option>
                @endif
            @endforeach
        </flux:select>
        <flux:button type="submit">Filtrar</flux:button>
    </form>

    @php
        $cargo = $elections->find($currentElectionId);
    @endphp

    @if ($currentElectionId)
        <table class="w-full mt-5">
            <thead>
                <tr class="bg-gray-100 dark:bg-zinc-700">
                    <th class="text-left px-3 py-2">Eleccion</th>
                    <th class="text-left px-3 py-2">Votante</th>
                    <th class="text-left px-3 py-2">Candidato</th>
                    <th class="text-left px-3 py-2">Fecha y hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($room->users as $user)
                    @if (!in_array($user->pivot->role_in_room, ['voter', 'both']))
                        @continue
                    @endif

                    @php
                        $vote = $votes->where('voter_id', $user->id)->first();
                        $candidate = $vote ? $room->users->find($vote->candidate_id) : null;
                    @endphp

                    <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-zinc-900 dark:even:bg-zinc-700">
                        <td class="px-3 py-2">{{ $cargo?->cargo }}</td>
                        <td class="px-3 py-2">{{ $user->name }}</td>
                        <td class="px-3 py-2">
                            {{ $vote ? $candidate?->name ?? 'VOTO EN BLANCO' : '' }}
                        </td>
                        <td class="px-3 py-2">
                            {{ $vote ? date('d/m/Y h:i:s A', strtotime($vote->created_at)) : '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-layouts.app>
