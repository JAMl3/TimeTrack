<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TimeTrack') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-900">
        <!-- Clock Icon -->
        <div class="mb-4">
            <svg class="w-12 h-12 text-indigo-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <!-- Title -->
        <div class="mb-2 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">TimeTrack</h1>
        </div>
        <div class="mb-8">
            <p class="text-sm text-gray-600 dark:text-gray-400">Sign in to manage your time effectively</p>
        </div>

        <!-- Login Box -->
        <div class="w-[400px] px-8 py-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input id="email" name="email" type="email" required placeholder="Enter your email"
                        class="mt-1 block w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        value="{{ old('email') }}" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input id="password" name="password" type="password" required placeholder="Enter your password"
                        class="mt-1 block w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded text-indigo-500 focus:ring-indigo-500">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Remember me</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-indigo-500 hover:text-indigo-400">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 mt-2 bg-indigo-600 hover:bg-indigo-700 rounded-md text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign In
                </button>
            </form>
        </div>

                <!-- Demo Accounts Box -->
        <div class="mt-4 w-[400px] bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700">
                <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300">Demo Accounts</h2>
            </div>
            <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-gray-700">
                <div class="p-3">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">HR Account</p>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-800 dark:text-gray-300">admin@admin.com</p>
                        <p class="text-sm text-gray-800 dark:text-gray-300">password</p>
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Test Account</p>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-800 dark:text-gray-300">test@test.com</p>
                        <p class="text-sm text-gray-800 dark:text-gray-300">password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check for saved theme preference or system preference
        if (localStorage.getItem('theme') === null) {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                localStorage.setItem('theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        } else if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
