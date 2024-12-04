<?php

namespace App\Filament\Resources\TenderResource\Pages;

use App\Filament\Resources\TenderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Tender;

class CreateTender extends CreateRecord
{
    protected static string $resource = TenderResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Tender
    {
        $data['admin_id'] = Auth::id();
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
