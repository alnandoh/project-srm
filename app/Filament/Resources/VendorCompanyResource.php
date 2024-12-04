<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorCompanyResource\Pages;
use App\Filament\Resources\VendorCompanyResource\RelationManagers;
use App\Models\VendorCompany;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class VendorCompanyResource extends Resource
{
    protected static ?string $model = VendorCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('company_name'),
                TextInput::make('phone'),
                TextInput::make('address'),
                TextInput::make('province'),
                TextInput::make('city'),
                TextInput::make('postal_code'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorCompanies::route('/'),
            'create' => Pages\CreateVendorCompany::route('/create'),
            'edit' => Pages\EditVendorCompany::route('/{record}/edit'),
        ];
    }
}
