<?php

namespace App\Livewire\Overlay;

use App\Messages\Stores\PromotedMessageStore;
use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

class ToastDisplay extends Component
{
    public $overlayKey = '';

    public ?Message $currentMessage = null;

    public function mount(PromotedMessageStore $store): void
    {
        $this->currentMessage = $store->list(1)->mapInto(Message::class)->first();

        // Dispatch event to trigger typing animation on initial load
        $this->dispatch('message-updated');
    }

    #[On('echo:local,MessagePromoted')]
    public function nextMessage($event): void
    {
        $this->currentMessage = Message::find($event['message']['id']) ?: $this->getFirstMessage();

        // Dispatch event to trigger typing animation
        $this->dispatch('message-updated');
    }

    #[On('echo:local,MessageDemoted')]
    public function clearMessage(): void
    {
        $this->currentMessage = $this->getFirstMessage();
        $this->dispatch('message-updated');
    }

    public function render()
    {
        return view('livewire.overlay.toast-display');
    }

    /**
     * @return Message|null
     */
    public function getFirstMessage(): ?Message
    {
        return app(PromotedMessageStore::class)
            ->list(1)
            ->mapInto(Message::class)
            ->first();
    }
}
