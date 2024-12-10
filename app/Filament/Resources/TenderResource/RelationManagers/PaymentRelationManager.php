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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required(),
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                FileUpload::make('invoice_image')
                    ->image()
                    ->directory('invoices')
                    ->disk('public')
                    ->storeFiles(true),
                Toggle::make('payment_status')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('PaymentRelationManager')
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('amount'),
                ImageColumn::make('invoice_image')
                    ->getStateUsing(fn ($record) => $record->invoice_image)
                    ->url(fn ($record) => $record->invoice_image 
                        ? Storage::url($record->invoice_image) 
                        : null)
                    ->width(100)
                    ->height(100),
                IconColumn::make('payment_status')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
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
