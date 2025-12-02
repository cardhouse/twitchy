<?php

use App\Models\Chatroom;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public bool $isPaused = false;
    public string $statusText = 'Active';
    public string $statusColor = 'green';

    public function mount(): void
    {
        $this->updateStatus();
    }

    public function toggle(): void
    {
        $chatroom = Chatroom::first();

        if (!$chatroom) {
            Flux::toast('No chatroom found', 'error');
            return;
        }

        if ($this->isPaused) {
            $chatroom->resume();
            Flux::toast('Stream resumed');
        } else {
            $chatroom->pause();
            Flux::toast('Stream paused');
        }

        $this->updateStatus();
    }

    private function updateStatus(): void
    {
        $chatroom = Chatroom::first();

        if (!$chatroom) {
            $this->isPaused = false;
            $this->statusText = 'No Chatroom';
            $this->statusColor = 'gray';
            return;
        }

        $this->isPaused = $chatroom->isPaused();
        $this->statusText = $this->isPaused ? 'Paused' : 'Active';
        $this->statusColor = $this->isPaused ? 'red' : 'green';
    }
}; ?>

<div class="flex items-center gap-3">
    <!-- Status Indicator -->
    <div class="flex items-center gap-2">
        <div class="h-2 w-2 rounded-full {{ $statusColor === 'green' ? 'bg-green-500' : ($statusColor === 'red' ? 'bg-red-500' : 'bg-gray-500') }}"></div>
        <span class="text-sm font-medium {{ $statusColor === 'green' ? 'text-green-700 dark:text-green-400' : ($statusColor === 'red' ? 'text-red-700 dark:text-red-400' : 'text-gray-700 dark:text-gray-400') }}">
            {{ $statusText }}
        </span>
    </div>

    <!-- Toggle Switch -->
    <flux:switch
        wire:model.live="isPaused"
        wire:click="toggle"
        size="sm"
    />
</div>

