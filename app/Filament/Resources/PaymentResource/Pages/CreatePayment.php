<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Offering;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
    protected static bool $canCreateAnother = false;
    
    protected function beforeFill(): void 
    {
        $tenderId = request()->query('tender_id');
        $vendorId = request()->query('vendor_id');

        Log::info('CreatePayment beforeFill', [
            'tender_id' => $tenderId,
            'vendor_id' => $vendorId
        ]);

        if (!$tenderId || !$vendorId) {
            Notification::make()
                ->title('Error')
                ->body('Missing required parameters.')
                ->danger()
                ->send();

            $this->redirect(static::getResource()::getUrl('index'));
            return;
        }

        // Pre-fill the form data
        $this->data['tender_id'] = $tenderId;
        $this->data['vendor_id'] = $vendorId;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Use form data instead of query parameters
        $tenderId = $data['tender_id'];
        $vendorId = $data['vendor_id'];

        Log::info('CreatePayment mutateFormDataBeforeCreate', [
            'tender_id' => $tenderId,
            'vendor_id' => $vendorId,
            'form_data' => $data
        ]);

        // Find the related offering
        $offering = Offering::where('tender_id', $tenderId)
            ->where('vendor_id', $vendorId)
            ->where('offering_status', 'accepted')
            ->first();

        if ($offering) {
            Log::info('Offering found', [
                'offering_id' => $offering->id,
                'status' => $offering->offering_status,
                'total_amount' => $offering->total_amount
            ]);

            return [
                'admin_id' => Auth::id(),
                'tender_id' => $tenderId,
                'vendor_id' => $vendorId,
                'amount' => $offering->offer + $offering->delivery_cost,
                'payment_type' => $offering->payment_type,
                'dp_amount' => $offering->dp_amount ?? 0,
                'payment_status' => false,
                'invoice_image' => $data['invoice_image'] ?? null
            ];
        }

        Log::error('No valid offering found', [
            'tender_id' => $tenderId,
            'vendor_id' => $vendorId
        ]);

        Notification::make()
            ->title('Error')
            ->body('No valid offering found for this tender and vendor.')
            ->danger()
            ->persistent()
            ->send();

        $this->redirect(static::getResource()::getUrl('index'));
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}