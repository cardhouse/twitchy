@extends('layouts.control')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:flex-1 lg:min-h-0 lg:grid-cols-3">
        <!-- Chat Feed Section -->
        <flux:card class="flex flex-col lg:col-span-2 lg:h-full lg:min-h-0">
            <livewire:control.chat-feed />
        </flux:card>

        <!-- Toast Preview Section -->
        <flux:card class="z-10 flex flex-col self-start lg:sticky lg:top-4 lg:h-full lg:min-h-0">
            <x-slot name="heading">Toast Preview</x-slot>
            <x-slot name="description">Current active toast status</x-slot>

            <livewire:control.toast-preview />
        </flux:card>
    </div>
@endsection
