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

<body>
    <div x-data="eventTracker()">
        <div>
            <div>
                Bienvenido {{ auth()->user()->name }} a la sala <span x-text="room.name"></span>
                <flux:badge color="lime" x-show="room.is_open">Abierto</flux:badge>
                <flux:badge color="red" x-show="!room.is_open">Cerrado</flux:badge>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        Cerrar sesion
                    </flux:menu.item>
                </form>
            </div>
        </div>

        <div x-show="election">
            Elecciones para
            <span x-text="election?.cargo"></span>
            ...
        </div>

        <div x-show="voteById">
            <div x-data="{ show: false }">
                <div x-on:click="show = !show">Tu voto ya fue registrado. Toca aqui para ver.</div>
                <div x-show="show"
                    x-text="'votaste por ' + eligibles.find(i => String(i.id) === String(voteById))?.name"></div>
            </div>
        </div>

        <ul class="flex flex-wrap gap-3">
            <template x-for="eligible in eligibles" :key="eligible.id">
                <li class="shadow p-2">
                    <div x-text="eligible.name"></div>

                    <flux:button x-show="!voteById && election !== null"
                        x-on:click="select = eligible; $flux.modal('register-vote').show()">
                        Registar voto
                    </flux:button>
                </li>
            </template>
        </ul>

        <div @notify.window="$flux.modal('register-vote').close()"></div>

        <flux:modal name="register-vote" class="md:w-96">
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

    @if (!$event->is_open)
        <ul>
            @foreach ($winners as $item)
                <li>
                    <div>
                        {{ $item->name }} {{$item->cargo->name}}
                        <flux:badge color="lime">Votos obtenidos {{ $item->votes_count }}</flux:badge>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    @fluxScripts

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('eventTracker', (props) => ({
                room: @json($event),
                eligibles: @json($eligibles),
                voteById: @json($voteById),
                election: @json($election),
                totalVotes: 0,
                select: null,
                sendingVote: false,

                init() {
                    window.Echo.join(`events.{{ $event->id }}`)
                        .listen('UpdateEvent', (e) => {
                            console.log('UpdateEvent', e);
                            this.room = e.event;
                        })
                        .listen('ElectionToggleStatusEvent', e => {
                            console.log(e);
                            if (e.election.status === 'closed') {
                                this.election = null;
                            } else {
                                this.election = e.election;
                                this.election.cargo = e.cargo.name
                            }
                        })
                        .listen('ElejiblesActualizadosEvent', (e) => {
                            console.log(e);

                            this.eligibles = e.eligibles;
                        })
                        .listen('ElectionAnnulled', (e) => {
                            this.election = null;
                            this.voteById = null;
                        });
                },

                registerVote(candidateId) {
                    if (!this.election) return;

                    if (this.sendingVote) return

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
                                this.voteById = candidateId;
                                alert(d.message);
                            } else if (d.success === false) {
                                alert(d.message);
                            }

                            window.dispatchEvent(new CustomEvent('notify'));
                        })
                        .finally(() => this.sendingVote = false);
                }
            }));
        });
    </script>
</body>

</html>
