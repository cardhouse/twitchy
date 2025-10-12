<?php

use App\Events\MessageDemoted;
use App\Messages\Actions\DemoteMessage;
use App\Messages\Stores\PromotedMessageStore;
use App\Models\Message;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Services\OverlayService;

new class extends Component {
    public Collection $messages;

    public function mount(): void
    {
        $this->messages = $this->loadNewMessages(2);
    }

    #[On('echo:local,MessagePromoted')]
    public function addMessage($event): void
    {
        $this->refreshMessages();
        Flux::toast('New promoted message from ' . ($event['message']['display_name'] ?? 'Unknown'));
    }

    public function clearMessages(): void
    {
        $this->messages->clear();
        app(\App\Messages\Actions\ClearPromotedMessages::class)->handle();
        Flux::toast('All promoted messages cleared');
    }

    public function nextMessage(): void
    {
        if ($this->messages->isNotEmpty()) {
            $message = $this->messages->first();
            app(DemoteMessage::class)->handle($message['id']);
        }
    }

    #[On('echo:local,MessageDemoted')]
    public function refreshMessages(): void
    {
        $this->messages = $this->loadNewMessages(2);
        Flux::toast('Promoted messages refreshed');
    }

    private function loadNewMessages(int $count = 10): Collection
    {
        return app(PromotedMessageStore::class)->list($count);
    }
}; ?>

<div class="flex h-full min-h-0 flex-col overflow-hidden">
    <!-- Preview Header -->
    <div class="flex shrink-0 items-center justify-between">
        <h3 class="text-lg font-medium text-white">Highlighted</h3>
        <div class="flex items-center space-x-2">
            @if($messages->isNotEmpty())
                <flux:button wire:click="nextMessage" size="sm">
                    Next
                </flux:button>
                <flux:button wire:click="clearMessages" variant="danger" size="sm">
                    Clear All
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Toast Preview -->
    <div class="mt-6 flex-1 overflow-y-auto space-y-6 pr-2">
        @forelse($messages as $currentToast)
            <div class="space-y-4" wire:key="{{$currentToast['id']}}">
                <!-- Status Indicator -->
                <div class="flex items-center space-x-2">
                    <div class="h-3 w-3 animate-pulse rounded-full bg-green-500"></div>
                    <span class="font-medium text-green-400">Active Toast</span>
                    <span class="text-sm text-gray-400">
                        Queued {{ \Carbon\Carbon::parse($currentToast['timestamp'])->diffForHumans() }}
                    </span>
                </div>

                <!-- Toast Preview Panel -->
                <div class="rounded-lg border-2 border-green-500/30 bg-gray-900 p-6">
                    <div class="mb-4 text-sm text-gray-400">Preview (as it appears on overlay):</div>

                    <!-- Mock Toast Display -->
                    <div class="max-w-lg rounded-lg border border-gray-600 bg-gray-800/90 p-4 backdrop-blur-sm">
                        <!-- Message Header -->
                        <div class="mb-2 flex items-center gap-2">
                            <!-- Badges -->
                            @if(isset($currentToast['badges']) && is_array($currentToast['badges']))
                                @foreach($currentToast['badges'] as $badge)
                                    @if(isset($badge['name']))
                                        <flux:badge variant="primary" size="sm">
                                            {{ $badge['name'] }}
                                        </flux:badge>
                                    @endif
                                @endforeach
                            @endif

                            <!-- Display Name -->
                            <span class="text-lg font-bold text-white">
                                {{ $currentToast['display_name'] ?? $currentToast['username'] ?? 'Unknown' }}
                            </span>
                        </div>

                        <!-- Message Content -->
                        <div class="break-words text-base leading-relaxed text-gray-200">
                            {{ $currentToast['message'] ?? '' }}
                        </div>
                    </div>

                    <!-- Toast Configuration -->
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-400">Duration:</span>
                            <span class="ml-2 text-white">{{ $currentToast['options']['duration_ms'] ?? 8000 }}ms</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Theme:</span>
                            <span class="ml-2 text-white">{{ ucfirst($currentToast['options']['theme'] ?? 'dark') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Font Scale:</span>
                            <span class="ml-2 text-white">{{ $currentToast['options']['fontScale'] ?? 1.0 }}x</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Animation:</span>
                            <span
                                class="ml-2 text-white">{{ ucfirst(str_replace('-', ' ', $currentToast['options']['animation'] ?? 'slide-up')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- No Active Toast -->
            <div class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-700">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.013 8.013 0 01-7-4c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-medium text-gray-300">No Active Toast</h3>
                <p class="text-sm text-gray-500">
                    Activate a message from the chat feed to see it here
                </p>
            </div>
        @endforelse
    </div>

    <!-- Auto-refresh Info -->
    <div class="mt-6 shrink-0 text-center text-xs text-gray-500">
        @if($lastChecked ?? false)
            Last checked: {{ $lastChecked->format('H:i:s') }}
        @endif
        <button wire:click="refreshPreview" class="ml-2 text-blue-400 hover:text-blue-300">
            Check now
        </button>
    </div>

    <!-- Direct Test Controls (Development) -->
    @if(config('app.env') === 'local')
        <div class="mt-6 shrink-0 border-t border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-300 mb-3">Development Tools</h4>
            <div class="space-y-2">
                <flux:button onclick="testToast()" variant="outline" size="sm" class="w-full">
                    Trigger Test Toast
                </flux:button>
                <flux:button onclick="window.open('{{ route('overlay.show', 'local') }}', '_blank')" variant="outline"
                             size="sm" class="w-full">
                    Open Overlay in New Tab
                </flux:button>
            </div>
        </div>
    @endif
</div>
