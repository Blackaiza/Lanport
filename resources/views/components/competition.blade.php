@props(['competitions'])

<div class="bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-5">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Competitions</h2>
    </div>
    <div class="overflow-x-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($competitions as $competition)
                <div class="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <a href="#">
                        @if ($competition->picture)
                            <img class="rounded-t-lg w-full h-48 object-cover" src="{{ asset('storage/' . $competition->picture) }}" alt="{{ $competition->title }}" />
                        @else
                            <div class="h-48 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-t-lg">
                                <span class="text-gray-500 dark:text-gray-400">No Image</span>
                            </div>
                        @endif
                    </a>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $competition->title }}</h5>
                            @php
                                $status = $competition->getStatus();
                                $statusColors = [
                                    'upcoming' => 'bg-blue-100 text-blue-800',
                                    'registration_open' => 'bg-green-100 text-green-800',
                                    'registration_closed' => 'bg-yellow-100 text-yellow-800',
                                    'ongoing' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusText = [
                                    'upcoming' => 'Upcoming',
                                    'registration_open' => 'Registration Open',
                                    'registration_closed' => 'Registration Closed',
                                    'ongoing' => 'Tournament Ongoing',
                                    'completed' => 'Completed'
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
                                {{ $statusText[$status] }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <div class="prose prose-sm max-w-none dark:prose-invert">
                                {!! Str::limit($competition->description, 100) !!}
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <!-- Registration Period -->
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Registration:</span><br>
                                    {{ Carbon\Carbon::parse($competition->registration_start)->format('M d, Y H:i') }} -
                                    {{ Carbon\Carbon::parse($competition->registration_end)->format('M d, Y H:i') }}
                                </div>
                            </div>

                            <!-- Tournament Period -->
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Tournament:</span><br>
                                    {{ Carbon\Carbon::parse($competition->tournament_start)->format('M d, Y H:i') }} -
                                    {{ Carbon\Carbon::parse($competition->tournament_end)->format('M d, Y H:i') }}
                                </div>
                            </div>

                            <!-- Team Slots -->
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <div class="text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">Teams:</span><br>
                                    <span class="text-green-600 dark:text-green-400">{{ $competition->approvedTeams()->count() }}</span> registered /
                                    {{ $competition->team_count }} total
                                    <br>
                                    <span class="text-sm text-blue-600 dark:text-blue-400">
                                        {{ $competition->getRemainingSlots() }} slots remaining
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                @php
                                    $progressPercentage = min(100, ($competition->approvedTeams()->count() / $competition->team_count) * 100);
                                @endphp
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('competition.show', $competition->id) }}"
                               class="px-3 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                More Details
                            </a>
                            @if($competition->getStatus() === 'registration_open' && $competition->getRemainingSlots() > 0)
                                <button
                                    onclick="openJoinModal({{ $competition->id }})"
                                    class="px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                    Join Competition
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">No competitions available at the moment</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modified Join Modal -->
<div id="joinModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mb-4">Join Competition</h3>

            @php
                $userTeams = Auth::user()->teams()
                    ->where('leader_id', Auth::id())
                    ->get();
            @endphp

            @if($userTeams->count() > 0)
                <form id="joinCompetitionForm" action="" method="POST" class="mt-4">
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
    const form = document.getElementById('joinCompetitionForm');
    if (form) {
        form.action = `/competition/${competitionId}/join`;
    }
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