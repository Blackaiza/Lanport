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
                Forms\Components\Select::make('game_id')
                    ->relationship('game', 'name')
                    ->required()
                    ->label('Game'),
                Forms\Components\Select::make('competition_id')
                    ->relationship('competition', 'title')
                    ->required()
                    ->label('Competition'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name'),
                Forms\Components\Select::make('best_of')
                    ->options([
                        1 => 'B01',
                        2 => 'B02',
                        3 => 'B03',
                        5 => 'B05',
                        7 => 'B07',
                    ])
                    ->required()
                    ->label('Best Of')
                    ->live(),
                Forms\Components\TextInput::make('max_round')
                    ->disabled()
                    ->label('Max Round'),
                Forms\Components\TextInput::make('winner')
                    ->disabled()
                    ->label('Winner (Rounds to Win)'),
                Forms\Components\Select::make('condition')
                    ->options([
                        'win' => 'Win',
                        'draw' => 'Draw',
                        'lose' => 'Lose',
                    ])
                    ->required()
                    ->label('Condition'),
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->required()
                    ->label('Score'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('game.name')
                    ->searchable()
                    ->sortable()
                    ->label('Game'),
                Tables\Columns\TextColumn::make('competition.title')
                    ->searchable()
                    ->sortable()
                    ->label('Competition'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('best_of')
                    ->formatStateUsing(fn ($state) => 'B' . str_pad($state, 2, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable()
                    ->label('Best Of'),
                Tables\Columns\TextColumn::make('max_round')
                    ->searchable()
                    ->sortable()
                    ->label('Max Round'),
                Tables\Columns\TextColumn::make('winner')
                    ->searchable()
                    ->sortable()
                    ->label('Winner'),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'win' => 'success',
                        'draw' => 'warning',
                        'lose' => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
