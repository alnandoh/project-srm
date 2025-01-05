<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->record->payment_status === true)
                ->disabled(fn () => $this->record->payment_status === true)
                ->tooltip(fn () => $this->record->payment_status === true 
                    ? 'Cannot delete completed payments' 
                    : null),
        ];
    }

    public function getSubheading(): ?string
    {
        if ($this->record->payment_status === true) {
            return 'This payment has been completed and cannot be modified.';
        }

        return null;
    }

    public function beforeSave(): void
    {
        if ($this->record->payment_status === true) {
            Notification::make()
                ->danger()
                ->title('This payment has been completed and cannot be modified.')
                ->send();

            $this->halt();
        }
    }

    protected function configureForm(Form $form): Form
    {
        return parent::configureForm($form)
            ->disabled($this->record->payment_status === true);
    }
}