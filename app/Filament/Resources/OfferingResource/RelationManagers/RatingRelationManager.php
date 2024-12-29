<?php

namespace App\Filament\Resources\OfferingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class RatingRelationManager extends RelationManager
{
    protected static string $relationship = 'rating';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(request()->query('vendor_id'))
                    ->required(),
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->default(request()->query('tender_id'))
                    ->required(),
                Select::make('offering_id')
                    ->relationship('offering', 'title')
                    ->default(request()->query('offering_id'))
                    ->required(),
                TextInput::make('work_quality')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                TextInput::make('timelines')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
                TextInput::make('communication')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('RatingRelationManager')
            ->columns([
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('offering.title')
                    ->searchable(),
                TextColumn::make('work_quality')
                    ->sortable(),
                TextColumn::make('timelines')
                    ->sortable(),
                TextColumn::make('communication')
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
