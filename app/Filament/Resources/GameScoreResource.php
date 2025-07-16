<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameScoreResource\Pages;
use App\Models\Game;
use App\Models\GameScore;
use App\Models\Competition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Team;
use App\Models\TeamCompetition;

class GameScoreResource extends Resource
{
    protected static ?string $model = GameScore::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Game Management';
    protected static ?string $navigationLabel = 'Score Game';
    protected static ?string $modelLabel = 'Game Score';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('competition_id')
                    ->label('Tournament')
                    ->options(Competition::pluck('title', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $competition = Competition::find($state);
                            if ($competition) {
                                $set('game_id', $competition->game_id);
                            }
                        }
                    }),

                Forms\Components\Select::make('game_id')
                    ->label('Game Type')
                    ->options(Game::pluck('name', 'game_id'))
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('team_id')
                    ->label('Team')
                    ->options(function (callable $get) {
                        $competitionId = $get('competition_id');
                        if (!$competitionId) {
                            return [];
                        }

                        return TeamCompetition::where('competition_id', $competitionId)
                            ->where('status', 'approved')
                            ->with('team')
                            ->get()
                            ->pluck('team.name', 'team.id');
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $team = Team::with('players')->find($state);
                            if ($team) {
                                $set('players', $team->players);
                            }
                        }
                    }),

                Forms\Components\Repeater::make('player_scores')
                    ->schema([
                        Forms\Components\Select::make('player_id')
                            ->label('Player')
                            ->options(function (callable $get) {
                                $teamId = $get('../../team_id');
                                if (!$teamId) {
                                    return [];
                                }

                                $team = Team::with('members')->find($teamId);
                                return $team && $team->members ? $team->members->pluck('name', 'id') : [];
                            })
                            ->required(),

                        Forms\Components\TextInput::make('kills')
                            ->numeric()
                            ->label('Kills')
                            ->required(),

                        Forms\Components\TextInput::make('deaths')
                            ->numeric()
                            ->label('Deaths')
                            ->required(),

                        Forms\Components\TextInput::make('assists')
                            ->numeric()
                            ->label('Assists')
                            ->required(),
                    ])
                    ->columns(4)
                    ->label('Player Scores')
                    ->visible(fn (callable $get) => $get('team_id') !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('competition.title')
                    ->label('Tournament')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.name')
                    ->label('Game Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team.name')
                    ->label('Team')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Total Score')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('competition_id')
                    ->label('Tournament')
                    ->options(Competition::pluck('title', 'id')),
                Tables\Filters\SelectFilter::make('game_id')
                    ->label('Game Type')
                    ->options(Game::pluck('name', 'game_id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListGameScores::route('/'),
            'create' => Pages\CreateGameScore::route('/create'),
            'edit' => Pages\EditGameScore::route('/{record}/edit'),
        ];
    }
}
