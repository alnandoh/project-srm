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

class VendorCompanyRelationManager extends RelationManager
{
    protected static string $relationship = 'vendorCompany';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('company_name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('phone')
                    ->required()
                    ->maxLength(100),
                TextInput::make('address')
                    ->required()
                    ->maxLength(100),
                TextInput::make('province')
                    ->required()
                    ->maxLength(100),
                TextInput::make('city')
                    ->required()
                    ->maxLength(100),
                TextInput::make('postal_code')
                    ->required()
                    ->maxLength(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('VendorCompanyRelationManager')
            ->columns([
                TextColumn::make('company_name'),
                TextColumn::make('phone'),
                TextColumn::make('address'),
                TextColumn::make('province'),
                TextColumn::make('city'),
                TextColumn::make('postal_code'),
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
