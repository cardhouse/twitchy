<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Control Panel - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance

    <style></style>
</head>
<body>
    <div class="flex min-h-screen flex-col">
        {{-- Header --}}
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 flex items-center">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:brand href="#" name="cardhouse_" class="max-lg:hidden">
                <div class="flex aspect-square items-center justify-center rounded-md bg-accent text-accent-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mic-vocal">
                        <path d="m11 7.601-5.994 8.19a1 1 0 0 0 .1 1.298l.817.818a1 1 0 0 0 1.314.087L15.09 12"/>
                        <path d="M16.5 21.174C15.5 20.5 14.372 20 13 20c-2.058 0-3.928 2.356-6 2-2.072-.356-2.775-3.369-1.5-4.5"/>
                        <circle cx="16" cy="7" r="5"/>
                    </svg>
                </div>
            </flux:brand>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item href="#">Chat</flux:navbar.item>
                <flux:navbar.item href="{{ route('overlay.show', 'local') }}" target="_blank">Overlay</flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <!-- Stream Toggle -->
            <livewire:control.stream-toggle />

            <flux:separator vertical variant="subtle" class="my-4 mx-3"/>

            <flux:dropdown position="top" align="end">
                <flux:profile class="cursor-pointer" avatar="https://fluxui.dev/img/demo/teej.png" />

                <flux:menu>

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog">Test message</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item variant="danger" icon="trash" class="w-full">Clear messages</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{-- Mobile sidebar --}}
        <flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:brand name="Podium" href="#" class="px-2">
                <div class="flex aspect-square items-center justify-center rounded-md bg-accent text-accent-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mic-vocal">
                        <path d="m11 7.601-5.994 8.19a1 1 0 0 0 .1 1.298l.817.818a1 1 0 0 0 1.314.087L15.09 12"/>
                        <path d="M16.5 21.174C15.5 20.5 14.372 20 13 20c-2.058 0-3.928 2.356-6 2-2.072-.356-2.775-3.369-1.5-4.5"/>
                        <circle cx="16" cy="7" r="5"/>
                    </svg>
                </div>
            </flux:brand>

            <flux:navlist variant="outline">
                <flux:navlist.group>
                    <flux:navlist.item href="#">Questions</flux:navlist.item>
                    <flux:navlist.item href="#">Leaderboard</flux:navlist.item>
                    <flux:navlist.item href="#">Announcements</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />
        </flux:sidebar>

        {{-- Main content --}}


        <!-- Main Content -->
        <main class="mx-auto flex w-full max-w-7xl flex-1 flex-col min-h-0 p-6">
            @yield('content')
        </main>

        <flux:toast />
    </div>
    @livewireScripts
    @fluxScripts
</body>
</html>
