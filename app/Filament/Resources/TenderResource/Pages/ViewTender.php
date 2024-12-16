<?php

namespace App\Filament\Resources\TenderResource\Pages;

use App\Filament\Resources\TenderResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ViewTender extends ViewRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_offer')
                ->label('Create Offer')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->url(fn () => route('filament.admin.resources.offerings.create', ['tender_id' => $this->record->id]))
                ->visible(fn () => Auth::user()->role === 'Vendor'),
        ];
    }
}