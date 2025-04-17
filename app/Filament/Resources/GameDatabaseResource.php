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
                                            ->label('Game ID')
                                            ->helperText('Game ID must contain only lowercase letters, numbers, and underscores')
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if ($state) {
                                                    // Clean the game_id
                                                    $cleanGameId = strtolower(preg_replace('/[^a-z0-9_]/', '', $state));
                                                    $set('game_id', $cleanGameId);
                                                    $set('database_name', 'db_game_' . $cleanGameId);
                                                    $set('credential_database_name', 'game_credential_' . $cleanGameId . '_Players');
                                                }
                                            }),
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
                                            ->helperText('Automatically generated from Game ID')
                                            ->disabled(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Default Parameters')
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
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                            ->default([
                                                [
                                                    'name' => 'kills',
                                                    'type' => 'integer',
                                                    'nullable' => false,
                                                    'unique' => false,
                                                ],
                                                [
                                                    'name' => 'deaths',
                                                    'type' => 'integer',
                                                    'nullable' => false,
                                                    'unique' => false,
                                                ],
                                                [
                                                    'name' => 'assists',
                                                    'type' => 'integer',
                                                    'nullable' => false,
                                                    'unique' => false,
                                                ],
                                            ]),
                                    ])
                                    ->columns(1),
                            ]),
                        Forms\Components\Tabs\Tab::make('Game Credentials')
                            ->schema([
                                Forms\Components\Section::make('Credentials Database')
                                    ->schema([
                                        Forms\Components\TextInput::make('credential_database_name')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->label('Credentials Database Name')
                                            ->helperText('Automatically generated from Game ID')
                                            ->disabled(),
                                    ])
                                    ->columns(1),

                                Forms\Components\Section::make('Credentials Parameters')
                                    ->schema([
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
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpan('full')
                    ->persistTabInQueryString(),
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
                            // Validate database names
                            $record->validateDatabaseNames();

                            // Validate parameters
                            if (empty($record->parameters)) {
                                throw new \Exception('Please add at least one parameter in Database Configuration');
                            }

                            if (empty($record->credential_parameters)) {
                                throw new \Exception('Please add at least one parameter in Game Credentials');
                            }

                            // Log the parameters for debugging
                            \Log::info('Game Parameters: ' . json_encode($record->parameters));
                            \Log::info('Credential Parameters: ' . json_encode($record->credential_parameters));

                            // Step 1: Create the main game database (Game Information)
                            $dbName = $record->database_name;
                            $parameters = $record->parameters;
                            $gameId = str_replace('db_game_', '', $dbName);

                            // Log the database name for debugging
                            \Log::info('Creating main game database (Game Information): ' . $dbName);

                            // Generate the migration file for main game database
                            $migrationName = 'create_' . $gameId . '_game_table';
                            $migrationPath = database_path('migrations/' . date('Y_m_d_His') . '_' . $migrationName . '.php');

                            $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nclass Create" . ucfirst($gameId) . "GameTable extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('{$dbName}', function (Blueprint \$table) {\n            \$table->id();\n";
                            $migrationContent .= "            \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n";
                            $migrationContent .= "            \$table->foreignId('competition_id')->constrained()->onDelete('cascade');\n";

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

                            $migrationContent .= "            \$table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('{$dbName}');\n    }\n}";

                            file_put_contents($migrationPath, $migrationContent);

                            // Step 2: Create the credentials database (Game Credentials)
                            $credentialDbName = $record->credential_database_name;
                            $credentialParameters = $record->credential_parameters;

                            // Log the credential database name for debugging
                            \Log::info('Creating credential database (Game Credentials): ' . $credentialDbName);

                            // Generate the migration file for credentials database
                            $credentialMigrationName = 'create_' . $gameId . '_players_table';
                            $credentialMigrationPath = database_path('migrations/' . date('Y_m_d_His', strtotime('+1 second')) . '_' . $credentialMigrationName . '.php');

                            $credentialMigrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nclass Create" . ucfirst($gameId) . "PlayersTable extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('{$credentialDbName}', function (Blueprint \$table) {\n            \$table->id();\n";
                            $credentialMigrationContent .= "            \$table->foreignId('user_id')->constrained()->onDelete('cascade');\n";

                            foreach ($credentialParameters as $param) {
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

                                $credentialMigrationContent .= $column;
                            }

                            $credentialMigrationContent .= "            \$table->timestamps();\n        });\n    }\n\n    public function down(): void\n    {\n        Schema::dropIfExists('{$credentialDbName}');\n    }\n}";

                            file_put_contents($credentialMigrationPath, $credentialMigrationContent);

                            // Run the migrations in sequence
                            \Artisan::call('migrate');

                            $record->update(['is_created' => true]);

                            Notification::make()
                                ->title('Databases Created Successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Log::error('Error creating databases: ' . $e->getMessage());
                            Notification::make()
                                ->title('Error Creating Databases')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Create Databases')
                    ->modalDescription('Are you sure you want to create these databases? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, create databases')
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
