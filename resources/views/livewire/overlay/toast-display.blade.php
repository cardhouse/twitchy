<div data-overlay-key="{{ $overlayKey }}">
    <!-- Toast Container -->
    <div>
        @if($currentMessage)
            <flux:context>
                <flux:card size="xl" class="hover:bg-zinc-50 dark:hover:bg-zinc-700">
                    <flux:heading class="flex items-center gap-2">{{ $currentMessage->display_name }} <flux:icon name="arrow-up-right" class="ml-auto text-zinc-400" variant="micro" /></flux:heading>
                    <flux:text class="mt-2 font-mono">
                        <span id="typed-message" data-message="{{ $currentMessage->message }}"></span>
                    </flux:text>
                </flux:card>

                <flux:menu>
                    <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                    <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                    <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
                </flux:menu>
            </flux:context>
        @endif
    </div>

    @script
    <script>
        const typedMessageEl = document.getElementById('typed-message');
        const minTypingSpeed = 50; // Minimum typing speed in milliseconds
        const maxTypingSpeed = 200; // Maximum typing speed in milliseconds
        const cursorBlinkDelay = 400; // Cursor blink delay in milliseconds

        function getRandomTypingSpeed(min, max) {
            return Math.random() * (max - min) + min;
        }

        function typeText(text, index = 0) {
            if (!typedMessageEl) return;

            if (index < text.length) {
                typedMessageEl.textContent = text.slice(0, index + 1);
                const typingSpeed = getRandomTypingSpeed(minTypingSpeed, maxTypingSpeed);
                setTimeout(() => typeText(text, index + 1), typingSpeed);
            } else {
                // Animation complete - add blinking cursor class
                setTimeout(() => {
                    typedMessageEl.classList.add('typing-cursor');
                }, cursorBlinkDelay);
            }
        }

        function startTyping() {
            if (!typedMessageEl) return;

            const message = typedMessageEl.dataset.message;
            if (!message) return;

            // Clear current content and remove cursor
            typedMessageEl.textContent = '';
            typedMessageEl.classList.remove('typing-cursor');

            // Start typing animation
            const initialTypingSpeed = getRandomTypingSpeed(minTypingSpeed, maxTypingSpeed);
            setTimeout(() => typeText(message, 0), initialTypingSpeed);
        }

        // Listen for message updates from Livewire
        $wire.on('message-updated', () => {
            // Small delay to ensure DOM is updated
            setTimeout(startTyping, 50);
        });

        // Start typing animation on initial load
        document.addEventListener('DOMContentLoaded', () => {
            startTyping();
        });
    </script>
    @endscript

    <style>
        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        .typing-cursor::after {
            content: "_";
            animation: blink 0.6s step-start infinite;
        }
    </style>
</div>
