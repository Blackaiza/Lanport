<?php

namespace App\Filament\Resources\GameDatabaseResource\Pages;

use App\Filament\Resources\GameDatabaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameDatabase extends EditRecord
{
    protected static string $resource = GameDatabaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Generate database names from game_id
        $gameId = strtolower($data['game_id']);
        $databaseName = 'db_game_' . $gameId;
        $credentialDatabaseName = 'game_credential_' . $gameId . '_Players';

        // Only save the game information
        return [
            'game_id' => $data['game_id'],
            'name' => $data['name'],
            'picture' => $data['picture'] ?? null,
            'database_name' => $databaseName,
            'credential_database_name' => $credentialDatabaseName,
            'parameters' => $this->record->parameters ?? [],
            'credential_parameters' => $this->record->credential_parameters ?? [],
            'is_created' => $this->record->is_created ?? false,
        ];
    }
}
