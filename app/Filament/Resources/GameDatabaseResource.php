<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameDatabaseResource\Pages;
use App\Filament\Resources\GameDatabaseResource\RelationManagers;
use App\Models\Game;
use App\Models\DatabaseGame;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class GameDatabaseResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Game Management';
    protected static ?string $navigationLabel = 'Create Game Database';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Game Creation')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Game Information')
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('game_id')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->label('Game ID'),
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Game Name'),
                                        Forms\Components\FileUpload::make('picture')
                                            ->image()
                                            ->directory('games')
                                            ->label('Game Picture')
                                            ->columnSpan('full'),
                                    ])
                                    ->columns(2),
                            ]),
                        Forms\Components\Tabs\Tab::make('Database Configuration')
                            ->schema([
                                Forms\Components\Section::make('Database Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('database_name')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->label('Database Name')
                                            ->helperText('Must start with db_game_ and contain only lowercase letters, numbers, and underscores')
                                            ->disabled(fn ($record) => $record?->is_created ?? false),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Parameters')
                                    ->schema([
                                        Forms\Components\Repeater::make('parameters')
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
                                                    ->label('Data Type'),
                                                Forms\Components\TextInput::make('max_length')
                                                    ->numeric()
                                                    ->label('Max Length')
                                                    ->visible(fn ($get) => in_array($get('type'), ['string', 'text'])),
                                                Forms\Components\Checkbox::make('nullable')
                                                    ->label('Allow Null')
                                                    ->default(false),
                                                Forms\Components\Checkbox::make('unique')
                                                    ->label('Unique Value')
                                                    ->default(false),
                                            ])
                                            ->columns(2)
                                            ->minItems(1)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpan('full'),
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
                Tables\Columns\ImageColumn::make('picture')
                    ->circular(),
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
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => !$record->is_created),
                Tables\Actions\Action::make('create_database')
                    ->action(function ($record) {
                        try {
                            // Create the database
                            $dbName = $record->database_name;
                            $parameters = $record->parameters;

                            // Generate the migration file
                            $migrationName = 'create_' . str_replace('db_game_', '', $dbName) . '_table';
                            $migrationPath = database_path('migrations/' . date('Y_m_d_His') . '_' . $migrationName . '.php');

                            $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('{$dbName}', function (Blueprint \$table) {\n            \$table->id();\n";

                            foreach ($parameters as $param) {
                                $type = $param['type'];
                                $name = $param['name'];
                                $maxLength = $param['max_length'] ?? null;
                                $nullable = $param['nullable'] ?? false;
                                $unique = $param['unique'] ?? false;

                                $column = "\$table->{$type}('{$name}')";
                                if ($maxLength && in_array($type, ['string', 'text'])) {
                                    $column .= "->length({$maxLength})";
                                }
                                if ($nullable) {
                                    $column .= '->nullable()';
                                }
                                if ($unique) {
                                    $column .= '->unique()';
                                }
                                $column .= ";\n";

                                $migrationContent .= $column;
                            }

                            $migrationContent .= "            \$table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('{$dbName}');\n    }\n};";

                            file_put_contents($migrationPath, $migrationContent);

                            // Run the migration
                            \Artisan::call('migrate');

                            $record->update(['is_created' => true]);

                            Notification::make()
                                ->title('Database Created Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error Creating Database')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Create Database')
                    ->modalDescription('Are you sure you want to create this database? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, create database')
                    ->visible(fn ($record) => !$record->is_created)
                    ->icon('heroicon-o-server')
                    ->color('success'),
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
            'index' => Pages\ListGameDatabases::route('/'),
            'create' => Pages\CreateGameDatabase::route('/create'),
            'edit' => Pages\EditGameDatabase::route('/{record}/edit'),
        ];
    }
}
