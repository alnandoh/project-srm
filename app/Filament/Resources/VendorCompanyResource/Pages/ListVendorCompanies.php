<?php

namespace App\Filament\Resources\VendorCompanyResource\Pages;

use App\Filament\Resources\VendorCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorCompanies extends ListRecords
{
    protected static string $resource = VendorCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
