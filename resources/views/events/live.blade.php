<x-layouts.app title="Eventos">
    <div x-data="data" class="h-full">

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
            <div class="flex flex-col h-full">
                <template x-if="!election">
                    <div class="h-full flex flex-col items-center justify-center">
                        <h2 class="text-xl font-medium">No hay eleccion</h2>
                        <div class="mt-2 text-gray-600">Espere a que se habilite una eleccion</div>
                    </div>
                </template>

                <template x-if="election">
                    <div class="px-4 pb-6">
                        <flux:icon.crown class="size-20" />
                        <div class="">
                            Elige tu candidato para <span x-text="cargos.get(election.cargo_id).name"></span>
                        </div>

                        <p class="text-gray-700" x-show="!myVote">Seleccione un candidato</p>

                        <div x-show="myVote">
                            <div x-data="{ show: false }">
                                <div x-on:click="show = !show" class="text-blue-500 mt-3">Tu voto ya fue registrado.
                                    Toca
                                    aqui
                                    para ver.</div>
                                <div x-show="show && !myVote.candidate_id">Registrate un voto en blanco</div>
                                <div x-show="show && myVote.candidate_id !== null"
                                    x-text="'Votaste por ' + users.get(myVote?.candidate_id)?.name">
                                </div>
                            </div>
                        </div>

                        <ul class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-4 mt-5">
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
                        </ul>
                    </div>
                </template>
            </div>
        </template>


        <div>
            <div @notify.window="$flux.modal('register-vote').close()"></div>

            <flux:modal name="register-vote" class="md:w-96">
                <template x-if="!select">
                    <div>
                        <flux:heading size="lg">Voto en blanco</flux:heading>
                        <p class="mt-2">Estás a punto de registrar un voto en blanco para las elecciones de
                            <span x-text="election?.cargo"></span>.
                        </p>

                        <div class="flex mt-6">
                            <flux:spacer />

                            <flux:button variant="primary" x-on:click="registerVote(null)">
                                Emitir voto en blanco
                                <flux:icon.loading x-show="sendingVote" />
                            </flux:button>
                        </div>
                    </div>
                </template>
                <template x-if="select">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Registar mi voto</flux:heading>
                            <flux:text class="mt-2" x-text="select.name"></flux:text>
                        </div>

                        <div class="flex">
                            <flux:spacer />

                            <flux:button variant="primary" x-on:click="registerVote(select.id)">
                                Emitir voto
                                <flux:icon.loading x-show="sendingVote" />
                            </flux:button>
                        </div>
                    </div>
                </template>
            </flux:modal>
        </div>

    </div>

    <script>
        const data = ({
            room: @json($room),
            eligibles: @json($room->candidates),
            myVotes: @json($myVotes),
            select: null,
            sendingVote: false,
            currentElectionId: @json($currentElectionId),
            users: new Map(@json($room->candidates).map(u => [u.id, u])),
            elections: new Map(@json($room->elections).map(e => [e.id, e])),
            cargos: new Map(@json($room->cargos).map(c => [c.id, c])),

            init() {
                window.Echo.join(`events.{{ $room->id }}`)
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

            registerVote(candidateId) {
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
                            candidateId: candidateId,
                        })
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.myVotes.push({
                                election_id: this.election.id,
                                candidate_id: candidateId
                            });
                        } else if (d.success === false) {
                            alert(d.message);
                        }

                        window.dispatchEvent(new CustomEvent('notify'));
                    })
                    .finally(() => this.sendingVote = false);
            },

            get election() {
                return this.elections.get(this.currentElectionId);
            },

            get myVote() {
                return this.myVotes.find(i => i.election_id === this.election.id);
            }
        })
    </script>
</x-layouts.app>
