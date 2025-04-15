@extends('layouts.app')

@section('content')
    <h1>Tournaments</h1>
    <a href="{{ route('tournaments.create') }}">Create Tournament</a>
    <ul>
        @foreach ($tournaments as $tournament)
            <li>
                <a href="{{ route('tournaments.show', $tournament->id) }}">{{ $tournament->name }}</a>
                <a href="{{ route('tournaments.generateBracket', $tournament->id) }}">Generate Bracket</a>
            </li>
        @endforeach
    </ul>
@endsection
