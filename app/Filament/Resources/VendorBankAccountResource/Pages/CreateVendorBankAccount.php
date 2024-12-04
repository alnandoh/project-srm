<?php

namespace App\Filament\Resources\VendorBankAccountResource\Pages;

use App\Filament\Resources\VendorBankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorBankAccount;

class CreateVendorBankAccount extends CreateRecord
{
    protected static string $resource = VendorBankAccountResource::class;

    protected static bool $canCreateAnother = false;


    protected function handleRecordCreation(array $data): VendorBankAccount
    {
        $data['vendor_id'] = Auth::id();
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
