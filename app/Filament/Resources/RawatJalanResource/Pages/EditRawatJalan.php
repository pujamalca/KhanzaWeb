<?php

namespace App\Filament\Resources\RawatJalanResource\Pages;

use App\Filament\Resources\RawatJalanResource;
use App\Models\reg_periksa;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawatJalan extends EditRecord
{
    protected static string $resource = RawatJalanResource::class;

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
