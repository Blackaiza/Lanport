<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="max-w-sm">
                <label class="block text-sm mb-2 dark:text-white">{{ __('Password') }}</label>
                <div class="relative">
                    <input id="hs-toggle-password" type="password" name="password" required autocomplete="current-password"
                        class="py-2.5 sm:py-3 ps-4 pe-10 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                        placeholder="Enter password">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 rounded-e-md focus:outline-hidden focus:text-blue-600 dark:text-neutral-600 dark:focus:text-blue-500">
                        <svg id="eyeIcon" class="shrink-0 size-3.5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path id="eyeOpen" d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                            <path id="eyeOpen" d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                            <path id="eyeOpen" d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                            <line id="eyeOpen" x1="2" x2="22" y1="2" y2="22"></line>
                            <path id="eyeClosed" class="hidden" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle id="eyeClosed" class="hidden" cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <script>
                function togglePassword() {
                    const passwordField = document.getElementById("hs-toggle-password");
                    const eyeOpen = document.querySelectorAll("#eyeOpen");
                    const eyeClosed = document.getElementById("eyeClosed");

                    if (passwordField.type === "password") {
                        passwordField.type = "text";
                        eyeOpen.forEach(e => e.classList.add("hidden"));
                        eyeClosed.classList.remove("hidden");
                    } else {
                        passwordField.type = "password";
                        eyeOpen.forEach(e => e.classList.remove("hidden"));
                        eyeClosed.classList.add("hidden");
                    }
                }
            </script>



            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
