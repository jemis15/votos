<x-layouts.app title="Editar cargo">
    <flux:button href="{{ route('events.show', $cargo->event_id) }}" icon="arrow-left" class="mb-4">Atras</flux:button>

    <h1 class="text-xl mb-4">Editar cargo</h1>
    <form action="{{ route('cargos.update', $cargo->id) }}" method="POST" class="flex flex-col gap-4"
        onsubmit="handleSubmit(e)">
        @csrf
        @method('PUT')
        <flux:field>
            <flux:label>Nombre</flux:label>

            <flux:input name="name" type="text" value="{{ old('name', $cargo->name) }}" />

            <flux:error name="name" />
        </flux:field>
        <div>
            <flux:button href="{{ route('events.show', $cargo->event_id) }}">Cancelar</flux:button>
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
