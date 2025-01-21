<?php

namespace App\Filament\Resources\TrackersqlResource\Pages;

use App\Filament\Resources\TrackersqlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrackersql extends EditRecord
{
    protected static string $resource = TrackersqlResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
