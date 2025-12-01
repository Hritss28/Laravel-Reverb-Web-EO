<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }} - Admin Panel</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Filament Styles -->
    @filamentStyles
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/echo.js', 'resources/js/filament-realtime.js'])
    
    <!-- Pass user data to JavaScript -->
    <script>
        window.filamentData = {
            user: @json(auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'role' => auth()->user()->role,
                'email' => auth()->user()->email
            ] : null),
            csrfToken: '{{ csrf_token() }}',
            locale: '{{ app()->getLocale() }}',
            reverb: {
                key: '{{ config('broadcasting.connections.reverb.key') }}',
                host: '{{ config('broadcasting.connections.reverb.options.host') }}',
                port: '{{ config('broadcasting.connections.reverb.options.port') }}',
                scheme: '{{ config('broadcasting.connections.reverb.options.scheme') }}'
            }
        };
    </script>
</head>
<body class="antialiased">
    {{ $slot }}
    
    <!-- Filament Scripts -->
    @filamentScripts
    
    <!-- Custom Real-time Enhancements -->
    @stack('scripts')
    
    <!-- Real-time Connection Status -->
    <div id="realtime-status-container"></div>
    
    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
</body>
</html>
