<?php

namespace App\Filament\Resources\VendorCompanyResource\Pages;

use App\Filament\Resources\VendorCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorCompany extends EditRecord
{
    protected static string $resource = VendorCompanyResource::class;

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
