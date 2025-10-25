<!DOCTYPE html>
<html lang="en">

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
</head>

<body x-data="eventTracker()">
    <div>
        <span x-text="'Sala ' + room.name"></span>
        <flux:badge color="lime" x-show="room.is_open">Abierto</flux:badge>
        <flux:badge color="red" x-show="!room.is_open">Cerrado</flux:badge>
    </div>

    <template x-if="room.is_open">
        <div>

            <template x-if="election">
                <div>
                    <h2 class="text-xl" x-text="'Elecciones para ' + election.cargo"></h2>
                    <div x-text="'Estado de la eleccion ' + election.status"></div>
                </div>
            </template>

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
                        </li>
                    </template>
                </ul>
            </div>

            <div class="shadown p-4">
                <div>Eligibles</div>
                <ul class="flex gap-x-2 mt-3">
                    <template x-for="eligible in eligibles" :key="eligible.id">
                        <li class="flex-1 border max-w-[200px]">
                            <div class="bg-gray-100 aspect-square">
                                <template x-if="eligible.photo_url">
                                    <img class="aspect-square object-cover" x-bind:src="eligible.photo_url"
                                        alt="">
                                </template>
                            </div>
                            <div x-text="eligible.name"></div>
                            <div x-text="eligible.identification"></div>
                            <template x-if="election?.status === 'closed'">
                                <div
                                    x-text="'Votos obtenidos '+ votes.reduce((total, data) => (data.candidate_id == eligible.id ? total + 1 : total), 0)">
                                </div>
                            </template>
                        </li>
                    </template>
                </ul>
            </div>


            <ul class="shadow p-4">
                <li>En linea</li>
                <template x-for="user in users" :key="user.id">
                    <li>
                        <div x-text="user.name"></div>
                    </li>
                </template>
            </ul>

            <ul class="shadow p-4">
                <li>
                    Votantes
                    <flux:badge x-text="'Votantes ' + votes.length"></flux:badge>
                    <flux:badge x-text="'En linea ' + users.length" color="green"></flux:badge>
                    <flux:badge x-text="'Votos ' + votes.length" color="blue"></flux:badge>
                </li>
                <template x-for="user in voters" key="user.id">
                    <li>
                        <span x-text="user.name"></span>
                        <flux:badge x-show="users.some(i => i.id == user.id)" color="green">En linea</flux:badge>
                        <flux:badge x-show="votes.some(i => i.user_id == user.id)" color="blue">Voto</flux:badge>
                    </li>
                </template>
            </ul>

            {{-- <ul class="shadow p-4">
            <li>Votos</li>
            <template x-for="vote in votes" key="vote.id">
                <li>
                    <div x-text="vote.user_id"></div>
                </li>
            </template>
        </ul> --}}

        </div>
    </template>

    <template x-if="!room.is_open">
        <div class="shadown p-4">
            <div>Ganadores</div>
            <ul class="flex gap-x-2 mt-3">
                <template x-for="winner in winners" :key="winner.id">
                    <li class="flex-1 border max-w-[100px]">
                        <div class="bg-gray-100 aspect-square">
                            <template x-if="winner.photo_url">
                                <img class="aspect-square object-cover" x-bind:src="winner.photo_url" alt="">
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
    </template>

    @fluxScripts

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('eventTracker', (props) => ({
                room: @json($event),
                cargos: @json($event->cargos),
                election: @json($election),
                eligibles: @json($eligibles),
                winners: @json($winners),
                summaryVotes: @json($summaryVotes),
                totalVotes: 0,
                select: null,
                sendingVote: false,
                users: [],
                votes: @json($votes),
                voters: @json($event->voters),

                init() {
                    window.Echo.join(`events.{{ $event->id }}`)
                        .here((e) => {
                            this.users = e;
                        })
                        .joining(e => {
                            this.users = [...this.users, e];
                            console.log('se unio ' + e.name);

                        })
                        .leaving(e => {
                            this.users = this.users.filter(i => i.id !== e.id);
                            console.log('salio ' + e.name);
                        })
                        .listen('UpdateEvent', (e) => {
                            console.log('UpdateEvent', e);
                            this.room = e.event;

                            if (!e.event.is_open) {
                                location.reload();
                            }
                        })
                        .listen('RegisteredVote', (e) => {
                            console.log(e);
                            alert(this.users.find(i => i.id == e.vote.user_id)?.name +
                                ' registro su voto')

                            this.votes = [...this.votes, e.vote]

                            console.log(this.users.find(i => i.id == e.vote.user_id)?.name +
                                ' registro su voto');
                        })
                        .listen('ElectionToggleStatusEvent', e => {
                            console.log(e);

                            this.election = e.election;
                            this.election.cargo = e.cargo.name;
                        })
                        .listen('ElejiblesActualizadosEvent', (e) => {
                            console.log(e);

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

                        });
                }
            }));
        });
    </script>
</body>

</html>
