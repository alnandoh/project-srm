<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Filament\Resources\RatingResource\RelationManagers;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->required(),
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->required(),
                Select::make('offering_id')
                    ->relationship('offering', 'title')
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

    public static function table(Table $table): Table
    {
        return $table
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
            // ->modifyQueryUsing(function (Builder $query) {
            //     $user = Auth::user();
                
            //     if ($user) {
            //         // Filter offerings by the current user's ID in the vendor_id column
            //         $query->where('vendor_id', $user->id);
            //     } else {
            //         // If no user, return no results
            //         $query->whereNull('id');
            //     }
            // })
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
