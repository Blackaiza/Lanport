<?php

namespace App\Filament\Resources\GameDatabaseResource\Pages;

use App\Filament\Resources\GameDatabaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGameDatabase extends CreateRecord
{
    protected static string $resource = GameDatabaseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Clean the game_id to ensure it only contains valid characters
        $gameId = strtolower(preg_replace('/[^a-z0-9_]/', '', $data['game_id']));

        // Generate database names
        $databaseName = 'db_game_' . $gameId;
        $credentialDatabaseName = 'game_credential_' . $gameId . '_Players';

        // Log the generated names and parameters for debugging
        \Log::info('Generated database name: ' . $databaseName);
        \Log::info('Generated credential database name: ' . $credentialDatabaseName);
        \Log::info('Game Parameters: ' . json_encode($data['parameters'] ?? []));
        \Log::info('Credential Parameters: ' . json_encode($data['credential_parameters'] ?? []));

        return [
            'game_id' => $gameId,
            'name' => $data['name'],
            'picture' => $data['picture'] ?? null,
            'database_name' => $databaseName,
            'credential_database_name' => $credentialDatabaseName,
            'parameters' => $data['parameters'] ?? [],
            'credential_parameters' => $data['credential_parameters'] ?? [],
            'is_created' => false,
        ];
    }
}
