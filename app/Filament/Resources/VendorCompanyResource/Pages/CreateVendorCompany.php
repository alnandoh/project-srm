<?php

namespace App\Filament\Resources\VendorCompanyResource\Pages;

use App\Filament\Resources\VendorCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorCompany;

class CreateVendorCompany extends CreateRecord
{
    protected static string $resource = VendorCompanyResource::class;
    
    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): VendorCompany
    {
        $data['vendor_id'] = Auth::id();
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
