<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TournamentResource\Pages;
use App\Filament\Resources\TournamentResource\RelationManagers;
use App\Models\Tournament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Competition;
use App\Models\TournamentMatch;
use App\Models\TeamCompetition;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use App\Filament\Resources\TournamentResource\SingleEliminationFormat;
use App\Filament\Resources\TournamentResource\DoubleEliminationFormat;

class TournamentResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Competition Management';
    protected static ?string $navigationLabel = 'Competition Brackets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('tournament_type')
                            ->options([
                                'single_elimination' => 'Single Elimination',
                                'double_elimination' => 'Double Elimination',
                                'round_robin' => 'Round Robin',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $get, callable $set) {
                                if ($state) {
                                    $teams = TeamCompetition::where('competition_id', $get('id'))
                                        ->where('status', 'approved')
                                        ->get()
                                        ->shuffle();

                                    $matchups = [];
                                    for ($i = 0; $i < $teams->count(); $i += 2) {
                                        $matchups[] = [
                                            'team1' => $teams[$i]->team->name ?? 'BYE',
                                            'team2' => isset($teams[$i + 1]) ? $teams[$i + 1]->team->name : 'BYE',
                                        ];
                                    }
                                    $set('preview_matchups', $matchups);
                                }
                            })
                            ->visible(fn ($record) => !$record->matches()->exists()),

                        Forms\Components\View::make('tournament.preview-matchups')
                            ->viewData([
                                'matchups' => fn ($get) => $get('preview_matchups') ?? [],
                                'type' => fn ($get) => $get('tournament_type')
                            ])
                            ->visible(fn ($get) => !empty($get('preview_matchups'))),

                        // Dynamic Round Sections for Single Elimination
                        Forms\Components\Grid::make()
                            ->schema(function ($record) {
                                if (!$record || $record->tournament_type !== 'single_elimination') {
                                    return [];
                                }

                                $rounds = TournamentMatch::where('competition_id', $record->id)
                                    ->select('round')
                                    ->distinct()
                                    ->get()
                                    ->pluck('round')
                                    ->sort(function ($a, $b) {
                                        // Extract numbers from round names
                                        $aNum = (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT);
                                        $bNum = (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);

                                        // Handle special round names
                                        $roundOrder = [
                                            'finals' => PHP_INT_MAX,           // Highest value to be last
                                            'semi_finals' => PHP_INT_MAX - 1,  // Second highest to be second last
                                            'quarter_finals' => PHP_INT_MAX - 2, // Third highest to be third last
                                        ];

                                        // Get order values for special rounds or use round number
                                        $aOrder = $roundOrder[$a] ?? $aNum;
                                        $bOrder = $roundOrder[$b] ?? $bNum;

                                        // Sort in ascending order
                                        return $aOrder <=> $bOrder;
                                    });

                                $completedRounds = [];
                                $lastCompletedRound = null;

                                // Determine which rounds are completed
                                foreach ($rounds as $round) {
                                    $allMatchesComplete = TournamentMatch::where('competition_id', $record->id)
                                        ->where('round', $round)
                                        ->whereNotNull('winner_id')
                                        ->count() === TournamentMatch::where('competition_id', $record->id)
                                        ->where('round', $round)
                                        ->count();

                                    if ($allMatchesComplete) {
                                        $completedRounds[] = $round;
                                        $lastCompletedRound = $round;
                                    }
                                }

                                return $rounds->map(function ($round) use ($lastCompletedRound, $completedRounds, $rounds) {
                                    $roundTitle = str_replace('_', ' ', ucfirst($round));
                                    $isCompleted = in_array($round, $completedRounds);
                                    $isActive = $round === $lastCompletedRound ||
                                               (empty($completedRounds) && $round === $rounds->first()) ||
                                               (in_array($lastCompletedRound, $completedRounds) &&
                                                array_search($round, $rounds->toArray()) === array_search($lastCompletedRound, $rounds->toArray()) + 1);

                                    return Forms\Components\Section::make($roundTitle)
                                        ->schema([
                                            Forms\Components\Grid::make(1)
                                                ->schema([
                                                    Forms\Components\View::make('tournament.round-status')
                                                        ->viewData([
                                                            'isCompleted' => $isCompleted,
                                                            'isActive' => $isActive,
                                                        ]),
                                                    Forms\Components\Actions::make([
                                                        Forms\Components\Actions\Action::make("complete_{$round}")
                                                            ->label('Complete Round & Advance Winners')
                                                            ->button()
                                                            ->color('success')
                                                            ->icon('heroicon-o-check-circle')
                                                            ->action(function (Competition $record) use ($round) {
                                                                // First save the form data
                                                                $data = request()->all();
                                                                foreach ($data["{$round}_matches"] ?? [] as $index => $matchData) {
                                                                    if (isset($matchData['team1_score']) && isset($matchData['team2_score'])) {
                                                                        $match = TournamentMatch::find($matchData['id']);
                                                                        if ($match) {
                                                                            $match->update([
                                                                                'team1_score' => $matchData['team1_score'],
                                                                                'team2_score' => $matchData['team2_score']
                                                                            ]);
                                                                        }
                                                                    }
                                                                }
                                                                // Then complete the round
                                                                $numTeams = TeamCompetition::where('competition_id', $record->id)
                                                                    ->where('status', 'approved')
                                                                    ->count();
                                                                static::completeRound($record, $round, $numTeams);
                                                            })
                                                            ->visible(fn ($record) =>
                                                                $record?->status !== 'completed' &&
                                                                $isActive &&
                                                                !$isCompleted
                                                            ),
                                                    ]),
                                                ]),
                                            Forms\Components\Repeater::make("{$round}_matches")
                                                ->schema([
                                                    Forms\Components\Grid::make(3)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('team1_score')
                                                                ->numeric()
                                                                ->label(function ($record) {
                                                                    if ($record?->team1_id) {
                                                                        $team = TeamCompetition::with('team')
                                                                            ->find($record->team1_id);
                                                                        $name = $team?->team?->name;
                                                                        // Add crown if this is the winner in finals
                                                                        if ($record->round === 'finals' && $record->winner_id === $record->team1_id) {
                                                                            return "👑 {$name}";
                                                                        }
                                                                        return $name;
                                                                    }
                                                                    return 'TBD';
                                                                })
                                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                                ->nullable(),
                                                            Forms\Components\TextInput::make('match_number')
                                                                ->disabled()
                                                                ->label(function ($record) {
                                                                    if (!$record) {
                                                                        return 'Match';
                                                                    }

                                                                    $team1Name = 'TBD';
                                                                    $team2Name = 'TBD';

                                                                    if ($record->team1_id) {
                                                                        $team1 = TeamCompetition::with('team')->find($record->team1_id);
                                                                        $team1Name = $team1?->team?->name ?? 'TBD';
                                                                    }

                                                                    if ($record->team2_id) {
                                                                        $team2 = TeamCompetition::with('team')->find($record->team2_id);
                                                                        $team2Name = $team2?->team?->name ?? 'TBD';
                                                                    }

                                                                    $matchNumber = $record->match_number ?? 'N/A';
                                                                    return "Match {$matchNumber}: {$team1Name} vs {$team2Name}";
                                                                }),
                                                            Forms\Components\TextInput::make('team2_score')
                                                                ->numeric()
                                                                ->label(function ($record) {
                                                                    if ($record?->team2_id) {
                                                                        $team = TeamCompetition::with('team')
                                                                            ->find($record->team2_id);
                                                                        $name = $team?->team?->name;
                                                                        // Add crown if this is the winner in finals
                                                                        if ($record->round === 'finals' && $record->winner_id === $record->team2_id) {
                                                                            return "👑 {$name}";
                                                                        }
                                                                        return $name;
                                                                    }
                                                                    return 'TBD';
                                                                })
                                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                                ->nullable(),
                                                        ]),
                                                ])
                                                ->columns(1)
                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                ->relationship('matches', function ($query) use ($round) {
                                                    return $query->where('round', $round)
                                                        ->with(['team1.team', 'team2.team'])
                                                        ->orderBy('match_number');
                                                })
                                        ])
                                        ->collapsible()
                                        ->collapsed(fn () => !$isActive);
                                })->toArray();
                            })
                            ->visible(fn ($record) => $record?->tournament_type === 'single_elimination'),


                        // // Champion Section
                        // Forms\Components\Section::make('👑 Champion Round 👑')
                        //     ->schema([
                        //         Forms\Components\Grid::make(1)
                        //             ->schema([
                        //                 Forms\Components\Actions::make([
                        //                     Forms\Components\Actions\Action::make('complete_champion')
                        //                         ->label('Crown the Champion')
                        //                         ->button()
                        //                         ->color('warning')
                        //                         ->action(function (Competition $record) {
                        //                             static::completeRound($record, 'champion');
                        //                             $record->update(['status' => 'completed']);

                        //                             $championMatch = $record->matches()
                        //                                 ->where('round', 'champion')
                        //                                 ->first();

                        //                             if ($championMatch && $championMatch->winner_id) {
                        //                                 $champion = TeamCompetition::with('team')->find($championMatch->winner_id);
                        //                                 Notification::make()
                        //                                     ->title('👑 Champion Crowned 👑')
                        //                                     ->body('All hail the Champion: ' . $champion->team->name . '! 🎉👑🏆')
                        //                                     ->success()
                        //                                     ->send();
                        //                             }
                        //                         })
                        //                         ->visible(fn ($record) => $record?->status !== 'completed'),
                        //                 ]),
                        //             ]),
                        //         Forms\Components\Repeater::make('champion_matches')
                        //             ->schema([
                        //                 Forms\Components\Grid::make(3)
                        //                     ->schema([
                        //                         Forms\Components\TextInput::make('team1_score')
                        //                             ->numeric()
                        //                             ->label(fn ($record) => '👑 ' . (optional($record)->team1?->team?->name ?? ''))
                        //                             ->nullable(),
                        //                         Forms\Components\TextInput::make('match_number')
                        //                             ->disabled()
                        //                             ->label('Champion Match'),
                        //                         Forms\Components\TextInput::make('team2_score')
                        //                             ->numeric()
                        //                             ->label(fn ($record) => '👑 ' . (optional($record)->team2?->team?->name ?? ''))
                        //                             ->nullable(),
                        //                     ]),
                        //             ])
                        //             ->columns(1)
                        //             ->disabled(fn ($record) => $record?->status === 'completed')
                        //             ->relationship('matches', fn ($query) => $query->where('round', 'champion'))
                        //     ])
                        //     ->extraAttributes(['class' => 'bg-yellow-50 dark:bg-yellow-900 border-2 border-yellow-500'])
                        //     ->collapsible()
                        //     ->collapsed(),

                        // Dynamic Round Sections for Double Elimination
                        Forms\Components\Grid::make()
                            ->schema(function ($record) {
                                if (!$record || $record->tournament_type !== 'double_elimination') {
                                    return [];
                                }

                                $rounds = TournamentMatch::where('competition_id', $record->id)
                                    ->select('round')
                                    ->distinct()
                                    ->get()
                                    ->pluck('round')
                                    ->sort(function ($a, $b) {
                                        // Extract numbers from round names
                                        $aNum = (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT);
                                        $bNum = (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);

                                        // Handle special round names
                                        $roundOrder = [
                                            'grand_finals' => PHP_INT_MAX,           // Highest value to be last
                                            'finals' => PHP_INT_MAX - 1,            // Second highest
                                            'upper_semi_finals' => PHP_INT_MAX - 2,  // Third highest
                                            'lower_semi_finals' => PHP_INT_MAX - 3,  // Fourth highest
                                            'upper_quarter_finals' => PHP_INT_MAX - 4,
                                            'lower_quarter_finals' => PHP_INT_MAX - 5,
                                            'upper_round_2' => PHP_INT_MAX - 6,
                                            'lower_round_2' => PHP_INT_MAX - 7,
                                            'upper_round_1' => PHP_INT_MAX - 8,
                                            'lower_round_1' => PHP_INT_MAX - 9,
                                        ];

                                        // Get order values for special rounds or use round number
                                        $aOrder = $roundOrder[$a] ?? $aNum;
                                        $bOrder = $roundOrder[$b] ?? $bNum;

                                        // Sort in ascending order
                                        return $aOrder <=> $bOrder;
                                    });

                                $completedRounds = [];
                                $lastCompletedRound = null;

                                // Determine which rounds are completed
                                foreach ($rounds as $round) {
                                    $allMatchesComplete = TournamentMatch::where('competition_id', $record->id)
                                        ->where('round', $round)
                                        ->whereNotNull('winner_id')
                                        ->count() === TournamentMatch::where('competition_id', $record->id)
                                        ->where('round', $round)
                                        ->count();

                                    if ($allMatchesComplete) {
                                        $completedRounds[] = $round;
                                        $lastCompletedRound = $round;
                                    }
                                }

                                return $rounds->map(function ($round) use ($lastCompletedRound, $completedRounds, $rounds, $record) {
                                    $roundTitle = str_replace('_', ' ', ucfirst($round));
                                    $isCompleted = in_array($round, $completedRounds);

                                    // For lower rounds, check if the corresponding upper round is completed
                                    if (str_starts_with($round, 'lower_')) {
                                        $correspondingUpperRound = str_replace('lower_', 'upper_', $round);
                                        $upperRoundCompleted = in_array($correspondingUpperRound, $completedRounds);

                                        // For lower_round_1, check if upper_round_1 is completed
                                        if ($round === 'lower_round_1') {
                                            $isActive = in_array('upper_round_1', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'lower_round_2') {
                                            // Lower Round 2 becomes active after Upper Round 2 is completed
                                            $isActive = in_array('upper_round_2', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'lower_quarter_finals') {
                                            // Lower Quarter Finals become active after Upper Quarter Finals is completed
                                            $isActive = in_array('upper_quarter_finals', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'lower_semi_finals') {
                                            // Lower Semi Finals become active after Upper Semi Finals is completed
                                            $isActive = in_array('upper_semi_finals', $completedRounds) && !$isCompleted;
                                        } else {
                                            $isActive = $upperRoundCompleted && !$isCompleted;
                                        }
                                    } else {
                                        // For upper rounds
                                        if ($round === 'upper_round_2') {
                                            // Upper Round 2 becomes active after Upper Round 1 is completed
                                            $isActive = in_array('upper_round_1', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'upper_quarter_finals') {
                                            // Upper Quarter Finals become active after Upper Round 2 is completed
                                            $isActive = in_array('upper_round_2', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'upper_semi_finals') {
                                            // Upper Semi Finals become active after Upper Quarter Finals is completed
                                            $isActive = in_array('upper_quarter_finals', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'finals') {
                                            // Finals become active after Semi Finals is completed
                                            $isActive = in_array('semi_finals', $completedRounds) && !$isCompleted;
                                        } else if ($round === 'grand_finals') {
                                            // Grand Finals become active if the winner of Upper Semi Finals loses in Finals
                                            $finalsMatch = TournamentMatch::where('competition_id', $record->id)
                                                ->where('round', 'finals')
                                                ->first();

                                            $upperSemiFinalsWinner = TournamentMatch::where('competition_id', $record->id)
                                                ->where('round', 'upper_semi_finals')
                                                ->whereNotNull('winner_id')
                                                ->first();

                                            $isActive = $finalsMatch &&
                                                       $upperSemiFinalsWinner &&
                                                       $finalsMatch->winner_id !== $upperSemiFinalsWinner->winner_id &&
                                                       !$isCompleted;
                                        } else if ($round === 'upper_round_1') {
                                            // Upper Round 1 is active if it's the first round or if no rounds are completed
                                            $isActive = empty($completedRounds) ||
                                                       $round === $lastCompletedRound ||
                                                       (in_array($lastCompletedRound, $completedRounds) &&
                                                        array_search($round, $rounds->toArray()) === array_search($lastCompletedRound, $rounds->toArray()) + 1);
                                        } else {
                                            // For other upper rounds, use standard progression
                                            $isActive = $round === $lastCompletedRound ||
                                                      (empty($completedRounds) && $round === $rounds->first()) ||
                                                      (in_array($lastCompletedRound, $completedRounds) &&
                                                       array_search($round, $rounds->toArray()) === array_search($lastCompletedRound, $rounds->toArray()) + 1);
                                        }
                                    }

                                    return Forms\Components\Section::make($roundTitle)
                                        ->schema([
                                            Forms\Components\Grid::make(1)
                                                ->schema([
                                                    Forms\Components\View::make('tournament.round-status')
                                                        ->viewData([
                                                            'isCompleted' => $isCompleted,
                                                            'isActive' => $isActive,
                                                        ]),
                                                    Forms\Components\Actions::make([
                                                        Forms\Components\Actions\Action::make("complete_{$round}")
                                                            ->label('Complete Round & Advance Winners')
                                                            ->button()
                                                            ->color('success')
                                                            ->icon('heroicon-o-check-circle')
                                                            ->action(function (Competition $record) use ($round) {
                                                                static::completeRound($record, $round);
                                                            })
                                                            ->visible(fn ($record) =>
                                                                $record?->status !== 'completed' &&
                                                                $isActive &&
                                                                !$isCompleted
                                                            ),
                                                    ]),
                                                ]),
                                            Forms\Components\Repeater::make("{$round}_matches")
                                                ->schema([
                                                    Forms\Components\Grid::make(3)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('team1_score')
                                                                ->numeric()
                                                                ->label(function ($record) {
                                                                    if ($record?->team1_id) {
                                                                        $team = TeamCompetition::with('team')
                                                                            ->find($record->team1_id);
                                                                        $name = $team?->team?->name;
                                                                        // Add crown if this is the winner in finals
                                                                        if ($record->round === 'finals' && $record->winner_id === $record->team1_id) {
                                                                            return "👑 {$name}";
                                                                        }
                                                                        return $name;
                                                                    }
                                                                    return 'TBD';
                                                                })
                                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                                ->nullable(),
                                                            Forms\Components\TextInput::make('match_number')
                                                                ->disabled()
                                                                ->label(function ($record) {
                                                                    if (!$record) {
                                                                        return 'Match';
                                                                    }

                                                                    $team1Name = 'TBD';
                                                                    $team2Name = 'TBD';

                                                                    if ($record->team1_id) {
                                                                        $team1 = TeamCompetition::with('team')->find($record->team1_id);
                                                                        $team1Name = $team1?->team?->name ?? 'TBD';
                                                                    }

                                                                    if ($record->team2_id) {
                                                                        $team2 = TeamCompetition::with('team')->find($record->team2_id);
                                                                        $team2Name = $team2?->team?->name ?? 'TBD';
                                                                    }

                                                                    $matchNumber = $record->match_number ?? 'N/A';
                                                                    return "Match {$matchNumber}: {$team1Name} vs {$team2Name}";
                                                                }),
                                                            Forms\Components\TextInput::make('team2_score')
                                                                ->numeric()
                                                                ->label(function ($record) {
                                                                    if ($record?->team2_id) {
                                                                        $team = TeamCompetition::with('team')
                                                                            ->find($record->team2_id);
                                                                        $name = $team?->team?->name;
                                                                        // Add crown if this is the winner in finals
                                                                        if ($record->round === 'finals' && $record->winner_id === $record->team2_id) {
                                                                            return "👑 {$name}";
                                                                        }
                                                                        return $name;
                                                                    }
                                                                    return 'TBD';
                                                                })
                                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                                ->nullable(),
                                                        ]),
                                                ])
                                                ->columns(1)
                                                ->disabled(fn () => !$isActive || $isCompleted)
                                                ->relationship('matches', function ($query) use ($round) {
                                                    return $query->where('round', $round)
                                                        ->with(['team1.team', 'team2.team'])
                                                        ->orderBy('match_number');
                                                })
                                        ])
                                        ->collapsible()
                                        ->collapsed(fn () => !$isActive);
                                })->toArray();
                            })
                            ->visible(fn ($record) => $record?->tournament_type === 'double_elimination'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tournament_type')
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'single_elimination' => 'Single Elimination',
                        'double_elimination' => 'Double Elimination',
                        'round_robin' => 'Round Robin',
                        default => $state ?? 'Not Set',
                    })
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'single_elimination' => 'success',
                        'double_elimination' => 'warning',
                        'round_robin' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'upcoming' => 'gray',
                        'ongoing' => 'success',
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => ucfirst($state ?? 'upcoming')),
            ])
            ->actions([
                Tables\Actions\Action::make('reset_bracket')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->action(function (Competition $record) {
                        $record->matches()->delete();
                        $record->update([
                            'status' => 'upcoming',
                            'tournament_type' => null
                        ]);
                        Notification::make()
                            ->title('Bracket Reset')
                            ->body('Tournament bracket has been reset.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reset Tournament Bracket')
                    ->modalDescription('Are you sure you want to reset the bracket? This will delete all matches and their results.')
                    ->modalSubmitActionLabel('Yes, reset')
                    ->visible(fn (Competition $record): bool =>
                        $record->matches()->exists()
                    ),
                Tables\Actions\Action::make('generate_bracket')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (Competition $record) {
                        static::generateBracket($record);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Tournament Bracket')
                    ->modalDescription('Are you sure you want to generate the bracket? This will create all tournament matches.')
                    ->modalSubmitActionLabel('Yes, generate')
                    ->visible(fn (Competition $record): bool =>
                        !$record->matches()->exists()
                    ),
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, Competition $record): array {
                        foreach ($data['matches'] ?? [] as $index => $match) {
                            if (isset($match['team1_score']) && isset($match['team2_score'])) {
                                $winner_id = null;
                                if ($match['team1_score'] > $match['team2_score']) {
                                    $winner_id = $record->matches[$index]->team1_id;
                                } elseif ($match['team2_score'] > $match['team1_score']) {
                                    $winner_id = $record->matches[$index]->team2_id;
                                }

                                if ($winner_id) {
                                    $data['matches'][$index]['winner_id'] = $winner_id;
                                    static::updateNextMatch($record->matches[$index], $winner_id);
                                }
                            }
                        }
                        return $data;
                    })
                    ->after(function (Competition $record) {
                        $finalMatch = $record->matches()
                            ->where('round', 'like', '%finals%')
                            ->first();

                        if ($finalMatch && $finalMatch->winner_id) {
                            $record->update(['status' => 'completed']);
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
        ];
    }

    protected static function generateBracket(Competition $competition)
    {
        // Get approved teams
        $teams = TeamCompetition::where('competition_id', $competition->id)
            ->where('status', 'approved')
            ->get();

        if ($teams->count() < 2) {
            Notification::make()
                ->title('Not enough teams')
                ->body('You need at least 2 approved teams to generate a bracket.')
                ->danger()
                ->send();
            return;
        }

        // Automatically set tournament dates
        $competition->update([
            'tournament_start' => now(),
            'tournament_end' => now()->addDays(7), // One week tournament
        ]);

        switch ($competition->tournament_type) {
            case 'single_elimination':
                static::generateSingleElimination($competition, $teams);
                break;
            case 'double_elimination':
                static::generateDoubleElimination($competition, $teams);
                break;
            case 'round_robin':
                static::generateRoundRobin($competition, $teams);
                break;
        }

        $competition->update(['status' => 'ongoing']);

        Notification::make()
            ->title('Bracket Generated')
            ->body('Tournament bracket has been successfully generated.')
            ->success()
            ->send();
    }

    protected static function updateNextMatch($currentMatch, $winner_id)
    {
        // Get total rounds for this tournament
        $totalRounds = TournamentMatch::where('competition_id', $currentMatch->competition_id)
            ->select('round')
            ->distinct()
            ->count();

        // Get current round number
        $currentRoundNum = (int) filter_var($currentMatch->round, FILTER_SANITIZE_NUMBER_INT);
        $roundsLeft = $totalRounds - $currentRoundNum;

        // Determine next round name
        $nextRoundName = match($roundsLeft) {
            1 => 'finals',
            2 => 'semi_finals',
            3 => 'quarter_finals',
            default => "round_" . ($currentRoundNum + 1)
        };

        if (!$nextRoundName || $currentMatch->round === 'finals') {
            // If this is the finals, just update the winner
            $currentMatch->update([
                'winner_id' => $winner_id,
                'is_final_winner' => true
            ]);
            return;
        }

        // Calculate next match number
        $nextMatchNumber = ceil($currentMatch->match_number / 2);

        // Find next match
        $nextMatch = TournamentMatch::where('competition_id', $currentMatch->competition_id)
            ->where('round', $nextRoundName)
            ->where('match_number', $nextMatchNumber)
            ->first();

        if ($nextMatch) {
            // Update current match winner
            $currentMatch->update(['winner_id' => $winner_id]);

            // Update next match with winner
            if ($currentMatch->match_number % 2 == 1) {
                $nextMatch->update([
                    'team1_id' => $winner_id,
                    'team1_score' => null
                ]);
            } else {
                $nextMatch->update([
                    'team2_id' => $winner_id,
                    'team2_score' => null
                ]);
            }

            // Schedule next match if both teams are set
            if ($nextMatch->team1_id && $nextMatch->team2_id) {
                $nextMatch->update(['scheduled_at' => now()->addHours(2)]);

                $team1 = TeamCompetition::with('team')->find($nextMatch->team1_id);
                $team2 = TeamCompetition::with('team')->find($nextMatch->team2_id);

                Notification::make()
                    ->title('Next Round Match Set')
                    ->body("Match {$nextMatch->match_number}: {$team1->team->name} vs {$team2->team->name}")
                    ->success()
                    ->send();
            }
        }
    }

    protected static function updateDoubleEliminationMatch($currentMatch, $winner_id)
    {
        $loser_id = $currentMatch->team1_id === $winner_id ? $currentMatch->team2_id : $currentMatch->team1_id;

        // Update current match with winner
        $currentMatch->update(['winner_id' => $winner_id]);

        if (str_starts_with($currentMatch->round, 'upper_')) {
            // Handle Upper Bracket advancement
            $nextUpperRound = static::getNextUpperRound($currentMatch->round);
            if ($nextUpperRound) {
                $nextUpperMatch = TournamentMatch::where('competition_id', $currentMatch->competition_id)
                    ->where('round', $nextUpperRound)
                    ->where('match_number', ceil($currentMatch->match_number / 2))
                    ->first();

                if ($nextUpperMatch) {
                    if ($currentMatch->match_number % 2 == 1) {
                        $nextUpperMatch->update(['team1_id' => $winner_id, 'team1_score' => null]);
                    } else {
                        $nextUpperMatch->update(['team2_id' => $winner_id, 'team2_score' => null]);
                    }
                }
            }

            // Send loser to Lower Bracket
            $lowerRound = static::getLowerRoundForUpperLoser($currentMatch->round);
            if ($lowerRound) {
                // For Upper Round 1, we need to handle the pairing of losers differently
                if ($currentMatch->round === 'upper_round_1') {
                    // Find the corresponding Lower Round 1 match
                    $lowerMatchNumber = ceil($currentMatch->match_number / 2);
                    $lowerMatch = TournamentMatch::where('competition_id', $currentMatch->competition_id)
                        ->where('round', 'lower_round_1')
                        ->where('match_number', $lowerMatchNumber)
                        ->first();

                    if ($lowerMatch) {
                        // If this is the first half of Upper Round 1 matches, put loser in team1
                        // If this is the second half, put loser in team2
                        if ($currentMatch->match_number <= ceil($currentMatch->competition->teams()->count() / 2)) {
                            $lowerMatch->update(['team1_id' => $loser_id, 'team1_score' => null]);
                        } else {
                            $lowerMatch->update(['team2_id' => $loser_id, 'team2_score' => null]);
                        }
                    }
                } else {
                    // For other upper rounds, use standard approach
                    $lowerMatch = TournamentMatch::where('competition_id', $currentMatch->competition_id)
                        ->where('round', $lowerRound)
                        ->where('match_number', ceil($currentMatch->match_number / 2))
                        ->first();

                    if ($lowerMatch) {
                        if ($currentMatch->match_number % 2 == 1) {
                            $lowerMatch->update(['team1_id' => $loser_id, 'team1_score' => null]);
                        } else {
                            $lowerMatch->update(['team2_id' => $loser_id, 'team2_score' => null]);
                        }
                    }
                }
            }
        } else if (str_starts_with($currentMatch->round, 'lower_')) {
            // Handle Lower Bracket advancement
            $nextLowerRound = static::getNextLowerRound($currentMatch->round);
            if ($nextLowerRound) {
                $nextLowerMatch = TournamentMatch::where('competition_id', $currentMatch->competition_id)
                    ->where('round', $nextLowerRound)
                    ->where('match_number', ceil($currentMatch->match_number / 2))
                    ->first();

                if ($nextLowerMatch) {
                    if ($currentMatch->match_number % 2 == 1) {
                        $nextLowerMatch->update(['team1_id' => $winner_id, 'team1_score' => null]);
                    } else {
                        $nextLowerMatch->update(['team2_id' => $winner_id, 'team2_score' => null]);
                    }
                }
            }
        }

        // Notify about the results
        $winningTeam = TeamCompetition::with('team')->find($winner_id);
        $losingTeam = TeamCompetition::with('team')->find($loser_id);

        if (str_starts_with($currentMatch->round, 'lower_')) {
            Notification::make()
                ->title('Team Eliminated')
                ->body("{$losingTeam->team->name} has been eliminated from the tournament")
                ->warning()
                ->send();
        }

        Notification::make()
            ->title('Match Complete')
            ->body("{$winningTeam->team->name} advances to the next round")
            ->success()
            ->send();
    }

    protected static function getNextUpperRound($currentRound)
    {
        return match($currentRound) {
            'upper_round_1' => 'upper_round_2',
            'upper_round_2' => 'upper_quarter_finals',
            'upper_quarter_finals' => 'finals',
            default => null
        };
    }

    protected static function getNextLowerRound($currentRound)
    {
        return match($currentRound) {
            'lower_round_1' => 'lower_round_2',
            'lower_round_2' => 'lower_quarter_finals',
            'lower_quarter_finals' => 'finals',
            default => null
        };
    }

    protected static function getLowerRoundForUpperLoser($upperRound)
    {
        return match($upperRound) {
            'upper_round_1' => 'lower_round_1',
            'upper_round_2' => 'lower_round_2',
            'upper_quarter_finals' => 'lower_quarter_finals',
            default => null
        };
    }

    protected static function generateSingleElimination(Competition $competition, $teams)
    {
        SingleEliminationFormat::generate($competition, $teams);
    }

    protected static function completeRound(Competition $competition, string $round, ?int $numTeams = null)
    {
        if ($competition->tournament_type === 'single_elimination') {
            SingleEliminationFormat::completeRound($competition, $round, $numTeams);
        } else if ($competition->tournament_type === 'double_elimination') {
            DoubleEliminationFormat::completeRound($competition, $round, $numTeams);
        }
    }

    protected static function generateDoubleElimination(Competition $competition, $teams)
    {
        DoubleEliminationFormat::generate($competition, $teams);
    }
}
