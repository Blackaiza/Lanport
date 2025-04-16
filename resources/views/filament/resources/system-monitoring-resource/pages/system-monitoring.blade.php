<x-filament::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                System Monitoring Dashboard
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Quick Access</h3>
                    <div class="mt-2">
                        <a href="{{ url('/pulse') }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Open Laravel Pulse
                        </a>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">System Information</h3>
                    <div class="mt-2 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">PHP Version</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ phpversion() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Laravel Version</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ app()->version() }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Server Status</h3>
                    <div class="mt-2">
                        <div class="flex items-center">
                            <div class="h-2 w-2 rounded-full bg-green-500 mr-2"></div>
                            <span class="text-sm text-gray-900 dark:text-white">System is running</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Monitoring Tools
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Pulse</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Real-time application performance monitoring and debugging tool.
                    </p>
                    <div class="mt-4">
                        <a href="{{ url('/pulse') }}" target="_blank" class="text-sm text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                            Open Pulse Dashboard →
                        </a>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">System Resources</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Monitor server resources and performance metrics.
                    </p>
                    <div class="mt-4">
                        <a href="{{ url('/pulse/servers') }}" target="_blank" class="text-sm text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                            View Server Stats →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
