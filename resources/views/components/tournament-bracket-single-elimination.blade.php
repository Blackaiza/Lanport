@props(['matches', 'type'])

<div class="tournament-bracket overflow-x-auto">
    @if($matches && $matches->count() > 0)
        @php
            $rounds = $matches->groupBy('round')->sortBy(function($items, $key) {
                // Sort rounds in proper order
                $roundOrder = [
                    'round_1' => 1,
                    'round_2' => 2,
                    'round_of_16' => 3,
                    'quarter_finals' => 4,
                    'semi_finals' => 5,
                    'finals' => 6
                ];
                return $roundOrder[$key] ?? 999;
            });

            // Get the final match and winner
            $finalMatch = $matches->where('round', 'finals')->first();
            $winner = $finalMatch && $finalMatch->winner_id ? $finalMatch->winner : null;
        @endphp

        @if($winner)
            <div class="mb-8">
                <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 flex items-center justify-between">
                        <span>Tournament Champion</span>
                        <span class="text-2xl">🏆</span>
                    </h3>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 flex items-center justify-center">
                            {{ $winner->team_name }}
                            <span class="ml-2">👑</span>
                        </div>
                        @if($finalMatch->team1_score !== null && $finalMatch->team2_score !== null)
                        <div class="mt-2 text-center text-yellow-600 dark:text-yellow-400">
                            Final Score: {{ $finalMatch->team1_score }} - {{ $finalMatch->team2_score }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="flex space-x-8 pb-8">
            @foreach($rounds as $roundName => $roundMatches)
                <div class="round min-w-[300px] flex flex-col">
                    <h4 class="text-lg font-medium mb-4 text-center text-gray-900 dark:text-white">
                        @switch($roundName)
                            @case('round_1')
                                Round 1
                                @break
                            @case('round_2')
                                Round 2
                                @break
                            @case('round_of_16')
                                Round of 16
                                @break
                            @case('quarter_finals')
                                Quarter Finals
                                @break
                            @case('semi_finals')
                                Semi Finals
                                @break
                            @case('finals')
                                Finals
                                @break
                            @default
                                {{ ucwords(str_replace('_', ' ', $roundName)) }}
                        @endswitch
                    </h4>

                    <div class="space-y-4 flex-1 flex flex-col justify-center">
                        @foreach($roundMatches->sortBy('match_number') as $match)
                            <div class="match-card bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow relative">
                                <!-- Team 1 -->
                                <div class="team p-2 rounded {{ $match->winner_id === $match->team1_id ? 'bg-green-100 dark:bg-green-800' : '' }}">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            @if($match->team1_id)
                                                <div class="flex items-center {{ $match->winner_id === $match->team1_id && $match->round === 'finals' ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                    {{ optional($match->team1)->team_name }}
                                                    @if($match->winner_id === $match->team1_id && $match->round === 'finals')
                                                        <span class="ml-2">👑</span>
                                                    @endif
                                                </div>
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                        @if(isset($match->team1_score))
                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ $match->team1_score }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Versus Divider -->
                                <div class="text-center py-1 text-sm text-gray-500 dark:text-gray-400">VS</div>

                                <!-- Team 2 -->
                                <div class="team p-2 rounded {{ $match->winner_id === $match->team2_id ? 'bg-green-100 dark:bg-green-800' : '' }}">
                                    <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            @if($match->team2_id)
                                                <div class="flex items-center {{ $match->winner_id === $match->team2_id && $match->round === 'finals' ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                    {{ optional($match->team2)->team_name }}
                                                    @if($match->winner_id === $match->team2_id && $match->round === 'finals')
                                                        <span class="ml-2">👑</span>
                                                    @endif
                                                </div>
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                        @if(isset($match->team2_score))
                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ $match->team2_score }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Match Info -->
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    @if($match->scheduled_at)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $match->scheduled_at->format('M d, Y H:i') }}
                                        </div>
                                    @endif
                                </div>

                                @if($match->winner_id)
                                    <div class="absolute -right-2 -top-2">
                                        <div class="bg-green-500 text-white rounded-full p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
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

    .match-card {
        transition: all 0.3s ease;
    }

    .match-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .team {
        transition: background-color 0.3s ease;
    }

    .team:hover {
        background-color: rgba(59, 130, 246, 0.1);
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