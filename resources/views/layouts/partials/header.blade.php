<div class="top-menu ml-10">
    <div class="flex space-x-4">
        {{-- <li>
            <a class="flex space-x-2 items-center hover:text-yellow-900 text-sm text-yellow-500"
                href="http://127.0.0.1:8000">
                Home
            </a>
        </li> --}}

        <x-nav-link href="{{ route('home') }}" :active="request()->routeIs('dashboards')">
            {{ __('Home') }}
        </x-nav-link>

        <x-nav-link href="{{ route('posts.index') }}" :active="request()->routeIs('posts.index')">
            {{ __('Blog') }}
        </x-nav-link>

    </div>
</div>
