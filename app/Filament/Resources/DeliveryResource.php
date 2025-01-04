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
use Filament\Forms\Components\Hidden;
use App\Filament\Resources\DeliveryResource\Pages\ViewDelivery;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    
    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $tenderId = request()->query('tender_id');
        $isEdit = $form->getOperation() === 'edit';
        
        return $form
            ->schema([
                Select::make('tender_id')
                    ->relationship(
                        'tender',
                        'name',
                        // function (Builder $query) use ($user) {
                        //     if ($user->role === 'Vendor') {
                        //         $query->whereHas('offering', function ($query) use ($user) {
                        //             $query->where('vendor_id', $user->id)
                        //                 ->where('offering_status', 'accepted')
                        //                 ->where(function ($q) {
                        //                     $q->where('dp_paid', true)
                        //                       ->orWhere('full_paid', true);
                        //                 });
                        //         });
                        //     }
                        // }
                    )
                    ->default($tenderId)
                    ->required()
                    ->disabled(fn () => $tenderId !== null || $isEdit),
                    // ->afterStateUpdated(function ($state, callable $set) {
                    //     if ($state) {
                    //         $offering = \App\Models\Offering::where('tender_id', $state)
                    //             ->where('vendor_id', auth()->id())
                    //             ->where('offering_status', 'accepted')
                    //             ->first();
                            
                    //         if ($offering) {
                    //             $set('offering_id', $offering->id);
                    //         }
                    //     }
                    // }),

                Hidden::make('offering_id'),

                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(fn () => $user->role === 'Vendor' ? $user->id : null)
                    ->required()
                    ->disabled(fn () => $user->role === 'Vendor' || $isEdit),
                TextInput::make('shipping_track_number')
                    ->required()
                    ->disabled($user->role === 'Admin')
                    ->maxLength(255),
                Select::make('courier')
                    ->options([
                        'JNE' => 'JNE',
                        'JNT' => 'J&T',
                        'SICEPAT' => 'SiCepat',
                        'ANTERAJA' => 'AnterAja',
                        'POS' => 'POS Indonesia',
                    ])
                    ->searchable()
                    ->required()
                    ->disabled(fn () => $isEdit || $user->role === 'Admin'),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                    ])
                    ->default('shipped')
                    ->required()
                    ->dehydrated(true)
                    ->visible($user->role === 'Admin')
                    ->disabled($user->role !== 'Admin'),

                TextInput::make('quantity_received')
                    ->numeric()
                    ->visible($user->role === 'Admin'),

                Toggle::make('quality_check')
                    ->visible($user->role === 'Admin')
                    ->reactive(),

                Toggle::make('quantity_check')
                    ->visible($user->role === 'Admin'),

                Textarea::make('qc_notes')
                    ->visible($user->role === 'Admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('shipping_track_number')
                    ->searchable(),
                TextColumn::make('courier')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'info',
                        'shipped' => 'gray',
                        'delivered' => 'success',
                        default => 'warning',
                    }),
                TextColumn::make('courier')
                    ->searchable(),
                TextColumn::make('quantity_received')
                ->hidden($user->role !== 'Admin'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->role === 'Vendor') {
                    $query->where('vendor_id', $user->id);
                }
            })
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Delivery $record) => $record->status !== "delivered"),
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
            'view' => Pages\ViewDelivery::route('/{record}'),
            'edit' => Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
