@props(['matches', 'type'])

<div class="tournament-bracket overflow-x-auto">
    @if($matches && $matches->count() > 0)
        @php
            // Define the order of rounds
            $roundOrder = [
                'upper_round_1' => 1,
                'lower_round_1' => 2,
                'upper_round_2' => 3,
                'lower_round_2' => 4,
                'upper_quarter_finals' => 5,
                'lower_quarter_finals' => 6,
                'semi_finals' => 7,
                'finals' => 8,
                'grand_finals' => 9
            ];

            // Group matches by round and sort them according to the defined order
            $rounds = $matches->groupBy('round')->sortBy(function($items, $key) use ($roundOrder) {
                return $roundOrder[$key] ?? 999;
            });

            // Get the grand finals match and winner if exists
            $grandFinalsMatch = $matches->where('round', 'grand_finals')->first();
            $finalsMatch = $matches->where('round', 'finals')->first();
            $semiFinalsMatch = $matches->where('round', 'semi_finals')->first();
            $champion = null;
            if ($grandFinalsMatch && $grandFinalsMatch->winner_id) {
                $champion = $grandFinalsMatch->winner;
            } elseif ($finalsMatch && $finalsMatch->winner_id) {
                // Only show finals winner as champion if grand finals was not played or not needed
                $champion = $finalsMatch->winner;
            }
        @endphp

        @if($champion)
            <div class="mb-8">
                <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 flex items-center justify-between">
                        <span>Tournament Champion</span>
                        <span class="text-2xl">🏆</span>
                    </h3>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300 flex items-center justify-center">
                            {{ $champion->team_name }}
                            <span class="ml-2">👑</span>
                        </div>
                        @if(($grandFinalsMatch && $grandFinalsMatch->winner_id) && $grandFinalsMatch->team1_score !== null && $grandFinalsMatch->team2_score !== null)
                        <div class="mt-2 text-center text-yellow-600 dark:text-yellow-400">
                            Final Score: {{ $grandFinalsMatch->team1_score }} - {{ $grandFinalsMatch->team2_score }}
                        </div>
                        @elseif($finalsMatch && $finalsMatch->winner_id && $finalsMatch->team1_score !== null && $finalsMatch->team2_score !== null)
                        <div class="mt-2 text-center text-yellow-600 dark:text-yellow-400">
                            Final Score: {{ $finalsMatch->team1_score }} - {{ $finalsMatch->team2_score }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Upper Bracket Section -->
        <div class="mb-12">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Upper Bracket</h3>
            <div class="flex space-x-8 pb-8">
                @foreach($rounds as $roundName => $roundMatches)
                    @if(str_starts_with($roundName, 'upper_') || $roundName === 'semi_finals' || $roundName === 'finals')
                        <div class="round min-w-[300px] flex flex-col">
                            <h4 class="text-lg font-medium mb-4 text-center text-gray-900 dark:text-white">
                                @php
                                    $roundNumber = $roundOrder[$roundName] ?? 0;
                                    $isRoundComplete = $roundMatches->every(function($match) {
                                        return $match->winner_id !== null;
                                    });
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full mr-2
                                    @if($isRoundComplete) bg-green-500 text-white @else bg-yellow-500 text-white @endif">
                                    {{ $roundNumber }}
                                </span>
                                @switch($roundName)
                                    @case('upper_round_1')
                                        Round 1
                                        @break
                                    @case('upper_round_2')
                                        Round 2
                                        @break
                                    @case('upper_quarter_finals')
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
                                                    @if($match->team1)
                                                        <div class="flex items-center {{ $match->winner_id === $match->team1_id && $match->round === 'grand_finals' ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                            {{ $match->team1->team_name }}
                                                            @if($match->winner_id === $match->team1_id && $match->round === 'grand_finals')
                                                                <span class="ml-2">👑</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        TBD
                                                    @endif
                                                </span>
                                                @if($match->team1_score !== null)
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
                                                    @if($match->team2)
                                                        <div class="flex items-center {{ $match->winner_id === $match->team2_id && $match->round === 'grand_finals' ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                            {{ $match->team2->team_name }}
                                                            @if($match->winner_id === $match->team2_id && $match->round === 'grand_finals')
                                                                <span class="ml-2">👑</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        TBD
                                                    @endif
                                                </span>
                                                @if($match->team2_score !== null)
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
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Lower Bracket Section -->
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Lower Bracket</h3>
            <div class="flex space-x-8 pb-8">
                @foreach($rounds as $roundName => $roundMatches)
                    @if(str_starts_with($roundName, 'lower_'))
                        <div class="round min-w-[300px] flex flex-col">
                            <h4 class="text-lg font-medium mb-4 text-center text-gray-900 dark:text-white">
                                @php
                                    $roundNumber = $roundOrder[$roundName] ?? 0;
                                    $isRoundComplete = $roundMatches->every(function($match) {
                                        return $match->winner_id !== null;
                                    });
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full mr-2
                                    @if($isRoundComplete) bg-green-500 text-white @else bg-yellow-500 text-white @endif">
                                    {{ $roundNumber }}
                                </span>
                                @switch($roundName)
                                    @case('lower_round_1')
                                        Round 1
                                        @break
                                    @case('lower_round_2')
                                        Round 2
                                        @break
                                    @case('lower_quarter_finals')
                                        Quarter Finals
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
                                                    @if($match->team1)
                                                        {{ $match->team1->team_name }}
                                                    @else
                                                        TBD
                                                    @endif
                                                </span>
                                                @if($match->team1_score !== null)
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
                                                    @if($match->team2)
                                                        {{ $match->team2->team_name }}
                                                    @else
                                                        TBD
                                                    @endif
                                                </span>
                                                @if($match->team2_score !== null)
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
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Grand Finals Section -->
        @if($grandFinalsMatch)
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Grand Finals</h3>
                <div class="flex justify-center">
                    <div class="round min-w-[300px] flex flex-col">
                        <h4 class="text-lg font-medium mb-4 text-center text-gray-900 dark:text-white">
                            @php
                                $roundNumber = $roundOrder['grand_finals'] ?? 0;
                                $isRoundComplete = $grandFinalsMatch->winner_id !== null;
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full mr-2
                                @if($isRoundComplete) bg-green-500 text-white @else bg-yellow-500 text-white @endif">
                                {{ $roundNumber }}
                            </span>
                            Grand Finals
                        </h4>

                        <div class="match-card bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow relative">
                            <!-- Team 1 -->
                            <div class="team p-2 rounded {{ $grandFinalsMatch->winner_id === $grandFinalsMatch->team1_id ? 'bg-green-100 dark:bg-green-800' : '' }}">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        @if($grandFinalsMatch->team1)
                                            <div class="flex items-center {{ $grandFinalsMatch->winner_id === $grandFinalsMatch->team1_id ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                {{ $grandFinalsMatch->team1->team_name }}
                                                @if($grandFinalsMatch->winner_id === $grandFinalsMatch->team1_id)
                                                    <span class="ml-2">👑</span>
                                                @endif
                                            </div>
                                        @else
                                            TBD
                                        @endif
                                    </span>
                                    @if($grandFinalsMatch->team1_score !== null)
                                        <span class="text-gray-700 dark:text-gray-300">
                                            {{ $grandFinalsMatch->team1_score }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Versus Divider -->
                            <div class="text-center py-1 text-sm text-gray-500 dark:text-gray-400">VS</div>

                            <!-- Team 2 -->
                            <div class="team p-2 rounded {{ $grandFinalsMatch->winner_id === $grandFinalsMatch->team2_id ? 'bg-green-100 dark:bg-green-800' : '' }}">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        @if($grandFinalsMatch->team2)
                                            <div class="flex items-center {{ $grandFinalsMatch->winner_id === $grandFinalsMatch->team2_id ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
                                                {{ $grandFinalsMatch->team2->team_name }}
                                                @if($grandFinalsMatch->winner_id === $grandFinalsMatch->team2_id)
                                                    <span class="ml-2">👑</span>
                                                @endif
                                            </div>
                                        @else
                                            TBD
                                        @endif
                                    </span>
                                    @if($grandFinalsMatch->team2_score !== null)
                                        <span class="text-gray-700 dark:text-gray-300">
                                            {{ $grandFinalsMatch->team2_score }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Match Info -->
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                @if($grandFinalsMatch->scheduled_at)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $grandFinalsMatch->scheduled_at->format('M d, Y H:i') }}
                                    </div>
                                @endif
                            </div>

                            @if($grandFinalsMatch->winner_id)
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
                    </div>
                </div>
            </div>
        @endif
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
