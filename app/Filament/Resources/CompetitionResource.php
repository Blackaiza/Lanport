<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetitionResource\Pages;
use App\Filament\Resources\CompetitionResource\RelationManagers;
use App\Models\Competition;
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
                        Forms\Components\TextInput::make('team_count')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->label('Maximum Number of Teams')
                            ->helperText('Enter the maximum number of teams that can participate'),
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