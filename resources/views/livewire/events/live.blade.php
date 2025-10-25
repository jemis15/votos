<div class="p-6">

    <h1 class="font-medium text-center">{{ $event->name }}</h1>

    @if ($election)
        <h2 class="text-center text-2xl">Votar por {{ $election->cargo }}</h2>
    @endif

    @if ($voteBy)
        <div x-data="{ show: false }" x-on:click="show = !show">
            Tu voto ya fue registrado
            <div x-show="show">"{{ $eligibles?->where('id', $voteBy)->first()?->name }}"</div>
        </div>
    @endif

    <div class="grid grid-cols-2 justify-center gap-4 mt-5">
        @foreach ($eligibles as $eligible)
            <div class="flex-1 shadow p-3 rounded max-w-xs" wire:click="registerMyVoto({{ $eligible->id }})">
                @if ($eligible->photo_url)
                    <img class="aspect-square object-cover" src="{{ $eligible->photo_url }}" alt="">
                @else
                    <div class="aspect-square bg-gray-200"></div>
                @endif
                <div>{{ $eligible->name }}</div>
                <div>{{ $eligible->identification }}</div>
            </div>
        @endforeach
    </div>

    <div x-data="voteTracker({ electionId: 1 })">

        <div x-text="totalVotes"></div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('voteTracker', (props) => ({
                electionId: props.electionId,
                totalVotes: 0,

                init() {
                    console.log('Suscrito al canal votes...');
                    window.Echo.join(`events.{{ $event->id }}`)
                        .listen('UpdateEvent', (e) => {
                            console.log('evento obtenido');

                            this.totalVotes = this.totalVotes + 1
                        });
                }
            }));
        });

        // const channel = window.Echo.join(`events.{{ $event->id }}`);

        // channel.here(users => {
        //     console.log('users', users);
        // });

        // channel.joining(user => {
        //     console.log('user joined', user);
        // });

        // channel.listen('UpdateEvent', (e) => {
        //     console.log('evento UpdateEvent', e);
        // });

        // channel.listen('ElejiblesActualizadosEvent', (e) => {
        //     console.log('elegibles actualizado', e);
        // });
    </script>
</div>
