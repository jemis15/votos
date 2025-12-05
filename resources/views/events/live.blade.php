<x-layouts.app title="Eventos">
    <div x-data="data">

        <template x-if="!room.is_open">
            <div class="h-full flex flex-col items-center justify-center max-w-xl mx-auto px-6 md:px-0">
                <div>
                    <img src="{{ asset('images/logo.png') }}" class="max-w-64 max-h-36" alt="logo denec">
                </div>
                <h2 class="font-medium text-xl mt-12">{{ $room->name }}</h2>
                <div class="mt-2">Bienvenidos, espere la apertura de esta sala.</div>
            </div>
        </template>

        <template x-if="room.is_open">
            <div class="pb-6 max-w-lg mx-auto">
                <div class="flex justify-center">
                    <img src="{{ asset('images/logo.png') }}" class="w-60 dark:hidden" alt="logo denec">
                    <img src="{{ asset('images/logo-blank.png') }}" class="w-60 hidden dark:block" alt="logo denec">
                </div>

                <template x-if="!election">
                    <div class="mt-4">
                        <h2 class="text-xl font-medium text-center text-sky-500">No hay eleccion</h2>
                        <div class="mt-2 text-gray-500 text-center">Espere a que se habilite una eleccion</div>
                    </div>
                </template>

                <template x-if="myVote">
                    <div x-data="{ show: false }">
                        <div class="text-center text-xl font-medium text-sky-500 mt-4">¡Voto registrado!</div>
                        <div x-on:click="show = !show" class="text-gray-500 underline mt-3 text-center">
                            <span x-text="!show ? 'Mostrar mi voto' : 'Ocultar mi voto'"></span>
                        </div>

                        <div x-show="show && !myVote?.candidate_id" class="p-4 shadow rounded-md mt-4">Registrate voto
                            en blanco</div>

                        <div x-show="show && myVote?.candidate_id !== null" class="p-4 shadow rounded-md mt-4 dark:border dark:border-zinc-700">
                            <flux:text x-text="cargos.get(election.cargo_id).name" />
                            <div class="flex items-center gap-x-4 mt-2">
                                <div class="w-12 h-12 rounded-md overflow-hidden border">
                                    <img class="w-full h-full object-cover"
                                        :src="users.get(myVote?.candidate_id)?.image_url" alt="">
                                </div>
                                <div>
                                    <flux:heading x-text="users.get(myVote?.candidate_id)?.name" />
                                    <div class="text-sky-500 font-medium" x-text="users.get(myVote?.candidate_id).instance"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="election && !myVote">
                    <div class="pb-6 mt-5">
                        <div class="text-center text-xl font-medium text-sky-500">
                            <span x-text="cargos.get(election.cargo_id).name"></span>
                        </div>

                        <div class="text-center text-gray-700 dark:text-gray-400" x-show="!myVote">Seleccione tu
                            candidato</div>

                        {{-- <ul class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-4 mt-5">
                            <li class="shadow p-2 flex items-center justify-center rounded-md min-h-[200px] hover:-translate-y-1 hover:scale-110 transition delay-150 duration-300 ease-in-out"
                                x-on:click="if (!myVote) { select=null, $flux.modal('register-vote').show() }">
                                <div>Voto en blanco</div>
                            </li>
                            <template x-for="eligible in election.candidates">
                                <li class="shadow p-2 rounded-md overflow-hidden group hover:-translate-y-1 hover:scale-110 transition delay-150 duration-300 ease-in-out"
                                    x-on:click="if (!myVote) { select = users.get(eligible), $flux.modal('register-vote').show() }">
                                    <div class="bg-gray-100 aspect-square -mx-2 -mt-2">
                                        <template x-if="users.get(eligible).image_url">
                                            <img class="aspect-square object-cover group-hover:contrast-200 transition"
                                                x-bind:src="users.get(eligible).image_url" alt="">
                                        </template>
                                    </div>
                                    <div x-text="users.get(eligible).name" class="mt-2 group-hover:font-medium"></div>
                                </li>
                            </template>
                        </ul> --}}

                        <ul class="space-y-2 mt-5">
                            <template x-for="eligible in election.candidates">
                                <li class="flex shadow-sm p-2 rounded-lg items-center gap-x-4 dark:bg-zinc-700 cursor-pointer"
                                    :class="{ 'ring-2 ring-sky-500 dark:ring-zinc-400': select === eligible }"
                                    x-on:click="handleCandidateSelect(eligible)">
                                    <div class="ring ring-gray-300 w-16 h-16 rounded-lg overflow-hidden">
                                        <template x-if="users.get(eligible)?.image_url">
                                            <img x-show="users.get(eligible)?.image_url"
                                                class="aspect-square w-20 object-cover"
                                                x-bind:src="users.get(eligible)?.image_url" alt="">
                                        </template>
                                    </div>
                                    <div>
                                        <flux:heading size="lg" x-text="users.get(eligible)?.name" />
                                        <div class="text-sky-500 font-medium" x-text="users.get(eligible)?.instance">
                                        </div>
                                    </div>
                                </li>
                            </template>
                            <li class="shadow-sm py-6 flex items-center justify-center rounded-md dark:bg-zinc-700 cursor-pointer"
                                x-on:click="handleCandidateSelect(null), $flux.modal('register-vote').show()">
                                <div>Voto en blanco</div>
                            </li>
                        </ul>
                </template>
            </div>
        </template>

        <div x-show="select" class="fixed bottom-0 left-0 right-0 flex justify-end px-4 py-2 lg:ml-64">
            <flux:button class="w-full max-w-lg mx-auto" variant="primary" color="sky"
                x-on:click="$flux.modal('register-vote').show()">
                Votar
            </flux:button>
        </div>

        <div @notify.window="$flux.modal('register-vote').close()"></div>

        <flux:modal name="register-vote" class="w-88">
            <template x-if="!select">
                <div>
                    <flux:heading size="lg">Confirma tu voto en blanco</flux:heading>
                    <flux:text class="mt-2">
                        Estás a punto de registrar un voto en blanco
                    </flux:text>
                </div>
            </template>
            <template x-if="users.get(select)">
                <div>
                    <flux:heading size="lg">Confirma tu voto</flux:heading>

                    <div class="bg-sky-50 dark:bg-zinc-700 flex flex-col items-center py-4 rounded-md mt-2">
                        <div
                            class="w-24 h-24 ring ring-sky-500 rounded-md bg-white dark:bg-transparent overflow-hidden">
                            <template x-if="users.get(select).image_url">
                                <img class="w-24 h-24 object-cover" :src="users.get(select).image_url" alt="">
                            </template>
                        </div>
                        <div class="mt-4" x-text="users.get(select).name">
                        </div>
                        <div class="text-sky-500 font-medium" x-text="users.get(select).instance"></div>
                    </div>
                </div>
            </template>

            <div class="flex gap-x-4 mt-4">
                <flux:button class="flex-1" x-on:click="$flux.modal('register-vote').close()">
                    Cancelar
                </flux:button>
                <flux:button class="flex-1" variant="primary" color="sky" x-on:click="registerVote(select)">
                    Emitir voto
                    <flux:icon.loading x-show="sendingVote" />
                </flux:button>
            </div>
        </flux:modal>

    </div>

    <script>
        var data = ({
            room: @json($room),
            eligibles: @json($room->candidates),
            myVotes: @json($myVotes),
            // select: null,
            sendingVote: false,
            currentElectionId: @json($currentElectionId),
            users: new Map(@json($room->candidates).map(u => [u.id, u])),
            elections: new Map(@json($room->elections).map(e => [e.id, e])),
            cargos: new Map(@json($room->cargos).map(c => [c.id, c])),

            dataCandidateSelected: {
                candidateId: null,
                electionId: null
            },

            init() {
                window.Echo.join(`events.{{ $room->id }}`)
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
                        this.room.is_open = e.event.is_open;
                    })
                    .listen('ElectionCreated', (e) => {
                        this.elections.set(e.election.id, {
                            id: e.election.id,
                            cargo_id: e.election.cargo_id,
                            status: e.status,
                            candidates: []
                        });
                    })
                    .listen('ElectionToggleStatusEvent', e => {
                        console.log(e);

                        this.elections.get(e.election.id).status = e.election.status;

                        if (e.election.status === 'open') {
                            this.currentElectionId = e.election.id;
                        } else {
                            this.currentElectionId = null;
                        }

                    })
                    .listen('ElejiblesActualizadosEvent', (e) => {
                        console.log(e, 'ElejiblesActualizadosEvent');

                        this.eligibles = e.eligibles;
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

            handleCandidateSelect(candidateId) {
                this.dataCandidateSelected = {
                    electionId: this.election.id,
                    candidateId: candidateId
                };
            },

            registerVote() {
                if (!this.election || this.election.status === 'closed') return;

                if (this.sendingVote) return;

                this.sendingVote = true;

                fetch('/register-vote', {
                        method: 'post',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _token: @json(csrf_token()),
                            electionId: this.election.id,
                            candidateId: this.select,
                        })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.myVotes.push({
                                election_id: this.election.id,
                                candidate_id: this.select
                            });
                            this.dataCandidateSelected = {
                                electionId: null,
                                candidateId: null
                            };
                        } else if (d.success === false) {
                            alert(d.message);
                        }

                        window.dispatchEvent(new CustomEvent('notify'));
                    })
                    .finally(() => this.sendingVote = false);
            },

            get select() {
                if (this.dataCandidateSelected?.electionId === this.election?.id) {
                    return this.dataCandidateSelected.candidateId;
                }

                return null;
            },

            get election() {
                return this.elections.get(this.currentElectionId);
            },

            get myVote() {
                return this.myVotes.find(i => i.election_id === this.election?.id);
            }
        })
    </script>
</x-layouts.app>
