<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Offering;

class CreateOffering extends CreateRecord
{
    protected static string $resource = OfferingResource::class;
    
    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Offering
    {
        $data['vendor_id'] = Auth::id();
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
