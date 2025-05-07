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

        <div class="w-full text-center py-32">
            <h1 class="text-2xl md:text-3xl font-bold text-center lg:text-5xl text-gray-700 dark:text-gray-200">
                Welcome to <span class="text-yellow-500">&lt;LanPort&gt;</span> <span class="text-gray-900 dark:text-white"> News</span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-lg mt-1">ESPORT UKM NEWS</p>
            <a class="px-3 py-2 text-lg text-white bg-gray-800 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 rounded mt-5 inline-block transition duration-150"
                href="{{ url('/blog') }}">Start Reading</a>
        </div>

        <div class="mb-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16">
                <h2 class="mt-16 mb-5 text-3xl text-yellow-500 font-bold">Featured Posts</h2>
                <div class="w-full">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 w-full">
                        @foreach ($featuredPosts as $post)
                            <div class="md:col-span-1">
                                <x-posts.post-card :post="$post" />
                            </div>
                        @endforeach
                    </div>
                </div>
                <a class="mt-10 block text-center text-lg text-yellow-500 font-semibold hover:text-yellow-600 transition duration-150"
                    href="{{ url('/blog') }}">More Posts</a>
            </div>
            <hr class="border-gray-200 dark:border-gray-700">

            <h2 class="mt-16 mb-5 text-3xl text-yellow-500 font-bold">Latest Posts</h2>
            <div class="w-full mb-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 w-full">
                    @foreach ($latestPosts as $post)
                        <div class="md:col-span-1">
                            <x-posts.post-card :post="$post" />
                        </div>
                    @endforeach
                </div>
            </div>
            <a class="mt-10 block text-center text-lg text-yellow-500 font-semibold hover:text-yellow-600 transition duration-150"
                href="{{ url('/blog') }}">More Posts</a>
        </div>

        @include('layouts.partials.footer')
    </div>
</body>

</html>
