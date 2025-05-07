<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $post->title }} - {{ config('app.name', 'Laravel') }}</title>

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
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <article class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Post Header -->
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <img class="w-10 h-10 rounded-full mr-3" src="{{ $post->author->profile_photo_url }}" alt="{{ $post->author->name }}">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $post->author->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $post->published_at->format('M d, Y') }}</div>
                            </div>
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $post->title }}</h1>

                        <!-- Categories -->
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($post->categories as $category)
                                <span class="rounded-xl px-3 py-1 text-sm transition duration-150"
                                    style="background-color: {{ $category->bg_color ?? '#EF4444' }}; color: {{ $category->text_color ?? '#FFFFFF' }}">
                                    {{ $category->title }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Featured Image -->
                        @if($post->image)
                            <div class="mb-8">
                                <img src="{{ asset('storage/' . $post->image) }}"
                                     alt="{{ $post->title }}"
                                     class="w-full h-auto rounded-lg">
                            </div>
                        @endif

                        <!-- Post Content -->
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $post->body !!}
                        </div>

                        <!-- Reading Time -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $post->getReadingTime() }} min read
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </main>

        @include('layouts.partials.footer')
    </div>
</body>
</html>