<?php

namespace App\Filament\Resources\TenderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use App\Models\Offering;

class OfferingRelationManager extends RelationManager
{
    protected static string $relationship = 'offering';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('OfferingRelationManager')
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor Name')
                    ->getStateUsing(fn ($record) => $record->vendor->name)
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('offer')
                    ->money('IDR'),
                TextColumn::make('offering_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
            ])
             ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->role === 'Vendor') {
                    $query->where('vendor_id', $user->id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $offerings = Offering::where('tender_id', $record->tender_id)->get();

                        \DB::transaction(function () use ($record, $offerings) {
                            $record->update(['offering_status' => 'accepted']);

                            foreach ($offerings as $offering) {
                                if ($offering->id !== $record->id) {
                                    $offering->update(['offering_status' => 'cancelled']);
                                }
                            }
                        });
                    })
                    ->visible(fn ($record) => $record->offering_status === 'pending'),
                Action::make('cancel')
                    ->label('Cancel')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['offering_status' => 'cancelled']);
                    })
                    ->visible(fn ($record) => $record->offering_status === 'pending'),            
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
