<?php

namespace App\Filament\Resources\VendorBankAccountResource\Pages;

use App\Filament\Resources\VendorBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorBankAccounts extends ListRecords
{
    protected static string $resource = VendorBankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
