<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Real-time Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/echo.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <h1 class="text-xl font-bold text-gray-900">{{ config('app.name') }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ Auth::user()->name ?? 'Guest' }}</span>
                        <a href="{{ route('filament.admin.pages.dashboard') }}" 
                           class="text-gray-500 hover:text-gray-700">
                            Admin Panel
                        </a>
                        @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                Logout
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @if(isset($component))
                <livewire:{{ $component }} />
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>

    @livewireScripts
    
    <!-- Include the real-time JS assets -->
    <script>
        // Initialize Echo for real-time functionality
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
        };
    </script>
    
    <!-- Echo and real-time JS from Vite build -->
    @vite(['resources/js/echo.js'])
</body>
</html>
