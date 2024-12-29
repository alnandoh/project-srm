<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->hidden(),
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->default(request()->query('tender_id'))
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(request()->query('vendor_id'))
                    ->required(),
                FileUpload::make('invoice_image')
                    ->image()
                    ->directory('invoices')
                    ->disk('public')
                    ->storeFiles(true),
                Toggle::make('payment_status')
                    ->required(),
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
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
