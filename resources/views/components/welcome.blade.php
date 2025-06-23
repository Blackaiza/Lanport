@php
    use App\Models\Game;
    use App\Models\GameScore;
    use App\Models\Team;
    use App\Models\User;
    use App\Models\Competition;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;

    // Get all games
    $games = Game::all();
    $competitions = Competition::all();

    // Get selected filters from request
    $selectedGame = request('game_id');
    $selectedCompetition = request('competition_id');

    // Get user's personal stats for each game
    $userStats = [];
    if (Auth::check()) {
        foreach ($games as $game) {
            try {
                $query = GameScore::where('game_id', $game->game_id);

                if ($selectedCompetition) {
                    $query->where('competition_id', $selectedCompetition);
                }

                $userGameStats = $query
                    ->get()
                    ->flatMap(function ($score) {
                        return is_array($score->player_scores) ? $score->player_scores : [];
                    })
                    ->filter(function ($score) {
                        return $score['player_id'] == Auth::id();
                    });

                if ($userGameStats->isNotEmpty()) {
                    $totalKills = $userGameStats->sum('kills');
                    $totalDeaths = $userGameStats->sum('deaths');
                    $totalAssists = $userGameStats->sum('assists');
                    $kda = $totalDeaths > 0 ? ($totalKills + $totalAssists) / $totalDeaths : $totalKills + $totalAssists;
                    $kd = $totalDeaths > 0 ? $totalKills / $totalDeaths : $totalKills;

                    $userStats[$game->game_id] = [
                        'game' => $game,
                        'kills' => $totalKills,
                        'deaths' => $totalDeaths,
                        'assists' => $totalAssists,
                        'kda' => round($kda, 2),
                        'kd' => round($kd, 2)
                    ];
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    // Get team rankings for each game
    $teamRankings = [];
    foreach ($games as $game) {
        try {
            $query = GameScore::where('game_id', $game->game_id);

            // Apply competition filter if selected
            if ($selectedCompetition) {
                $query->where('competition_id', $selectedCompetition);
            }

            $teamRankings[$game->game_id] = $query
                ->select('team_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('team_id')
                ->orderBy('total_score', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($score) {
                    $team = Team::find($score->team_id);
                    return [
                        'team' => $team,
                        'score' => $score->total_score
                    ];
                })
                ->filter(function ($ranking) {
                    return $ranking['team'] !== null;
                });
        } catch (\Exception $e) {
            $teamRankings[$game->game_id] = collect([]);
        }
    }

    // Get individual player rankings for each game
    $playerRankings = [];
    foreach ($games as $game) {
        try {
            $query = GameScore::where('game_id', $game->game_id);

            // Apply competition filter if selected
            if ($selectedCompetition) {
                $query->where('competition_id', $selectedCompetition);
            }

            $playerRankings[$game->game_id] = $query
                ->get()
                ->flatMap(function ($score) {
                    return is_array($score->player_scores) ? $score->player_scores : [];
                })
                ->groupBy('player_id')
                ->map(function ($scores) {
                    $totalKills = $scores->sum('kills');
                    $totalDeaths = $scores->sum('deaths');
                    $totalAssists = $scores->sum('assists');
                    $kda = $totalDeaths > 0 ? ($totalKills + $totalAssists) / $totalDeaths : $totalKills + $totalAssists;
                    $kd = $totalDeaths > 0 ? $totalKills / $totalDeaths : $totalKills;

                    return [
                        'player' => User::find($scores->first()['player_id']),
                        'kills' => $totalKills,
                        'deaths' => $totalDeaths,
                        'assists' => $totalAssists,
                        'kda' => round($kda, 2),
                        'kd' => round($kd, 2)
                    ];
                })
                ->filter(function ($ranking) {
                    return $ranking['player'] !== null;
                })
                ->sortByDesc('kills')
                ->take(10)
                ->values();
        } catch (\Exception $e) {
            $playerRankings[$game->game_id] = collect([]);
        }
    }
@endphp


<div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome to <span class="text-yellow-500">&lt;LanPort&gt;</span> Rankings</h2>
        {{-- <a href="{{ route('team.create') }}"
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Create Team
        </a> --}}
    </div>

    {{-- <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        Welcome to <span class="text-yellow-500">&lt;LanPort&gt;</span> Rankings
    </h1> --}}

    @if(Auth::check() && !empty($userStats))
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Your Stats
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($userStats as $gameId => $stats)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                            {{ $stats['game']->name }}
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-emerald-400 font-medium">Kills:</span>
                                    <span class="ml-2">{{ $stats['kills'] }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-red-400 font-medium">Deaths:</span>
                                    <span class="ml-2">{{ $stats['deaths'] }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-blue-400 font-medium">Assists:</span>
                                    <span class="ml-2">{{ $stats['assists'] }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="font-bold">KDA:</span>
                                    <span class="ml-2 font-bold">{{ $stats['kda'] }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-bold">KD:</span>
                                    <span class="ml-2 font-bold">{{ $stats['kd'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="mt-6 mb-8 flex flex-wrap gap-4">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="game_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Game</label>
                <select name="game_id" id="game_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">All Games</option>
                    @foreach($games as $game)
                        <option value="{{ $game->game_id }}" {{ $selectedGame == $game->game_id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label for="competition_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Competition</label>
                <select name="competition_id" id="competition_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">All Competitions</option>
                    @foreach($competitions as $competition)
                        <option value="{{ $competition->id }}" {{ $selectedCompetition == $competition->id ? 'selected' : '' }}>
                            {{ $competition->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    @if($selectedGame)
        @php $game = $games->firstWhere('game_id', $selectedGame); @endphp
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                {{ $game->name }} Rankings
            </h2>

            <!-- Team Rankings -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Team Rankings</h3>
                @if($teamRankings[$game->game_id]->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No team rankings available yet.</p>
                @else
                    <div class="flex flex-col gap-2">
                        @foreach ($teamRankings[$game->game_id]->take(5) as $index => $ranking)
                            @php
                                $bgClass = match($index) {
                                    0 => 'bg-yellow-100 dark:bg-yellow-900', // Gold
                                    1 => 'bg-gray-200 dark:bg-gray-700',     // Silver
                                    2 => 'bg-orange-100 dark:bg-orange-900', // Bronze
                                    default => 'bg-gray-50 dark:bg-gray-800',
                                };
                            @endphp
                            <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 {{ $bgClass }}">
                                <div class="mr-3">
                                    @if($index == 0)
                                        <span title="1st" class="text-2xl">&#x1F451;</span> {{-- Gold Crown --}}
                                    @elseif($index == 1)
                                        <span title="2nd" class="text-2xl">&#x1F948;</span> {{-- Silver Crown --}}
                                    @elseif($index == 2)
                                        <span title="3rd" class="text-2xl">&#x1F949;</span> {{-- Bronze Crown --}}
                                    @else
                                        <span class="text-lg font-bold min-w-[60px] text-right mr-2">Rank {{ $index + 1 }} :</span>
                                    @endif
                                </div>
                                <div class="flex-1 font-semibold">{{ $ranking['team']->name }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 ml-4">Total Score: <span class="font-bold">{{ $ranking['score'] }}</span></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Individual Rankings -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Individual Rankings</h3>
                @if($playerRankings[$game->game_id]->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400">No individual rankings available yet.</p>
                @else
                    <div class="flex flex-col gap-2">
                        @foreach ($playerRankings[$game->game_id]->take(5) as $index => $ranking)
                            @php
                                $bgClass = match($index) {
                                    0 => 'bg-yellow-100 dark:bg-yellow-900', // Gold
                                    1 => 'bg-gray-200 dark:bg-gray-700',     // Silver
                                    2 => 'bg-orange-100 dark:bg-orange-900', // Bronze
                                    default => 'bg-gray-50 dark:bg-gray-800',
                                };
                            @endphp
                            <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 {{ $bgClass }}">
                                <div class="mr-3">
                                    @if($index == 0)
                                        <span title="1st" class="text-2xl">&#x1F451;</span> {{-- Gold Crown --}}
                                    @elseif($index == 1)
                                        <span title="2nd" class="text-2xl">&#x1F948;</span> {{-- Silver Crown --}}
                                    @elseif($index == 2)
                                        <span title="3rd" class="text-2xl">&#x1F949;</span> {{-- Bronze Crown --}}
                                    @else
                                        <span class="text-lg font-bold min-w-[60px] text-right mr-2">Rank {{ $index + 1 }} :</span>
                                    @endif
                                </div>
                                <div class="flex-1 font-semibold">{{ $ranking['player']->name }}</div>
                                <div class="flex flex-row flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-300 ml-4">
                                    <span>Kills: <span class="font-bold">{{ $ranking['kills'] }}</span></span>
                                    <span>Deaths: <span class="font-bold">{{ $ranking['deaths'] }}</span></span>
                                    <span>Assists: <span class="font-bold">{{ $ranking['assists'] }}</span></span>
                                    <span>KDA: <span class="font-bold">{{ $ranking['kda'] }}</span></span>
                                    <span>KD: <span class="font-bold">{{ $ranking['kd'] }}</span></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="mt-8 text-center text-gray-500 dark:text-gray-400">
            Please select a game to view rankings
        </div>
    @endif
</div>
