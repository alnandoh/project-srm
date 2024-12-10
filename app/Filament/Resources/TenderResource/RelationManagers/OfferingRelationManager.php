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

class OfferingRelationManager extends RelationManager
{
    protected static string $relationship = 'offering';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (TextInput $component, $state, Forms\Set $set, Forms\Get $get) {
                        $quantity = $state ?? 0;
                        $unitPrice = $get('unit_price') ?? 0;
                        $totalPrice = $quantity * $unitPrice;
                        $set('total_price', $totalPrice);
                    }),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (TextInput $component, $state, Forms\Set $set, Forms\Get $get) {
                        $unitPrice = $state ?? 0;
                        $quantity = $get('quantity') ?? 0;
                        $totalPrice = $quantity * $unitPrice;
                        $set('total_price', $totalPrice);
                    }),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->disabled(),
                FileUpload::make('image')
                    ->image()
                    ->directory('offerings'),
                Select::make('offering_status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                FileUpload::make('payment_file')
                    ->directory('payments'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('OfferingRelationManager')
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric(),
                TextColumn::make('unit_price')
                    ->money('IDR'),
                TextColumn::make('total_price')
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
                if ($user && $user->vendorCompany) {
                    // Filter offerings by the current user's vendor company
                    $query->where('vendor_id', $user->vendorCompany->id);
                } else {
                    // If no vendor company, return no results
                    $query->whereNull('id');
                }
            })
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
