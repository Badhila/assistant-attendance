<div>
    <form wire:submit.prevent="create" class="space-y-6">
        <div wire:poll="updateRfid">
            {{ $this->form }}
        </div>

        <div class="flex gap-2 grid-cols-3 mt-6">
            <x-filament::button wire:click="create">
                {{ __('Create') }}
            </x-filament::button>
            <x-filament::button color="gray" wire:click="cancel">
                {{ __('Cancel') }}
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</div>
