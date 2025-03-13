<?php

namespace App\Filament\Resources\UgdResource\Pages;

use App\Filament\Resources\UgdResource;
use App\Models\reg_periksa;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUgd extends EditRecord
{
    protected static string $resource = UgdResource::class;

    public function mount(string|int $record): void
    {
        $decodedKey = str_replace('-', '/', (string) $record); // Pastikan $record string
        $this->record = reg_periksa::where('no_rawat', $decodedKey)->firstOrFail();
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
