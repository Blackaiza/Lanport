<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameCredentialResource\Pages;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameCredentialResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Game Management';
    protected static ?string $navigationLabel = 'Game Credentials';
    protected static ?string $modelLabel = 'Game Credential';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('game_id')
                    ->options(Game::pluck('name', 'game_id'))
                    ->required()
                    ->label('Game')
                    ->disabled(),
                Forms\Components\TextInput::make('credential_database_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Credentials Database Name')
                    ->disabled(),
                Forms\Components\Repeater::make('credential_parameters')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Parameter Name'),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'string' => 'String',
                                'integer' => 'Integer',
                                'float' => 'Float',
                                'boolean' => 'Boolean',
                                'text' => 'Text',
                                'date' => 'Date',
                                'datetime' => 'DateTime',
                            ])
                            ->label('Data Type')
                            ->live(),
                        Forms\Components\TextInput::make('max_length')
                            ->numeric()
                            ->label('Max Length')
                            ->visible(fn (Forms\Get $get) => in_array($get('type'), ['string', 'text'])),
                        Forms\Components\Checkbox::make('nullable')
                            ->label('Allow Null')
                            ->default(false),
                        Forms\Components\Checkbox::make('unique')
                            ->label('Unique Value')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->minItems(1)
                    ->required()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Game Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('game_id')
                            ->label('Game ID'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Game Name'),
                        Infolists\Components\TextEntry::make('credential_database_name')
                            ->label('Credentials Database Name'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Database Structure')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('credential_parameters')
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label('Parameter Name'),
                                Infolists\Components\TextEntry::make('type')
                                    ->label('Data Type'),
                                Infolists\Components\TextEntry::make('max_length')
                                    ->label('Max Length')
                                    ->visible(fn ($record) => in_array($record->type, ['string', 'text'])),
                                Infolists\Components\IconEntry::make('nullable')
                                    ->label('Allow Null')
                                    ->boolean(),
                                Infolists\Components\IconEntry::make('unique')
                                    ->label('Unique Value')
                                    ->boolean(),
                            ])
                            ->columns(5)
                            ->label('Credential Parameters'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('game_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('credential_database_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_created')
                    ->boolean()
                    ->label('Database Created'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_records')
                    ->label('View Records')
                    ->icon('heroicon-o-table-cells')
                    ->color('info')
                    ->action(function (Game $record) {
                        try {
                            if (!$record->is_created) {
                                Notification::make()
                                    ->title('Database Not Created')
                                    ->body('The database has not been created yet.')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $tableName = $record->credential_database_name;

                            // Debug information
                            $debugInfo = [
                                'table_name' => $tableName,
                                'current_db' => DB::connection()->getDatabaseName(),
                                'connection' => DB::connection()->getConfig(),
                            ];

                            // Try to get table information
                            try {
                                $tables = DB::select('SHOW TABLES');
                                $debugInfo['all_tables'] = array_map(function($table) {
                                    return reset($table);
                                }, $tables);
                            } catch (\Exception $e) {
                                $debugInfo['tables_error'] = $e->getMessage();
                            }

                            // Try to get table structure
                            try {
                                $columns = DB::select("SHOW COLUMNS FROM {$tableName}");
                                $debugInfo['columns'] = $columns;
                            } catch (\Exception $e) {
                                $debugInfo['columns_error'] = $e->getMessage();
                            }

                            // Try to get records
                            try {
                                $records = DB::table($tableName)->get();
                                $debugInfo['record_count'] = $records->count();

                                if ($records->isEmpty()) {
                                    Notification::make()
                                        ->title('No Records Found')
                                        ->body('Debug Info: ' . json_encode($debugInfo, JSON_PRETTY_PRINT))
                                        ->info()
                                        ->send();
                                    return;
                                }

                                // Store the records in the session to display in the modal
                                session()->flash('credential_records', $records);
                                session()->flash('credential_table_name', $tableName);
                                session()->flash('credential_parameters', $record->credential_parameters);

                                Notification::make()
                                    ->title('Records Loaded')
                                    ->body('Successfully loaded ' . $records->count() . ' records.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error Querying Table')
                                    ->body('Error: ' . $e->getMessage() . "\nDebug Info: " . json_encode($debugInfo, JSON_PRETTY_PRINT))
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error Loading Records')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalContent(fn (Game $record) => view('filament.pages.credential-records', [
                        'records' => session('credential_records', []),
                        'tableName' => session('credential_table_name', ''),
                        'parameters' => session('credential_parameters', []),
                    ]))
                    ->modalHeading('Database Records')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
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
            'index' => Pages\ListGameCredentials::route('/'),
            'create' => Pages\CreateGameCredential::route('/create'),
            'edit' => Pages\EditGameCredential::route('/{record}/edit'),
            'view' => Pages\ViewGameCredential::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotNull('credential_database_name');
    }
}
