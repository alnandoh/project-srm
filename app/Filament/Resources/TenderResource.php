<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenderResource\Pages;
use App\Filament\Resources\TenderResource\RelationManagers;
use App\Models\Tender;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;

class TenderResource extends Resource
{
    protected static ?string $model = Tender::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('special_preference')
                    ->maxLength(255),
                TextInput::make('food_type')
                    ->required()
                    ->maxLength(100),
                TextInput::make('budget')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),
                Textarea::make('note')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('end_registration')
                    ->required(),
                DateTimePicker::make('delivery_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('food_type')
                    ->searchable(),
                TextColumn::make('budget')
                    ->money()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_registration')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delivery_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
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
            RelationManagers\OfferingRelationManager::class,
            RelationManagers\DeliveryRelationManager::class,
            RelationManagers\PaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
        ];
    }
}
