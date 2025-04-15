<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('competition.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Competitions
                            </div>
                        </a>
                    </div>

                    <!-- Competition Title and Join Button Section -->
                    <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 md:mb-0">
                            {{ $competition->title }}
                        </h1>

                        <!-- Join Button - Always visible -->
                        <div class="w-full md:w-auto">
                            <button onclick="openJoinModal({{ $competition->id }})"
                                    class="w-full md:w-auto px-6 py-3 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Join Competition
                            </button>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center md:text-right">
                                {{ $competition->getRemainingSlots() }} slots remaining
                            </p>
                        </div>
                    </div>

                    <!-- Competition Header -->
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Competition Image -->
                        <div class="md:w-1/3">
                            @if ($competition->picture)
                                <img src="{{ asset('storage/' . $competition->picture) }}"
                                     alt="{{ $competition->title }}"
                                     class="w-full h-64 object-cover rounded-lg shadow-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded-lg shadow-lg flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">No Image Available</span>
                                </div>
                            @endif
                        </div>

                        <!-- Competition Info -->
                        <div class="md:w-2/3">
                            <!-- Status Badge -->
                            @php
                                $now = now();
                                $status = match(true) {
                                    $now < $competition->registration_start => 'Upcoming',
                                    $now >= $competition->registration_start && $now <= $competition->registration_end => 'Registration Open',
                                    $now > $competition->registration_end && $now < $competition->tournament_start => 'Registration Closed',
                                    $now >= $competition->tournament_start && $now <= $competition->tournament_end => 'Tournament Active',
                                    default => 'Tournament Ended'
                                };

                                $statusColor = match($status) {
                                    'Upcoming' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    'Registration Open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'Registration Closed' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'Tournament Active' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                    'Tournament Ended' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                                {{ $status }}
                            </span>

                            <!-- Key Information -->
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Registration Period</h3>
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <div>
                                                <div>Start: {{ $competition->registration_start->format('M d, Y H:i') }}</div>
                                                <div>End: {{ $competition->registration_end->format('M d, Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tournament Period</h3>
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <div>Start: {{ $competition->tournament_start->format('M d, Y H:i') }}</div>
                                                <div>End: {{ $competition->tournament_end->format('M d, Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Team Information</h3>
                                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <div>
                                                <div>Maximum Teams: {{ $competition->team_count }}</div>
                                                <div>Registered Teams: {{ $competition->teams->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Competition Description -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Description</h2>
                        <div class="prose prose-blue max-w-none dark:prose-invert">
                            {!! $competition->description !!}
                        </div>
                    </div>

                    <!-- Registered Teams -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Registered Teams</h2>
                        @if($competition->teams->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($competition->teams as $team)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $team->team_name }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            Status:
                                            <span class="@if($team->pivot->status === 'approved') text-green-600 @elseif($team->pivot->status === 'rejected') text-red-600 @else text-yellow-600 @endif">
                                                {{ ucfirst($team->pivot->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No teams have registered yet.</p>
                        @endif
                    </div>

                    <!-- Add Tournament Bracket Section Here -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                            Tournament Bracket
                        </h2>

                        @php
                            // Debug information
                            $firstMatch = $competition->matches->first();
                            $competitionType = $competition->tournament_type;
                            $matchType = $firstMatch?->tournament_type;

                            // Get the tournament type from the first match if it exists, otherwise use competition's type
                            $tournamentType = $matchType ?? $competitionType;

                            // Debug output
                            // echo "<div class='mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg'>";
                            // echo "<h3 class='text-lg font-bold mb-2'>Debug Information</h3>";
                            // echo "<p>Competition Type: " . $competitionType . "</p>";
                            // echo "<p>First Match Type: " . $matchType . "</p>";
                            // echo "<p>Final Type Used: " . $tournamentType . "</p>";
                            // echo "<p>Total Matches: " . $competition->matches->count() . "</p>";

                            if ($tournamentType === 'double_elimination') {
                                // echo "<div class='mt-4'>";
                                // echo "<h4 class='font-bold'>Double Elimination Details</h4>";
                                // echo "<p>Upper Bracket Matches: " . $competition->matches->where('bracket', 'upper')->count() . "</p>";
                                // echo "<p>Lower Bracket Matches: " . $competition->matches->where('bracket', 'lower')->count() . "</p>";

                                $upperRounds = $competition->matches->where('bracket', 'upper')->pluck('round')->unique();
                                $lowerRounds = $competition->matches->where('bracket', 'lower')->pluck('round')->unique();

                                // echo "<p>Upper Bracket Rounds: " . implode(', ', $upperRounds->toArray()) . "</p>";
                                // echo "<p>Lower Bracket Rounds: " . implode(', ', $lowerRounds->toArray()) . "</p>";

                                // Show all matches with their details
                            //     echo "<div class='mt-4'>";
                            //     echo "<h4 class='font-bold'>All Matches</h4>";
                            //     foreach ($competition->matches as $match) {
                            //         echo "<div class='mt-2 p-2 bg-white dark:bg-gray-700 rounded'>";
                            //         echo "Match ID: " . $match->id . "<br>";
                            //         echo "Round: " . $match->round . "<br>";
                            //         echo "Bracket: " . $match->bracket . "<br>";
                            //         echo "Match Number: " . $match->match_number . "<br>";
                            //         echo "Team 1: " . ($match->team1 ? $match->team1->team_name : 'TBD') . "<br>";
                            //         echo "Team 2: " . ($match->team2 ? $match->team2->team_name : 'TBD') . "<br>";
                            //         echo "Winner: " . ($match->winner ? $match->winner->team_name : 'TBD') . "<br>";
                            //         echo "</div>";
                            //     }
                            //     echo "</div>";
                            //     echo "</div>";
                             }
                            // echo "</div>";
                        @endphp

                        @switch($tournamentType)
                            @case('single_elimination')
                                <x-tournament-bracket-single-elimination
                                    :matches="$competition->matches->load(['team1', 'team2', 'winner'])"
                                    :type="$tournamentType"
                                />
                                @break
                            @case('double_elimination')
                                {{-- <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                    <h3 class="text-lg font-bold mb-2">Debug Information</h3>
                                    <p>Total Matches: {{ $competition->matches->count() }}</p>
                                    <p>Upper Bracket Matches: {{ $competition->matches->where('bracket', 'upper')->count() }}</p>
                                    <p>Lower Bracket Matches: {{ $competition->matches->where('bracket', 'lower')->count() }}</p>

                                    <h4 class="font-bold mt-4">Match Details:</h4>
                                    @foreach($competition->matches->load(['team1', 'team2', 'winner']) as $match)
                                        <div class="mt-2 p-2 bg-white dark:bg-gray-700 rounded">
                                            <p>Match ID: {{ $match->id }}</p>
                                            <p>Round: {{ $match->round }}</p>
                                            <p>Bracket: {{ $match->bracket }}</p>
                                            <p>Match Number: {{ $match->match_number }}</p>
                                            <p>Team 1: {{ $match->team1 ? $match->team1->team_name : 'TBD' }}</p>
                                            <p>Team 2: {{ $match->team2 ? $match->team2->team_name : 'TBD' }}</p>
                                            <p>Winner: {{ $match->winner ? $match->winner->team_name : 'TBD' }}</p>
                                        </div>
                                    @endforeach
                                </div> --}}

                                <x-tournament-bracket-double-elimination
                                    :matches="$competition->matches->load(['team1', 'team2', 'winner'])"
                                    :type="$tournamentType"
                                />
                                @break
                            @case('round_robin')
                                <x-tournament-bracket-round-robin
                                    :matches="$competition->matches"
                                    :type="$tournamentType"
                                />
                                @break
                            @default
                                <div class="text-center py-8">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <p>Invalid tournament type: {{ $tournamentType }}</p>
                                    </div>
                                </div>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Modal -->
    <div id="joinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mb-4">Join Competition</h3>

                @php
                    $userTeams = Auth::user()->teams()
                        ->where('leader_id', Auth::id())
                        ->get();
                @endphp

                @if($userTeams->count() > 0)
                    <form id="joinCompetitionForm" action="{{ route('competition.join', $competition->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mt-2 px-7 py-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Select Your Team
                            </label>
                            <select name="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select a team...</option>
                                @foreach($userTeams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="items-center px-4 py-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Join Competition
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center px-7 py-3">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">You don't have any teams where you're the leader.</p>
                        <a href="{{ route('team.create') }}"
                           class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md inline-block hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Create a Team
                        </a>
                    </div>
                @endif

                <!-- Close button -->
                <button onclick="closeJoinModal()"
                        class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function openJoinModal(competitionId) {
            const modal = document.getElementById('joinModal');
            modal.classList.remove('hidden');
        }

        function closeJoinModal() {
            const modal = document.getElementById('joinModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('joinModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
