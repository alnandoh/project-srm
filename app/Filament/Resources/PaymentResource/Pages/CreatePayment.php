<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Payment
    {
        $data['admin_id'] = Auth::id();
        $data['amount'] = 0;
        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
