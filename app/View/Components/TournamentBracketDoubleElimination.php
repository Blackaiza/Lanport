<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TournamentBracketDoubleElimination extends Component
{
    public $matches;
    public $type;

    public function __construct($matches, $type)
    {
        $this->matches = $matches;
        $this->type = $type;
    }

    public function render()
    {
        return view('components.tournament-bracket-double-elimination');
    }
}
