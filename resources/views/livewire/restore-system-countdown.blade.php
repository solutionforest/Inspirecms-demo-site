<div wire:init="updateCountdown" wire:poll.visible wire:poll.1s="updateCountdown">
    <x-inspirecms::alert color="danger" size="md" icon="heroicon-o-exclamation-circle" class="mt-3">
        <x-slot:message>
            {{ $this->getDisplayMessage() }} 
        </x-slot:message>
    </x-inspirecms::alert>
</div>