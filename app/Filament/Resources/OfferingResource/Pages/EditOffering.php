<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOffering extends EditRecord
{
    protected static string $resource = OfferingResource::class;


    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Contoh modifikasi data sebelum tampil di form
        $data['max_budget'] = $this->record->tender->budget ?? $data['max_budget'];
        $data['quantity'] = $this->record->tender->quantity ?? $data['quantity'];
        $data['food_type'] = $this->record->tender->food_type ?? $data['food_type'];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->record->offering_status !== 'pending')
                ->disabled(fn () => $this->record->offering_status !== 'pending')
                ->tooltip(fn () => $this->record->offering_status !== 'pending' 
                    ? 'Cannot delete completed payments' 
                    : null),
        ];
    }
}
