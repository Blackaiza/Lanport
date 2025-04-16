<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bracket/0.11.1/jquery.bracket.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bracket/0.11.1/jquery.bracket.min.js"></script>


@extends('layouts.app')

@section('content')
    <h1>{{ $tournament->name }}</h1>

    <div id="bracket"></div>

    <script>
        var bracketData = {
            teams: [
                @foreach ($tournament->matches->where('bracket', 'winners')->groupBy('round') as $roundMatches)
                    @foreach ($roundMatches as $match)
                        ["{{ optional($match->team1)->name ?? 'TBD' }}", "{{ optional($match->team2)->name ?? 'TBD' }}"],
                    @endforeach
                @endforeach
            ],
            results: []
        };

        var losersBracketData = {
            teams: [
                @foreach ($tournament->matches->where('bracket', 'losers')->groupBy('round') as $roundMatches)
                    @foreach ($roundMatches as $match)
                        ["{{ optional($match->team1)->name ?? 'TBD' }}", "{{ optional($match->team2)->name ?? 'TBD' }}"],
                    @endforeach
                @endforeach
            ],
            results: []
        };

        $(function() {
            $('#bracket').bracket({ init: bracketData });
            $('#losersBracket').bracket({ init: losersBracketData });
        });
    </script>

    <h3>Losers' Bracket</h3>
    <div id="losersBracket"></div>

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

    <form id="joinCompetitionForm" action="" method="POST" class="mt-4">
        @csrf
        <div class="mt-2 px-7 py-3">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Select Your Team
            </label>
            <select name="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('team_id') border-red-500 @enderror">
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

@endsection
