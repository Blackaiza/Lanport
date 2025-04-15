<x-app-layout>
    <x-slot name="header">
        {{-- Dalam navigate dalam --}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Join a Competition') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <x-competition :competitions="$competitions" />
            </div>
        </div>
    </div>
</x-app-layout>
