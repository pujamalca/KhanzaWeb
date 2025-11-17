<?php

namespace App\Filament\Resources\DetailPemberianObatResource\Pages;

use App\Filament\Resources\DetailPemberianObatResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDetailPemberianObat extends CreateRecord
{
    protected static string $resource = DetailPemberianObatResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tgl_perawatan'] = $data['tgl_perawatan'] ?? now()->toDateString();
        $data['jam'] = $data['jam'] ?? now()->format('H:i:s');

        // Calculate total if not set
        if (isset($data['kode_brng']) && isset($data['jml'])) {
            $item = \App\Models\Databarang::find($data['kode_brng']);
            if ($item) {
                $subtotal = $data['jml'] * $item->ralan;
                $data['total'] = $subtotal + ($data['embalase'] ?? 0) + ($data['tuslah'] ?? 0);
            }
        }

        return $data;
    }
}
