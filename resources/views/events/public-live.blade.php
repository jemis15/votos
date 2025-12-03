<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    <style>
        .winner {
            position: relative;
        }

        .winner::after {
            background: var(--background);
            position: absolute;
            height: calc(100% + 1px);
            top: 0;
            left: 0;
            width: var(--width);
            content: "";
            border-left: 8px solid var(--winnerPartyColor);
            transition: width cubic-bezier(0.5, 0.01, 0.5, 1.25) 500ms 100ms;
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>

<body x-data="data" class="dark:bg-zinc-800">

    {{-- <h2 class="px-6 text-2xl font-bold mt-4">Resultados</h2>
        <div class="flex gap-x-4 p-6">
            @foreach ($event->elections as $election)
                <div>
                    <div class="shadow rounded-md overflow-hidden">
                        <div class="bg-red-500 px-4 py-6">
                            <h2 class="text-white font-bold text-xl">
                                {{ $event->obj_cargos[$election->cargo_id]->name }}
                            </h2>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="text-xs px-3 py-2 text-left w-1/2">CANDIDATO</th>
                                    <th class="text-xs px-3 py-2 text-right">PORCENTAJE</th>
                                    <th class="text-xs px-3 py-2 text-right">VOTOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $votosEnBlanco = $election->votes->whereNull('candidate_id')->first()?->votes;
                                    $totalVotes = $votosEnBlanco;
                                @endphp
                                @foreach ($election->candidates as $key => $candidate)
                                    @php
                                        $votos = $election->votes->where('candidate_id', $candidate)->first()?->votes;
                                        $percentage = round(($votos / $event->voters->count()) * 100, 1);

                                        $totalVotes += $votos;
                                    @endphp
                                    <tr class="winner"
                                        style="--width: {{ $percentage }}%; --background: #CC000027 ; --winnerPartyColor: #CC0000">
                                        <td class="px-3 py-2">
                                            <div class="flex items-center gap-2 sm:gap-4">
                                                <flux:avatar circle size="lg" class="max-sm:size-8"
                                                    src="{{ $event->obj_users[$candidate]->image_url }}" />
                                                <div class="flex flex-col">
                                                    <flux:heading>{{ $event->obj_users[$candidate]->name }}
                                                    </flux:heading>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 text-right">{{ round($percentage, 1) }}%</td>
                                        <td class="px-3 py-2 text-right">{{ round($votos) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="winner"
                                    style="--width: {{ $percentage }}%; --background: #6BBD1127 ; --winnerPartyColor: #6BBD11">
                                    <td class="px-2 py-2">Votos en blanco</td>
                                    <td class="px-3 py-2 text-right">
                                        {{ round(($votosEnBlanco / $event->voters->count()) * 100, 1) }}%
                                    </td>
                                    <td class="px-3 py-2 text-right">{{ round($votosEnBlanco) }}</td>
                                </tr>

                                @php
                                    $votosViciados = $event->voters->count() - $totalVotes;
                                    $percentage = round(($votosViciados / $event->voters->count()) * 100, 1);
                                @endphp
                                <tr class="winner"
                                    style="--width: {{ $percentage }}%; --background: #6BBD1127 ; --winnerPartyColor: #6BBD11">
                                    <td class="px-3 py-2">Votos en viciados</td>
                                    <td class="px-3 py-2 text-right">
                                        {{ $percentage }}%
                                    </td>
                                    <td class="px-3 py-2 text-right">{{ round($votosViciados) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2">Total</td>
                                    <td class="px-3 py-2 text-right">100%</td>
                                    <td class="px-3 py-2 text-right">{{ round($event->voters->count()) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div> --}}

    <template x-if="!room.is_open">
        <div class="h-dvh flex flex-col items-center justify-center max-w-xl mx-auto px-6 md:px-0">
            <div>
                <img src="{{ asset('images/logo.png') }}" class="w-64 dark:hidden" alt="logo denec">
                <img src="{{ asset('images/logo-blank.png') }}" class="w-64 hidden dark:block" alt="logo denec">
            </div>
            <h2 class="font-medium text-xl mt-12">{{ $event->name }}</h2>
            <div class="mt-2">Bienvenidos, espere la apertura de esta sala.</div>
        </div>
    </template>

    <template x-if="room.is_open">
        <div class="mr-64 pb-24">
            <img src="{{ asset('images/logo.png') }}" class="absolute top-0 left-4 w-64 dark:hidden" alt="logo denec">
            <img src="{{ asset('images/logo-blank.png') }}" class="absolute top-0 left-4 w-64 hidden dark:block"
                alt="logo denec">
            <div class="flex flex-col">

                <div class="text-center font-medium mt-5">Sistema de votaciones en tiempo real</div>
                <div class="flex justify-center gap-x-4 mt-3">
                    <div class="flex gap-x-2">
                        <flux:icon.bolt class="text-green-500" />
                        <span x-text="votantesActivos() + ' Votantes activos'"></span>
                    </div>
                    <div class="flex gap-x-2">
                        <flux:icon.users class="text-blue-500" />
                        <span x-text="getTotalVotes() + '/' + room.voters.length + ' han votado'"></span>
                    </div>
                    <div class="flex gap-x-2">
                        <flux:icon.crown class="text-yellow-500" />
                        <span x-show="votosNesesariosParaGanar > 0"
                            x-text="'Se require ' + Math.ceil(votosNesesariosParaGanar) + ' votos'"></span>
                    </div>
                </div>

                <template x-if="election && election.status === 'open'">
                    <div class="mt-12">
                        <div class="text-center text-xl font-medium"
                            x-text="'Candidatos para ' + cargos.get(election.cargo_id).name"></div>
                        <div class="flex flex-wrap justify-center gap-5 mt-5">
                            <template x-for="eligible in election.candidates">
                                <div class="relative">
                                    <div class="">
                                        <img class="object-cover w-40 h-40 border"
                                            x-bind:src="users.get(eligible).image_url" alt="">
                                    </div>
                                    <div class="text-center font-medium mt-3" x-text="users.get(eligible).name">
                                    </div>
                                    {{-- <div x-show="getCurrentElection().status === 'closed'">
                                        <div x-text="totalVotesByCandidate(eligible) + ' votos'"></div>
                                        <div
                                            x-text="Math.round(totalVotesByCandidate(eligible) / room.voters.length * 100 * 10) / 10 + '%'">
                                        </div>
                                    </div> --}}
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="election?.status === 'closed'">
                    <div class="w-2xl mx-auto mt-10">
                        <h2 class="text-xl">
                            Resultados para <span class="font-medium"
                                x-text="cargos.get(election.cargo_id).name"></span>
                        </h2>
                        <table class="w-full mt-4">
                            <thead>
                                <tr class="border-b dark:border-zinc-700">
                                    <th class="text-sm text-left pb-2">Candidato</th>
                                    <th class="text-sm text-left pb-2 text-right">Votos</th>
                                    <th class="text-sm text-left pb-2 text-right">Porcentage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-zinc-700">
                                <template x-for="candidateId in election.candidates">
                                    <tr>
                                        <td class="py-3">
                                            <div class="flex items-center gap-x-2">
                                                <img class="w-12 h-12" :src="users.get(candidateId).image_url"
                                                    alt="">
                                                <span x-text="users.get(candidateId).name"></span>
                                                <flux:icon.crown
                                                    x-show="totalVotesByCandidate(candidateId) >= votosNesesariosParaGanar"
                                                    class="text-yellow-500 size-8" />
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <span x-text="totalVotesByCandidate(candidateId) + ' votos'"></span>
                                        </td>
                                        <td class="text-right">
                                            <span
                                                x-text="Math.round(totalVotesByCandidate(candidateId) / room.voters.length * 100 * 10) / 10 + ' %'"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <td class="py-3 pl-14">Votos en blanco</td>
                                    <td class="text-right">
                                        <span x-text="totalVotesByCandidate(null) + ' votos'"></span>
                                    </td>
                                    <td class="text-right">
                                        <span
                                            x-text="(Math.round((totalVotesByCandidate(null))  / room.voters.length * 100 * 10) / 10) + ' %'"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 pl-14">Votos viciados</td>
                                    <td class="text-right">
                                        <span x-text="room.voters.length - getTotalVotes() + ' votos'"></span>
                                    </td>
                                    <td class="text-right">
                                        <span
                                            x-text="(Math.round((room.voters.length - getTotalVotes())  / room.voters.length * 100 * 10) / 10) + ' %'"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {{-- <div class="flex justify-center gap-x-3">
                            <div class="shadow flex-none w-64 p-4">
                                <div>Votos en blanco</div>
                                <div x-text="totalVotesByCandidate(null) + ' votos'"></div>
                                <div
                                    x-text="(Math.round((totalVotesByCandidate(null))  / room.voters.length * 100 * 10) / 10) + '%'">
                                </div>
                            </div>
                            <div class="shadow flex-none w-64 p-4">
                                <div>Votos viciado</div>
                                <div x-text="room.voters.length - getTotalVotes() + ' votos'"></div>
                                <div
                                    x-text="(Math.round((room.voters.length - getTotalVotes())  / room.voters.length * 100 * 10) / 10) + '%'">
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </template>

                <template x-if="!getCurrentElection()">
                    <div class="text-center mt-12">No hay eleccion activa</div>
                </template>

                {{-- <template x-for="[id, election] in Array.from(elections)">
                        <template x-if="election.id === currentElectionId" class="mt-12">
                            <div class="text-center text-xl font-medium"
                                x-text="'Candidatos para ' + cargos.get(election.cargo_id).name"></div>
                            <div class="flex flex-wrap justify-center gap-5 mt-5">
                                <template x-for="eligible in election.candidates">
                                    <div>
                                        <div
                                            class="aspect-square w-40 rounded-full overflow-hidden border-4 border-red-500">
                                            <img class="object-cover w-full" x-bind:src="users.get(eligible).image_url"
                                                alt="">
                                        </div>
                                        <div class="text-center font-medium mt-3" x-text="users.get(eligible).name">
                                        </div>
                                        <div x-show="election.status === 'closed'">
                                            <div x-text="totalVotesByCandidate(eligible) + ' votos'"></div>
                                            <div
                                                x-text="Math.round(totalVotesByCandidate(eligible) / room.voters.length * 100 * 10) / 10 + '%'">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </template> --}}

                <div class="fixed bottom-5 left-5 mr-64 flex flex-wrap gap-2">
                    <template x-for="[id, election] in Array.from(elections)">
                        <flux:button x-on:click="currentElectionId = election.id"><span
                                x-bind:class="id === currentElectionId ? 'text-blue-500' : ''"
                                x-text="cargos.get(election.cargo_id).name"></span></flux:button>
                    </template>
                    <flux:button x-on:click="currentElectionId = null">Ninguno</flux:button>
                </div>
                {{-- <flux:button x-on:click="addVoteTest()">Ninguno</flux:button> --}}
            </div>

            <div
                class="fixed top-0 bottom-0 right-0 w-64 overflow-y-auto bg-white dark:bg-zinc-900 border-l dark:border-l-zinc-700">
                <ul class="p-4 space-y-1">
                    <li>En linea</li>
                    <template x-for="user in online">
                        <li class="flex items-center gap-2">
                            <div
                                class="flex-none relative flex items-center justify-center border dark:border-zinc-700 w-8 h-8 rounded-md">
                                <span x-text="users.get(user).name[0].toUpperCase()"></span>
                                <div x-show="online.some(i => i === user)"
                                    class="absolute h-2 min-w-2 rounded-[3px] bottom-0 right-0 bg-green-500 dark:bg-green-400"
                                    aria-hidden="true"></div>
                            </div>
                            <div x-text="users.get(user).name" class="truncate flex-1"></div>
                        </li>
                    </template>
                </ul>

                <ul class="p-4 space-y-1">
                    <li>
                        Votantes
                        <flux:badge x-text="'En linea ' + votantesActivos()" color="green"></flux:badge>
                    </li>
                    <template x-for="user in room.voters">
                        <li class="flex items-center gap-2">
                            <div
                                class="flex-none relative flex items-center justify-center border dark:border-zinc-700 w-8 h-8 rounded-md">
                                <span x-text="users.get(user).name[0].toUpperCase()"></span>
                                <div x-show="online.some(i => i === user)"
                                    class="absolute h-2 min-w-2 rounded-[3px] bottom-0 right-0 bg-green-500 dark:bg-green-400"
                                    aria-hidden="true"></div>
                            </div>
                            <div x-text="users.get(user).name" class="truncate flex-1"></div>
                            <div x-show="room.votes.some(i => i.voter_id == user && i.election_id === currentElectionId)"
                                class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </template>

    {{-- <template x-if="!room.is_open">
            <div class="shadown p-4">
                <div>Ganadores</div>
                <ul class="flex gap-x-2 mt-3">
                    <template x-for="winner in winners" :key="winner.id">
                        <li class="flex-1 border max-w-[100px]">
                            <div class="bg-gray-100 aspect-square">
                                <template x-if="winner.photo_url">
                                    <img class="aspect-square object-cover" x-bind:src="winner.photo_url"
                                        alt="">
                                </template>
                            </div>
                            <div x-text="winner.name"></div>
                            <div x-text="winner.identification"></div>
                            <div x-text="cargos.find(i => i.id == winner.cargo_id)?.name"></div>
                            <div x-text="summaryVotes.find(i => i.candidate_id == winner.id)?.votes"></div>
                        </li>
                    </template>
                </ul>
            </div>
        </template> --}}

    <div x-data="{
        toasts: [],
        addToast(nombre) {
            const id = Date.now();
            this.toasts.unshift({ id, nombre }); // 🔥 Agrega el nuevo al inicio (arriba)
    
            if (this.toasts.length > 5) {
                this.toasts.pop(); // 🔥 Si se pasa, elimina el último (abajo)
            }
    
            setTimeout(() => this.removeToast(id), 5000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }" @toast.window="addToast($event.detail.nombre)">
        <!-- Contenedor -->
        <div
            class="fixed bottom-5 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 z-50 w-full max-w-sm px-4">

            <template x-for="toast in toasts" :key="toast.id">
                <div x-transition:enter="transition transform duration-300 ease-out"
                    x-transition:enter-start="translate-y-3 opacity-0 scale-95"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                    x-transition:leave="transition transform duration-200 ease-in"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="flex items-center gap-3 w-full bg-emerald-600/85 backdrop-blur-lg 
                       shadow-[0_8px_25px_rgba(0,0,0,0.25)] rounded-2xl px-4 py-3 text-white">
                    <!-- Icono -->
                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/25 text-xl">
                        🗳️
                    </div>

                    <!-- Texto -->
                    <div class="flex-1 leading-tight font-medium">
                        <span x-text="toast.nombre"></span>
                        <span class="opacity-80">emitió su voto</span>
                    </div>

                    <!-- Botón cerrar -->
                    <button @click="removeToast(toast.id)"
                        class="text-white/70 hover:text-white transition text-lg leading-none">
                        ✕
                    </button>
                </div>
            </template>

        </div>
    </div>

    @fluxScripts

    <script>
        var data = ({
            room: @json($event),
            cargos: new Map(@json($event->cargos).map(c => [c.id, c])),
            currentElectionId: @json($currentElectionId),
            election: @json($event->elections->find($currentElectionId)),
            eligibles: @json($event->candidates),
            winners: @json($event->winners),
            summaryVotes: [],
            select: null,
            sendingVote: false,
            users: new Map(@json($event->users).map(u => [u.id, u])),
            elections: new Map(@json($event->elections).map(e => [e.id, e])),
            online: [],

            init() {
                window.Echo.join(`events.{{ $event->id }}`)
                    .here((e) => {
                        this.online = e.map(e => e.id);

                        e.forEach(u => {
                            if (!this.users.has(u.id)) {
                                this.users.set(u.id, {
                                    identification: null,
                                    role_in_room: null,
                                    cargo_id: null,
                                    ...u,
                                });
                            }
                        });
                    })
                    .joining(e => {
                        this.online = [...this.online, e.id];

                        if (!this.users.has(e.id)) {
                            this.users.set(e.id, {
                                identification: null,
                                role_in_room: null,
                                cargo_id: null,
                                ...e,
                            })
                        }

                        console.log('se unio ' + e.name);
                    })
                    .leaving(e => {
                        this.online = this.online.filter(i => i !== e.id);
                        console.log('salio ' + e.name);
                    })
                    .listen('RoomUpdated', e => {                        
                        if (e.data.type === 'election-created') {
                            this.elections.set(e.data.election.id, {
                                id: e.data.election.id,
                                cargo_id: parseInt(e.data.election.cargo_id),
                                status: e.data.election.status,
                                candidates: e.data.candidates.map(c => parseInt(c)),
                            });
                        }
                    })
                    .listen('UpdateEvent', (e) => {
                        console.log('UpdateEvent', e);

                        this.room.is_open = e.event.is_open;
                    })
                    .listen('RegisteredVote', (e) => {
                        console.log(e);

                        this.room.votes.push(e.vote);


                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: {
                                nombre: `${this.users.get(e.vote.voter_id).name}`,
                            }
                        }));

                    })
                    .listen('ElectionToggleStatusEvent', e => {
                        console.log(e);

                        if (e.election.status === 'open') {
                            this.currentElectionId = e.election.id;
                        }

                        this.elections.get(e.election.id).status = e.election.status;
                    })
                    .listen('ElejiblesActualizadosEvent', (e) => {
                        console.log(e, 'ElejiblesActualizadosEvent');

                        this.eligibles = e.eligibles;
                    })
                    .listen('UpdateCandidateCargo', e => {
                        console.log(e);

                        if (!e.candidate.cargo_id) {
                            console.log('no tiene cargo');

                            this.winners = this.winners.filter(i => String(i.id) !== String(e
                                .candidate.id));

                            return;
                        }

                        if (this.winners.some(i => String(i.id) === String(e.candidate.id))) {
                            this.winners = this.winners.map(i => {
                                if (String(i.id) === String(e.candidate.id)) {
                                    return e.candidate;
                                }

                                return i;
                            })
                        } else {
                            this.winners = [...this.winners, e.candidate]
                        }

                    })
                    .listen('ElectionCreated', (e) => {
                        this.elections.set(e.election.id, {
                            id: e.election.id,
                            cargo_id: e.election.cargo_id,
                            status: e.status,
                            candidates: []
                        });
                    })
                    .listen('ElectionAnnulled', (e) => {
                        console.log(e);
                        const isDeleted = this.elections.delete(e.electionId);

                        if (isDeleted) {
                            this.currentElectionId = null;
                        }
                    })
                    .listen('ElectionToggleCandidateEvent', (e) => {
                        console.log(e);
                        var candidates = this.elections.get(e.electionId).candidates;

                        // Aplicar attached
                        e.changes.attached.forEach(id => {
                            if (!candidates.includes(id)) {
                                candidates.push(id)
                            }
                        })

                        // Aplicar detached
                        e.changes.detached.forEach(id => {
                            candidates = candidates.filter(c => c !== id)
                        })

                        this.elections.get(e.electionId).candidates = candidates;
                    });
            },

            get election() {
                return this.elections.get(this.currentElectionId);
            },

            votantesActivos() {
                return Array.from(this.users.values())
                    .filter(u =>
                        this.online.includes(u.id) &&
                        (u.role_in_room === 'voter' || u.role_in_room === 'both')
                    ).length
            },

            getCurrentElection() {
                return this.election;
            },

            getTotalVotes() {
                const election = this.election;

                if (!election) {
                    return '-';
                }

                return this.room.votes.reduce(function(total, item) {
                    if (item.election_id === election.id) {
                        return total + 1;
                    }

                    return total;
                }, 0);
            },

            totalVotesByCandidate(eligible) {
                return this.room.votes.filter(v => v.candidate_id === eligible && v.election_id ===
                    this.getCurrentElection().id).length;
            },

            get votosNesesariosParaGanar() {
                if (!this.election) {
                    return 0;
                }

                if (this.election.candidates.length > 2) {
                    return this.room.voters.length * 2 / 3;
                }

                return this.room.voters.length / 2 + 1;
            }
        });
    </script>
</body>

</html>
