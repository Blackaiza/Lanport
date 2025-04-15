<div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        Preview of First Round Matchups
    </h3>

    <div class="space-y-4">
        @foreach($matchups as $index => $matchup)
            <div class="border rounded-lg p-4 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            Match {{ $index + 1 }}
                        </div>
                        <div class="mt-2 space-y-2">
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span class="font-medium">{{ $matchup['team1'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">VS</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                <span class="font-medium">{{ $matchup['team2'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($type === 'single_elimination')
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded">
            <h4 class="font-medium text-blue-700 dark:text-blue-300">Single Elimination Format</h4>
            <p class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                Winners advance to next round. Losers are eliminated.
            </p>
        </div>
    @elseif($type === 'double_elimination')
        <div class="mt-4 p-4 bg-purple-50 dark:bg-purple-900 rounded">
            <h4 class="font-medium text-purple-700 dark:text-purple-300">Double Elimination Format</h4>
            <p class="mt-1 text-sm text-purple-600 dark:text-purple-400">
                Teams must lose twice to be eliminated. Includes winners and losers brackets.
            </p>
        </div>
    @else
        <div class="mt-4 p-4 bg-green-50 dark:bg-green-900 rounded">
            <h4 class="font-medium text-green-700 dark:text-green-300">Round Robin Format</h4>
            <p class="mt-1 text-sm text-green-600 dark:text-green-400">
                Each team plays against every other team once.
            </p>
        </div>
    @endif
</div>
