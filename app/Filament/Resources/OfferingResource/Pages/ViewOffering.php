<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ViewOffering extends ViewRecord
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_delivery')
                ->label('Create Delivery')
                ->color('primary')
                ->icon('heroicon-o-truck')
                ->url(fn () => route('filament.admin.resources.deliveries.create', [
                    'tender_id' => $this->record->tender_id,
                    'vendor_id' => $this->record->vendor_id
                ]))
                ->visible(fn () => 
                    Auth::user()->role === 'Vendor' && 
                    $this->record->offering_status === 'accepted' &&
                    $this->record->vendor_id === Auth::id() &&
                    !$this->record->delivery()->exists()
                ),
        ];
    }
} 