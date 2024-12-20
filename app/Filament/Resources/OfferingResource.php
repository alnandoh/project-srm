<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OfferingResource\Pages;
use App\Filament\Resources\OfferingResource\RelationManagers;
use App\Models\Offering;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Get;
use Filament\Tables\Actions\Action;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        if ($user->role === 'Admin') {
            // Admin can only update status
            return $form->schema([
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->disabled(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->disabled(),
                TextInput::make('title')
                    ->disabled(),
                Textarea::make('description')
                    ->disabled(),
                TextInput::make('offer')
                    ->disabled(),
                FileUpload::make('image')                    
                    ->disabled(),
                Select::make('offering_status')
                    ->options([
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
        }
        
        return $form
            ->schema([
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->default(request()->query('tender_id'))
                    ->required()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $tender = \App\Models\Tender::find($state);
                            if ($tender) {
                                $set('max_budget', $tender->budget);
                            }
                        }
                    }),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->hidden(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(255)
                    ->required(),
                // TextInput::make('quantity')
                //     ->required()
                //     ->numeric()
                //     ->live(onBlur: true)
                //     ->afterStateUpdated(function (TextInput $component, $state, Forms\Set $set, Forms\Get $get) {
                //         $quantity = $state ?? 0;
                //         $unitPrice = $get('unit_price') ?? 0;
                //         $totalPrice = $quantity * $unitPrice;
                //         $set('total_price', $totalPrice);
                //     }),
                // TextInput::make('unit_price')
                //     ->required()
                //     ->numeric()
                //     ->prefix('IDR')
                //     ->live(onBlur: true)
                //     ->afterStateUpdated(function (TextInput $component, $state, Forms\Set $set, Forms\Get $get) {
                //         $unitPrice = $state ?? 0;
                //         $quantity = $get('quantity') ?? 0;
                //         $totalPrice = $quantity * $unitPrice;
                //         $set('total_price', $totalPrice);
                //     }),
                // TextInput::make('total_price')
                //     ->required()
                //     ->numeric()
                //     ->prefix('IDR')
                //     ->disabled(),
                TextInput::make('offer')
                    ->required()
                    ->numeric()
                    ->rules([
                        function (Forms\Get $get) {
                            return function ($attribute, $value, $fail) use ($get) {
                                $maxBudget = $get('max_budget');
                                if ($value > $maxBudget) {
                                    $fail("The offer cannot exceed the tender budget of IDR " . number_format($maxBudget, 2));
                                }
                            };
                        }
                    ])
                    ->prefix('IDR'),
                Hidden::make('max_budget'),
                FileUpload::make('image')
                    ->image()
                    ->directory('offerings'),
                Select::make('offering_status')
                    ->options([
                        'pending' => 'Pending',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->hidden()
                    ->required(),
                // FileUpload::make('payment_file')
                //     ->directory('payments'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tender.name')
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->label('Vendor Name')
                    ->getStateUsing(fn ($record) => $record->vendor->name)
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                // TextColumn::make('quantity')
                //     ->numeric(),
                // TextColumn::make('unit_price')
                //     ->money('IDR'),
                TextColumn::make('offer')
                    ->money('IDR'),
                TextColumn::make('offering_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->label('Vendor'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->role === 'Vendor') {
                    // Filter offerings by the current user's ID in the vendor_id column
                    $query->where('vendor_id', $user->id);
                }
            })
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Offering $record) => $record->offering_status !== 'accepted'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Offering $record) => $record->offering_status !== 'accepted'),
                Action::make('accept')
                    ->label('Accept')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (Offering $record) => 
                        auth()->user()->role === 'Admin' && 
                        $record->offering_status === 'pending'
                    )
                    ->action(function (Offering $record) {
                        $offerings = Offering::where('tender_id', $record->tender_id)->get();

                        \DB::transaction(function () use ($record, $offerings) {
                            $record->update(['offering_status' => 'accepted']);

                            foreach ($offerings as $offering) {
                                if ($offering->id !== $record->id) {
                                    $offering->update(['offering_status' => 'cancelled']);
                                }
                            }
                        });
                    }),
                Action::make('create_delivery')
                    ->label('Create Delivery')
                    ->color('primary')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Offering $record) => 
                    auth()->user()->role === 'Vendor' && 
                    $record->offering_status === 'accepted' &&
                    $record->vendor_id === auth()->id() && 
                    !\App\Models\Delivery::where('tender_id', $record->tender_id)
                             ->where('vendor_id', $record->vendor_id)
                             ->exists()
                )
                    ->url(fn (Offering $record) => route('filament.admin.resources.deliveries.create', [
                        'tender_id' => $record->tender_id,
                        'vendor_id' => $record->vendor_id
                    ]))
                    ,
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
            RelationManagers\RatingRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfferings::route('/'),
            'create' => Pages\CreateOffering::route('/create'),
            'edit' => Pages\EditOffering::route('/{record}/edit'),
        ];
    }
}