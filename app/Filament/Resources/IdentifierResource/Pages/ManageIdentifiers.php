<?php

namespace App\Filament\Resources\IdentifierResource\Pages;

use App\Filament\Resources\IdentifierResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIdentifiers extends ManageRecords
{
    protected static string $resource = IdentifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
