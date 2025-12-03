<div>
    <flux:modal name="two-factor-setup-modal" class="max-w-md md:min-w-md" @close="closeModal" wire:model="showModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Delete Election
            </h2>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Are you sure you want to delete this election? This action cannot be undone.
            </p>
            <div>{{ $cargo }}</div>
            <div>Numero {{ $number }}</div>
            <button wire:click="$refresh">eliminar</button>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                    @click="closeModal">
                    Cancel
                </button>
                <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                    wire:click="deleteElection">
                    Delete
                </button>
            </div>
        </div>
    </flux:modal>
</div>
