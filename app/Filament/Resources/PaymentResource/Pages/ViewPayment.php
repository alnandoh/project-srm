<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_rating')
                ->label('Create Rating')
                ->color('primary')
                ->icon('heroicon-o-star')
                ->url(fn () => route('filament.admin.resources.ratings.create', [
                    'payment_id' => $this->record->id,
                    'tender_id' => $this->record->tender_id,
                    'vendor_id' => $this->record->vendor_id
                ]))
                ->visible(fn () => 
                    Auth::user()->role === 'Admin' && 
                    $this->record->status === 'accepted' &&
                    !$this->record->rating()->exists()
                ),
        ];
    }
} 