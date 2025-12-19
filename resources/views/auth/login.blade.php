<?php $title = 'Login - ' . config('app.name', 'kopkar.tirtaraharja'); ?>

<x-guest-layout>
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-blue-400 to-green-500">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-100 opacity-20 rounded-full animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-green-100 opacity-15 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-blue-100 opacity-25 rounded-full animate-ping" style="animation-delay: 2s;"></div>
        </div>

        <div class="relative flex flex-col sm:justify-center items-center pt-6 sm:pt-0 min-h-screen">
            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/20 backdrop-blur-lg shadow-2xl overflow-hidden sm:rounded-2xl border border-white/30 transform transition-all duration-500 hover:scale-105 hover:shadow-3xl">
                
                <div class="text-center mb-8 animate-fade-in">
                    <div class="flex justify-center mb-4 transform transition-transform duration-300 hover:scale-110">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-green-500 rounded-full blur-lg opacity-30 animate-pulse"></div>
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Koperasi Tirta Raharja" class="relative h-24 w-auto drop-shadow-lg">
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-800 to-green-600 bg-clip-text text-transparent mb-2">Tirta Raharja</h1>
                    <p class="text-sm text-white/90 font-medium">Sistem Informasi Koperasi</p>
                    <div class="mt-3 h-1 w-20 bg-gradient-to-r from-blue-500 to-green-500 mx-auto rounded-full"></div>
                </div>

                <x-auth-session-status class="mb-6 p-4 bg-white/10 backdrop-blur-sm rounded-lg border border-white/20 text-white" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div class="group">
                        <x-input-label for="username" :value="__('Username')" class="text-white/90 font-medium mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/60 group-focus-within:text-blue-300 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <x-text-input id="username" class="block w-full pl-10 pr-3 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-300" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" placeholder="Masukkan username Anda" />
                        </div>
                        <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-200" />
                    </div>

                    <div class="group">
                        <x-input-label for="password" :value="__('Password')" class="text-white/90 font-medium mb-2 block" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/60 group-focus-within:text-blue-300 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <x-text-input id="password" class="block w-full pl-10 pr-3 py-3 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-300" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password Anda" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-white/30 bg-white/10 text-green-600 shadow-sm focus:ring-green-500 focus:ring-offset-0 transition-all duration-200" name="remember">
                            <span class="ms-3 text-sm text-white/90 group-hover:text-white transition-colors duration-200">{{ __('Remember me') }}</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a class="text-sm text-white/80 hover:text-white underline hover:no-underline transition-all duration-200 font-medium" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="pt-4">
                        <x-primary-button class="w-full bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Log in') }}
                            </span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>