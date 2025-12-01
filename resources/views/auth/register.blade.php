<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-purple-50 via-pink-50 to-red-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Event Organizer</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js', 'resources/js/auth.js', 'resources/js/auth-transitions.js'])
</head>
<body class="h-full">
    <div class="min-h-full flex">
        <!-- Left side - Decorative -->
        <div class="hidden lg:block relative flex-1 bg-gradient-to-br from-purple-600 via-pink-600 to-red-600 animate-fadeInLeft">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative h-full flex flex-col justify-center items-center px-12 text-center">
                <div class="max-w-md animate-fadeInUp">
                    <!-- Decorative Icon -->
                    <div class="mx-auto h-24 w-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-8 backdrop-blur-sm floating-elements">
                        <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Bergabung Bersama Kami
                    </h1>
                    <p class="text-xl text-purple-100 mb-8">
                        Daftarkan diri Anda dan nikmati kemudahan mengelola event
                    </p>
                    
                    <!-- Features -->
                    <div class="space-y-4 text-left">
                        <div class="flex items-center text-purple-100">
                            <svg class="h-5 w-5 mr-3 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Akses cepat ke semua fitur
                        </div>
                        <div class="flex items-center text-purple-100">
                            <svg class="h-5 w-5 mr-3 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Keamanan data terjamin
                        </div>
                        <div class="flex items-center text-purple-100">
                            <svg class="h-5 w-5 mr-3 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Kolaborasi tim yang efektif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-white bg-opacity-10 rounded-full translate-x-16 -translate-y-16 floating-elements parallax-element"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white bg-opacity-5 rounded-full -translate-x-20 translate-y-20 floating-elements parallax-element"></div>
        </div>

        <!-- Right side - Register Form -->
        <div class="flex-1 flex flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 animate-fadeInRight">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div class="text-center animate-fadeInUp">
                    <!-- Logo/Icon -->
                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg floating-elements">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                        Buat Akun Baru
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Isi formulir di bawah untuk mendaftar
                    </p>
                </div>

                <div class="mt-8">
                    <form method="POST" action="{{ route('register') }}" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Lengkap
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <input id="name" name="name" type="text" autocomplete="name" required 
                                       value="{{ old('name') }}"
                                       class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
                                       placeholder="Masukkan nama lengkap Anda">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

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
                                       class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
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
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="new-password" required 
                                       class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
                                       placeholder="Buat password yang kuat">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Konfirmasi Password
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                       class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm transition-all duration-200 hover:border-gray-400"
                                       placeholder="Ulangi password Anda">
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-[1.02] shadow-lg">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-purple-300 group-hover:text-purple-200 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                </span>
                                Daftar Sekarang
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                Sudah punya akun? 
                                <a href="{{ route('login') }}" 
                                   class="font-medium text-purple-600 hover:text-purple-500 transition-colors duration-200">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>