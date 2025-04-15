@props(['matches', 'type'])

<div class="tournament-bracket overflow-x-auto">
    @if($matches && $matches->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Team 1</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Team 2</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($matches->sortBy('scheduled_at') as $match)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ optional($match->team1)->team_name ?? 'TBD' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if(isset($match->team1_score) && isset($match->team2_score))
                                        {{ $match->team1_score }} - {{ $match->team2_score }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ optional($match->team2)->team_name ?? 'TBD' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if($match->scheduled_at)
                                        {{ $match->scheduled_at->format('M d, Y H:i') }}
                                    @else
                                        TBD
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($match->winner_id)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        Completed
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-500 dark:text-gray-400">
                @if($type)
                    <p class="text-lg mb-2">Tournament bracket will be generated soon.</p>
                    <p class="text-sm">Format: {{ ucwords(str_replace('_', ' ', $type)) }}</p>
                @else
                    <p>Tournament bracket has not been generated yet.</p>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .tournament-bracket {
        overflow-x: auto;
        padding-bottom: 2rem;
    }

    /* Custom scrollbar for webkit browsers */
    .tournament-bracket::-webkit-scrollbar {
        height: 8px;
    }

    .tournament-bracket::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .tournament-bracket::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .tournament-bracket::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
