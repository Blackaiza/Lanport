<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class TournamentBracketSingleElimination extends Component
{
    public $matches;
    public $type;

    public function __construct($matches, $type)
    {
        $this->matches = $matches;
        $this->type = $type;

        // Log component initialization
        Log::info('Single Elimination Component Initialized', [
            'type' => $type,
            'matches_count' => $matches->count()
        ]);
    }

    public function render()
    {
        return view('components.tournament-bracket-single-elimination');
    }
}
