<div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg shadow-md">
    <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 flex items-center justify-between">
        <span>Tournament Champion</span>
        <span class="text-2xl">🏆</span>
    </h3>
    <div class="mt-4">
        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 flex items-center justify-center">
            {{ $winnerName }}
            <span class="ml-2">👑</span>
        </div>
        @if(isset($finalScore))
        <div class="mt-2 text-center text-yellow-600 dark:text-yellow-400">
            Final Score: {{ $finalScore }}
        </div>
        @endif
    </div>
</div>
