<x-layouts.app title="Crear cargoa">
    <flux:button href="{{ route('events.show', $event->id) }}" icon="arrow-left" class="mb-4">Atras</flux:button>

    <h1 class="text-xl mb-4">Crear cargo para la sala <b>{{ $event->name }}</b></h1>

    <form action="{{ route('cargos.store') }}" method="POST" class="flex flex-col gap-4" onsubmit="handleSubmit(event)">
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id }}">
        <div>
            <flux:field>
                <flux:label>Nombre del cargo</flux:label>

                <flux:input name="name" type="text" value="{{ old('name') }}" />

                <flux:error name="name" />
            </flux:field>

            <flux:error name="event_id" />
        </div>
        <div>
            <flux:button href="{{ route('events.show', $event->id) }}">Cancelar</flux:button>
            <flux:button type="submit" id="btnSubmit" variant="primary">Actualizar cargo</flux:button>
        </div>
    </form>

    <script>
        function handleSubmit(e) {
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.setAttribute('disabled', '');
        }
    </script>

</x-layouts.app>
