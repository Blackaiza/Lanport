<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionResource\Pages;
use App\Filament\Resources\CompetitionResource\RelationManagers;
use App\Models\Competition;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Register Competition';

    protected static ?string $navigationGroup = 'Competition Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\RichEditor::make('description')
                            ->nullable()
                            ->columnSpan('full'),
                        Forms\Components\FileUpload::make('picture')
                            ->image()
                            ->directory('competitions')
                            ->nullable()
                            ->columnSpan('full'),
                        Forms\Components\Select::make('game_id')
                            ->relationship('game', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Game')
                            ->columnSpan('full')
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $game = \App\Models\Game::find($state);
                                    if ($game && $game->credential_database_name) {
                                        $set('require_player_id', true);
                                    }
                                }
                            }),
                        Forms\Components\Toggle::make('require_player_id')
                            ->label('Require Game Player ID')
                            ->helperText('Players must provide their in-game ID to participate')
                            ->disabled(fn (Forms\Get $get) => !$get('game_id'))
                            ->columnSpan('full'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Social Media Links')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp_link')
                            ->url()
                            ->nullable()
                            ->label('WhatsApp Group Link'),
                        Forms\Components\TextInput::make('telegram_link')
                            ->url()
                            ->nullable()
                            ->label('Telegram Group Link'),
                        Forms\Components\TextInput::make('discord_link')
                            ->url()
                            ->nullable()
                            ->label('Discord Server Link'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Tournament Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('registration_start')
                            ->required()
                            ->label('Registration Opens'),
                        Forms\Components\DateTimePicker::make('registration_end')
                            ->required()
                            ->after('registration_start')
                            ->label('Registration Closes'),
                        Forms\Components\DateTimePicker::make('tournament_start')
                            ->required()
                            ->after('registration_end')
                            ->label('Tournament Begins'),
                        Forms\Components\DateTimePicker::make('tournament_end')
                            ->required()
                            ->after('tournament_start')
                            ->label('Tournament Ends'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Team Settings')
                    ->schema([
                        Forms\Components\Select::make('team_count')
                            ->required()
                            ->options([
                                2 => '2 Teams',
                                4 => '4 Teams',
                                8 => '8 Teams',
                                16 => '16 Teams',
                                32 => '32 Teams',
                                64 => '64 Teams',
                                128 => '128 Teams',
                            ])
                            ->label('Maximum Number of Teams')
                            ->helperText('Select the maximum number of teams that can participate (must be a power of 2)'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Prize Pool')
                    ->schema([
                        Forms\Components\Repeater::make('prize_pool')
                            ->schema([
                                Forms\Components\Select::make('position')
                                    ->required()
                                    ->options([
                                        1 => 'Winner',
                                        2 => 'Second Place',
                                        3 => 'Third Place',
                                        4 => 'Fourth Place',
                                        5 => 'Fifth Place',
                                        6 => 'Sixth Place',
                                        7 => 'Seventh Place',
                                        8 => 'Eighth Place',
                                        9 => 'Ninth Place',
                                        10 => 'Tenth Place',
                                    ])
                                    ->label('Position'),
                                Forms\Components\CheckboxList::make('prize_types')
                                    ->options([
                                        'money' => 'Money (RM)',
                                        'diamond' => 'Diamond',
                                        'hamper' => 'Hamper',
                                        'other' => 'Other',
                                    ])
                                    ->required()
                                    ->label('Prize Types')
                                    ->live(),
                                Forms\Components\TextInput::make('money_amount')
                                    ->label('Money Amount (RM)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn (Forms\Get $get) => in_array('money', $get('prize_types') ?? []))
                                    ->required(fn (Forms\Get $get) => in_array('money', $get('prize_types') ?? [])),
                                Forms\Components\TextInput::make('diamond_amount')
                                    ->label('Diamond Amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->visible(fn (Forms\Get $get) => in_array('diamond', $get('prize_types') ?? []))
                                    ->required(fn (Forms\Get $get) => in_array('diamond', $get('prize_types') ?? [])),
                                Forms\Components\TextInput::make('hamper_description')
                                    ->label('Hamper Description')
                                    ->visible(fn (Forms\Get $get) => in_array('hamper', $get('prize_types') ?? []))
                                    ->required(fn (Forms\Get $get) => in_array('hamper', $get('prize_types') ?? [])),
                                Forms\Components\TextInput::make('other_prize')
                                    ->label('Other Prize Description')
                                    ->visible(fn (Forms\Get $get) => in_array('other', $get('prize_types') ?? []))
                                    ->required(fn (Forms\Get $get) => in_array('other', $get('prize_types') ?? [])),
                            ])
                            ->defaultItems(3)
                            ->minItems(1)
                            ->maxItems(10)
                            ->columnSpan('full')
                            ->label('Prize Distribution'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('game.name')
                    ->searchable()
                    ->label('Game'),
                Tables\Columns\TextColumn::make('description')
                    ->html()
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('picture'),
                Tables\Columns\TextColumn::make('registration_start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tournament_start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tournament_end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_count')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCompetitions::route('/'),
            'create' => Pages\CreateCompetition::route('/create'),
            'edit' => Pages\EditCompetition::route('/{record}/edit'),
        ];
    }
}