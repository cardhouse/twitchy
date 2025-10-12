<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Overlay</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Ensure transparent background for OBS */
        body {
            background: transparent !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
        }

        html {
            background: transparent !important;
        }

        /* Remove any default margins/padding that could interfere with OBS */
        * {
            box-sizing: border-box;
        }

        /* Enhanced animations for toast */
        .toast-enter {
            animation: toastEnter 0.3s ease-out forwards;
        }

        .toast-exit {
            animation: toastExit 0.2s ease-in forwards;
        }

        @keyframes toastEnter {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes toastExit {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(-10px) scale(0.98);
            }
        }

        /* Smooth text rendering for better readability in OBS */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    @fluxAppearance
</head>
<body class="antialiased">
    @yield('content')

    @livewireScripts
    @fluxScripts
</body>
</html>
