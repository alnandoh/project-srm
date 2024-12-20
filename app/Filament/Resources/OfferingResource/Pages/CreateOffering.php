<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Offering;
use App\Models\Tender;


class CreateOffering extends CreateRecord
{
    protected static string $resource = OfferingResource::class;
    
    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        parent::mount();

        // Get tender_id from query parameters
        $tenderId = request()->query('tender_id');
        if ($tenderId) {
            $tender = Tender::find($tenderId);
            if ($tender) {
                // Set the max_budget when mounting the form
                $this->form->fill([
                    'tender_id' => $tenderId,
                    'max_budget' => $tender->budget,
                ]);
            }
        }
    }

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
