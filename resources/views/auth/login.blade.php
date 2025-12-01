<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Event Organizer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js', 'resources/js/auth.js', 'resources/js/auth-transitions.js'])
</head>
<body class="h-full">
    <div class="min-h-full flex">
        <!-- Left side - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 animate-fadeInLeft">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div class="text-center animate-fadeInUp">
                    <!-- Logo/Icon -->
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg floating-elements">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                        Selamat Datang Kembali
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Masuk ke sistem event organizer
                    </p>
                </div>

                <div class="mt-8">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required 
                                       value="{{ old('email') }}"
                                       class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
                                       placeholder="Masukkan email Anda">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <div class="mt-1 relative">
                                <input id="password" name="password" type="password" autocomplete="current-password" required 
                                       class="pl-4 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
                                       placeholder="Masukkan password Anda">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="eyeIcon" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-colors duration-200">
                                <label for="remember" class="ml-2 block text-sm text-gray-700">
                                    Ingat saya
                                </label>
                            </div>
                            
                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" 
                                       class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                                        Lupa password?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                </span>
                                Masuk
                            </button>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" 
                                   class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors duration-200">
                                    Daftar di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right side - Decorative -->
        <div class="hidden lg:block relative flex-1 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 animate-fadeInRight">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative h-full flex flex-col justify-center items-center px-12 text-center">
                <div class="max-w-md animate-fadeInUp">
                    <!-- Decorative Icon -->
                    <div class="mx-auto h-24 w-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-8 backdrop-blur-sm floating-elements">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Sistem Event Organizer
                    </h1>
                    <p class="text-xl text-blue-100 mb-8">
                        Kelola acara dan event dengan mudah dan profesional
                    </p>
                    
                    <!-- Features -->
                    <div class="space-y-4 text-left">
                        <div class="flex items-center text-blue-100">
                            <svg class="h-5 w-5 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Manajemen event terintegrasi
                        </div>
                        <div class="flex items-center text-blue-100">
                            <svg class="h-5 w-5 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Sistem pembayaran otomatis
                        </div>
                        <div class="flex items-center text-blue-100">
                            <svg class="h-5 w-5 mr-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Monitoring event real-time
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-32 h-32 bg-white bg-opacity-10 rounded-full -translate-x-16 -translate-y-16 floating-elements parallax-element"></div>
            <div class="absolute bottom-0 right-0 w-40 h-40 bg-white bg-opacity-5 rounded-full translate-x-20 translate-y-20 floating-elements parallax-element"></div>
        </div>
    </div>
</body>
</html>