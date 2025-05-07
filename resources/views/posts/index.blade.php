<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <header class="flex items-center justify-between py-3 px-6 border-b border-gray-100 dark:border-gray-700">
            <div id="header-left" class="flex items-center">
                <div class="text-gray-800 dark:text-gray-200 font-semibold">
                    <span class="text-yellow-500 text-xl">&lt;LanPort&gt;</span> UKM
                </div>
                @include('layouts.partials.header')
            </div>
            <div id="header-right" class="flex items-center md:space-x-6">
                <div class="flex space-x-5">
                    <a class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500 dark:text-gray-400"
                        href="{{ url('/login') }}">
                        Login
                    </a>
                    <a class="flex space-x-2 items-center hover:text-yellow-500 text-sm text-gray-500 dark:text-gray-400"
                        href="{{ url('/register') }}">
                        Register
                    </a>
                </div>
            </div>
        </header>

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="w-full grid grid-cols-4 gap-10">
                            <div class="md:col-span-3 col-span-4">
                                <livewire:post-list />
                            </div>
                            <div id="side-bar"
                                class="border-t border-t-gray-100 dark:border-t-gray-700 md:border-t-none col-span-4 md:col-span-1 px-3 md:px-6 space-y-10 py-6 pt-10 md:border-l border-gray-100 dark:border-gray-700 h-screen sticky top-0">
                                @include('posts.partials.search-box')

                                <div id="recommended-topics-box">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Recommended Topics</h3>
                                    <div class="topics flex flex-wrap justify-start gap-2">
                                        <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl px-3 py-1 text-base transition duration-150">
                                            Tailwind
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include('layouts.partials.footer')
    </div>
</body>

</html>
