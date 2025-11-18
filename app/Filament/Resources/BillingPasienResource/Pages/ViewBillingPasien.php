<?php

namespace App\Filament\Resources\BillingPasienResource\Pages;

use App\Filament\Resources\BillingPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBillingPasien extends ViewRecord
{
    protected static string $resource = BillingPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('printInvoice')
                ->label('Print Invoice')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn () => route('print.invoice', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\Action::make('addPayment')
                ->label('Tambah Pembayaran')
                ->icon('heroicon-o-currency-dollar')
                ->color('success')
                ->url(fn () =>
                    route('filament.superadmin.resources.pembayaran-pasiens.create', ['billing_id' => $this->record->id])
                )
                ->visible(fn () => $this->record->sisa_tagihan > 0),
        ];
    }
}
