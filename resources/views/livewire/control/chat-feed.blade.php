<?php

use App\Messages\Actions\ClearMessages;
use App\Messages\Stores\ChatMessageStore;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $messages;
    public string $search = '';
    public string $filter = 'all';
    public int $selectedDuration = 8000;
    public ?int $activatingMessage;

    public function mount(ChatMessageStore $store): void
    {
        $this->messages = $store->list(100);
    }

    #[On('echo:local,MessageReceived')]
    public function addMessage($event): void
    {
        $this->messages->push($event['message']);
        Flux::toast('New message received from ' . ($event['message']['display_name'] ?? 'Unknown'));
    }

    public function promoteMessage(\App\Models\Message $message): void
    {
        app(\App\Messages\Actions\PromoteMessage::class)->handle($message);
        Flux::toast('Message promoted');
    }

    public function clearMessages(): void
    {
        app(ClearMessages::class)->handle();
        $this->messages->clear();
        Flux::toast('All messages cleared');
    }

    public function getFilteredMessagesProperty()
    {
        $filtered = $this->messages;

        // Apply search filter
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(function ($message) use ($search) {
                return str_contains(strtolower($message['display_name']), $search) ||
                    str_contains(strtolower($message['username']), $search) ||
                    str_contains(strtolower($message['message']), $search);
            });
        }

        // Apply badge filter
        if ($this->filter !== 'all') {
            $filtered = $filtered->filter(function ($message) {
                $badgeNames = collect($message['badges'])->pluck('name')->toArray();
                return in_array($this->filter, $badgeNames);
            });
        }

        return $filtered->toArray();
    }
}; ?>

<div class="flex h-full min-h-0 flex-col overflow-hidden">
    {{-- Secondary header --}}
    {{--    <div class="sm:border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">--}}
    <div
        class="max-w-7xl px-6 sm:px-8 py-3 mx-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-3
        sm:gap-2">
        <div class="max-sm:hidden flex items-baseline gap-3">
            <flux:heading size="lg" class="text-lg">Messages</flux:heading>

            <flux:text size="lg">{{ count($messages) }}</flux:text>
        </div>

        <flux:spacer/>

        {{--    Filters    --}}
        <div class="flex items-center gap-2">
            <flux:select variant="listbox" wire:model.live="filter" class="sm:max-w-fit">
                <x-slot name="trigger">
                    <flux:select.button size="sm">
                        <flux:icon.funnel variant="micro" class="mr-2 text-zinc-400"/>
                        <flux:select.selected/>
                    </flux:select.button>
                </x-slot>

                <flux:select.option value="all" selected>All</flux:select.option>
                <flux:select.option value="moderator">Moderator</flux:select.option>
                <flux:select.option value="subscriber">Subscriber</flux:select.option>
                <flux:select.option value="vip">VIP</flux:select.option>
            </flux:select>

            <flux:input
                id="search"
                icon="magnifying-glass"
                size="sm"
                wire:model.live.debounce.300ms="search"
                placeholder="Search for content..."
                type="text"
                class="sm:max-w-fit"
            />

        </div>

        <flux:button icon="pencil-square" size="sm" variant="primary" wire:click="clearMessages">
            Clear Messages
        </flux:button>
    </div>
    {{--    </div>--}}

    <div class="mt-4 flex-1 overflow-y-auto">
        <div class="mx-auto max-w-lg space-y-3 max-sm:px-2 pb-4">
            {{-- Loop: questions... --}}
            @forelse ($this->filteredMessages as $message)

                <div wire:key="{{ $message['id'] }}" wire:click="promoteMessage({{$message['id']}})" class="rounded-lg p-3 sm:p-4 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                    <div class="flex flex-row gap-2 sm:items-center">
                        <flux:avatar src="https://randomuser.me/api/portraits/men/{{$loop->iteration}}.jpg" size="xs"
                                     class="shrink-0"/>

                        <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:gap-2">
                            <div class="flex items-center gap-2">
                                <flux:heading>{{ $message['username'] }}</flux:heading>

                                <flux:badge color="lime" size="sm" icon="check-badge" inset="top bottom">Moderator
                                </flux:badge>
                            </div>

                            <flux:text
                                class="text-sm">{{ Carbon::parse($message['timestamp'])->diffForHumans() }}
                            </flux:text>
                        </div>
                    </div>

                    <div class="min-h-2 sm:min-h-1"></div>

                    <div class="pl-8">
                        <flux:text variant="strong">{{ $message['message'] }}</flux:text>
                    </div>
                </div>
            @empty

            @endforelse
        </div>
    </div>
</div>
