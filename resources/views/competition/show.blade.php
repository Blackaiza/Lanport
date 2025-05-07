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

                        <!-- Join Button - Only visible when registration is open -->
                        @php
                            $now = now();
                            $isRegistrationOpen = $now >= $competition->registration_start && $now <= $competition->registration_end;
                        @endphp

                        @if($isRegistrationOpen)
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
                        @endif
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

                    <!-- Social Media Links -->
                    @php
                        $userTeams = auth()->user()->teams;
                        $isRegistered = false;
                        foreach ($userTeams as $userTeam) {
                            if ($competition->teams->contains($userTeam->id)) {
                                $isRegistered = true;
                                break;
                            }
                        }
                    @endphp

                    @if($isRegistered && ($competition->whatsapp_link || $competition->telegram_link || $competition->discord_link))
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Join Our Community</h2>
                        <div class="flex flex-wrap gap-4">
                            @if($competition->whatsapp_link)
                            <a href="{{ $competition->whatsapp_link }}" target="_blank"
                               class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                WhatsApp Group
                            </a>
                            @endif

                            @if($competition->telegram_link)
                            <a href="{{ $competition->telegram_link }}" target="_blank"
                               class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.203-.657-.643.136-.953l11.566-4.458c.534-.197 1.001.13.832.941z"/>
                                </svg>
                                Telegram Group
                            </a>
                            @endif

                            @if($competition->discord_link)
                            <a href="{{ $competition->discord_link }}" target="_blank"
                               class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515a.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0a12.64 12.64 0 0 0-.617-1.25a.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057a19.9 19.9 0 0 0 5.993 3.03a.078.078 0 0 0 .084-.028a14.09 14.09 0 0 0 1.226-1.994a.076.076 0 0 0-.041-.106a13.107 13.107 0 0 1-1.872-.892a.077.077 0 0 1 .008-.128a10.2 10.2 0 0 0 .372-.292a.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1 .006.127a12.299 12.299 0 0 1-1.873.892a.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028a19.839 19.839 0 0 0 6.002-3.03a.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.956-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419c0-1.333.955-2.419 2.157-2.419c1.21 0 2.176 1.096 2.157 2.42c0 1.333-.946 2.418-2.157 2.418z"/>
                                </svg>
                                Discord Server
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Prize Pool -->
                    @if($competition->prize_pool)
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Prize Pool</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($competition->prize_pool as $prize)
                            <div class="rounded-lg p-4 @switch($prize['position'])
                                    @case(1)
                                        bg-gradient-to-br from-yellow-400 to-yellow-600 dark:from-yellow-500 dark:to-yellow-700
                                        @break
                                    @case(2)
                                        bg-gradient-to-br from-gray-300 to-gray-400 dark:from-gray-400 dark:to-gray-500
                                        @break
                                    @case(3)
                                        bg-gradient-to-br from-amber-600 to-amber-800 dark:from-amber-700 dark:to-amber-900
                                        @break
                                    @default
                                        bg-gray-50 dark:bg-gray-700
                                @endswitch">
                                <div class="font-semibold @switch($prize['position'])
                                        @case(1)
                                            text-white
                                            @break
                                        @case(2)
                                            text-white
                                            @break
                                        @case(3)
                                            text-white
                                            @break
                                        @default
                                            text-gray-900 dark:text-white
                                    @endswitch">
                                    @switch($prize['position'])
                                        @case(1)
                                            Winner
                                            @break
                                        @case(2)
                                            Second Place
                                            @break
                                        @case(3)
                                            Third Place
                                            @break
                                        @default
                                            {{ $prize['position'] }}th Place
                                    @endswitch
                                </div>
                                <div class="mt-2 space-y-2">
                                    @if(in_array('money', $prize['prize_types']))
                                    <div class="flex items-center @switch($prize['position'])
                                            @case(1)
                                                text-white
                                                @break
                                            @case(2)
                                                text-white
                                                @break
                                            @case(3)
                                                text-white
                                                @break
                                            @default
                                                text-gray-600 dark:text-gray-300
                                        @endswitch">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        RM {{ number_format($prize['money_amount'], 2) }}
                                    </div>
                                    @endif

                                    @if(in_array('diamond', $prize['prize_types']))
                                    <div class="flex items-center @switch($prize['position'])
                                            @case(1)
                                                text-white
                                                @break
                                            @case(2)
                                                text-white
                                                @break
                                            @case(3)
                                                text-white
                                                @break
                                            @default
                                                text-gray-600 dark:text-gray-300
                                        @endswitch">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ $prize['diamond_amount'] }} Diamonds
                                    </div>
                                    @endif

                                    @if(in_array('hamper', $prize['prize_types']))
                                    <div class="flex items-center @switch($prize['position'])
                                            @case(1)
                                                text-white
                                                @break
                                            @case(2)
                                                text-white
                                                @break
                                            @case(3)
                                                text-white
                                                @break
                                            @default
                                                text-gray-600 dark:text-gray-300
                                        @endswitch">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        {{ $prize['hamper_description'] }}
                                    </div>
                                    @endif

                                    @if(in_array('other', $prize['prize_types']))
                                    <div class="flex items-center @switch($prize['position'])
                                            @case(1)
                                                text-white
                                                @break
                                            @case(2)
                                                text-white
                                                @break
                                            @case(3)
                                                text-white
                                                @break
                                            @default
                                                text-gray-600 dark:text-gray-300
                                        @endswitch">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ $prize['other_prize'] }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Registered Teams -->
                    {{-- <div class="mt-8">
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
                    </div> --}}

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
                        ->where('game', $competition->game_id)
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
                        <p class="text-gray-600 dark:text-gray-400 mb-4">You don't have any teams for {{ $competition->game->name }} where you're the leader.</p>
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

    <!-- Notification Modal -->
    <div id="notificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div id="notificationIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                    <!-- Success Icon -->
                    <svg id="successIcon" class="h-12 w-12 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <!-- Error Icon -->
                    <svg id="errorIcon" class="h-12 w-12 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 id="notificationTitle" class="text-lg font-medium text-gray-900 dark:text-white text-center mb-2"></h3>
                <div id="notificationMessage" class="mt-2 px-7 py-3">
                    <p id="notificationText" class="text-sm text-gray-500 dark:text-gray-400 text-center"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeNotificationModal()"
                            class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Close
                    </button>
                </div>
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

        // Show notification modal
        function showNotificationModal(type, message) {
            const modal = document.getElementById('notificationModal');
            const successIcon = document.getElementById('successIcon');
            const errorIcon = document.getElementById('errorIcon');
            const title = document.getElementById('notificationTitle');
            const text = document.getElementById('notificationText');

            // Set icon and colors based on type
            if (type === 'success') {
                successIcon.classList.remove('hidden');
                errorIcon.classList.add('hidden');
                title.textContent = 'Success!';
                title.classList.add('text-green-600');
                title.classList.remove('text-red-600');
            } else {
                successIcon.classList.add('hidden');
                errorIcon.classList.remove('hidden');
                title.textContent = 'Error!';
                title.classList.add('text-red-600');
                title.classList.remove('text-green-600');
            }

            text.textContent = message;
            modal.classList.remove('hidden');
        }

        function closeNotificationModal() {
            const modal = document.getElementById('notificationModal');
            modal.classList.add('hidden');
        }

        // Check for session messages and show notification
        @if(session('success'))
            showNotificationModal('success', '{{ session('success') }}');
        @endif

        @if(session('error'))
            showNotificationModal('error', '{{ session('error') }}');
        @endif

        // Close notification modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('notificationModal');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
