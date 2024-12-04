<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorBankAccountResource\Pages;
use App\Filament\Resources\VendorBankAccountResource\RelationManagers;
use App\Models\VendorBankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class VendorBankAccountResource extends Resource
{
    protected static ?string $model = VendorBankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Bank Accounts';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name'),
                TextColumn::make('account_number'),
                TextColumn::make('account_name'),
                TextColumn::make('branch'),
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
            'index' => Pages\ListVendorBankAccounts::route('/'),
            'create' => Pages\CreateVendorBankAccount::route('/create'),
            'edit' => Pages\EditVendorBankAccount::route('/{record}/edit'),
        ];
    }
}
