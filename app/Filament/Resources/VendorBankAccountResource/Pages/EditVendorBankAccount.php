<?php

namespace App\Filament\Resources\VendorBankAccountResource\Pages;

use App\Filament\Resources\VendorBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorBankAccount extends EditRecord
{
    protected static string $resource = VendorBankAccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
