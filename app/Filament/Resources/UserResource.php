<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(50),
                Select::make('role')
                    ->required()
                    ->options([
                        'admin' => 'Admin',
                        'vendor' => 'Vendor',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'vendor' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('vendor_rating')
                ->label('Vendor Rating')
                ->getStateUsing(function ($record) {
                    // Check if the user is a vendor
                    if ($record->role === 'vendor') {
                        // Assuming you have a relationship method named 'vendorRatings'
                        // This calculates the average rating
                        $avgRating = $record->vendorRatings()
                            ->selectRaw('AVG((work_quality + timelines + communication) / 3) as avg_rating')
                            ->value('avg_rating');
                        
                        return $avgRating ? number_format($avgRating, 1) : 'N/A';
                    }
                    return 'N/A';
                })
                // Optional: Add color based on rating
                ->color(function ($record) {
                    if ($record->role === 'vendor') {
                        $avgRating = $record->vendorRatings()->avg('rating');
                        
                        if ($avgRating >= 4.5) return 'success';
                        if ($avgRating >= 3.5) return 'primary';
                        if ($avgRating >= 2.5) return 'warning';
                        return 'danger';
                    }
                    return 'gray';
                }),
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
            RelationManagers\VendorCompanyRelationManager::class,
            RelationManagers\VendorBankAccountRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
