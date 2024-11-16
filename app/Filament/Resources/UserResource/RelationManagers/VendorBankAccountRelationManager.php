<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class VendorBankAccountRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorBankAccount';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('bank_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('account_number')
                    ->required()
                    ->numeric()
                    ->maxLength(50),
                TextInput::make('account_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('branch')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('VendorBankAccountRelationManager')
            ->columns([
                TextColumn::make('bank_name'),
                TextColumn::make('account_number'),
                TextColumn::make('account_name'),
                TextColumn::make('branch'),            
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
