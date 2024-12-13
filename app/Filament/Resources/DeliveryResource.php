<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryResource\Pages;
use App\Filament\Resources\DeliveryResource\RelationManagers;
use App\Models\Delivery;
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

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        
        return $form
            ->schema([
                Select::make('tender_id')
                    ->relationship(
                        'tender',
                        'name',
                        function (Builder $query) use ($user) {
                            if ($user->role === 'Vendor') {
                                // Only show tenders where the vendor has accepted offerings
                                $query->whereHas('offering', function ($query) use ($user) {
                                    $query->where('vendor_id', $user->id)
                                        ->where('offering_status', 'accepted');
                                });
                            }
                        }
                    )
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(fn () => $user->role === 'Vendor' ? $user->id : null)
                    ->disabled($user->role === 'Vendor')
                    ->required(),
                TextInput::make('shipping_track_number')
                    ->required()
                    ->maxLength(255),
                Select::make('courier')
                    ->required()
                    ->options([
                        'courier1' => 'Courier 1',
                        'courier2' => 'Courier 2',
                        'courier3' => 'Courier 3',
                        'courier4' => 'Courier 4',
                    ])
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('shipping_track_number')
                    ->searchable(),
                TextColumn::make('courier'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDeliveries::route('/'),
            'create' => Pages\CreateDelivery::route('/create'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
