<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ViewDelivery extends ViewRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_payment')
                ->label('Create Payment')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar')
                ->url(fn () => route('filament.admin.resources.payments.create', [
                    'delivery_id' => $this->record->id,
                    'tender_id' => $this->record->tender_id,
                    'vendor_id' => $this->record->vendor_id,
                ]))
                ->visible(fn () => 
                    Auth::user()->role === 'Admin' &&
                    !$this->record->payment()->exists()
                )
                ->hidden(),
        ];
    }
} 