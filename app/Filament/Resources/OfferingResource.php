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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Exists;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Get;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Storage;

class OfferingResource extends Resource
{
    protected static ?string $model = Offering::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $tenderId = request()->query('tender_id');
        $tenderInfo = null;
        
        if ($tenderId) {
            $tenderInfo = \App\Models\Tender::find($tenderId);
        }

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
                    ->disabled()
                    ->prefix('IDR'),
                TextInput::make('delivery_cost')
                    ->disabled()
                    ->prefix('IDR'),
                FileUpload::make('image')                    
                    ->disabled(),
                Section::make('Payment Information')
                    ->schema([
                        Select::make('payment_type')
                            ->options([
                                'dp' => 'Down Payment',
                                'full' => 'Full Payment',
                            ])
                            ->disabled(),
                        TextInput::make('dp_amount')
                            ->prefix('IDR')
                            ->disabled()
                            ->visible(fn (Forms\Get $get) => $get('payment_type') === 'dp'),
                    ]),
                Section::make('Status')
                    ->schema([
                        Select::make('offering_status')
                            ->options([
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                    ]),
            ]);
        }
        
        return $form
            ->schema([
                Section::make('Tender Information')
                    ->schema([
                    Grid::make(2)
                        ->schema([
                Select::make('tender_id')
                    ->relationship('tender', 'name')
                    ->default($tenderId)
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $tender = \App\Models\Tender::find($state);
                            if ($tender) {
                                $set('max_budget', $tender->budget);
                            }
                        }
                    }),
                TextInput::make('food_type')
                    ->default($tenderInfo?->food_type)
                    ->label('Food Type')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('quantity')
                    ->default($tenderInfo?->quantity)
                    ->label('Required Quantity')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('max_budget')
                    ->default($tenderInfo?->max_budget)
                    ->label('Maximum Budget')
                    ->prefix('IDR')
                    ->disabled()
                    ->dehydrated(false),]),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name')
                    ->default(fn () => Auth::id())
                    ->required()
                    ->hidden(),
                ]),

                Section::make('Offering Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->maxLength(255)
                            ->required(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('offer')
                                    ->required()
                                    ->numeric()
                                    ->prefix('IDR')
                                    // ->live()
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
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                        self::calculateTotalAndDP($state, $get('delivery_cost'), $set)
                                    ),
                                TextInput::make('delivery_cost')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('IDR')
                                    // ->live()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                        self::calculateTotalAndDP($get('offer'), $state, $set)
                                    ),
                            ]),
                        FileUpload::make('image')
                            ->image()
                            ->directory('offerings'),
                    ]),

                Section::make('Payment Details')
                    ->schema([
                        Select::make('payment_type')
                            ->options([
                                'dp' => 'Down Payment',
                                'full' => 'Full Payment',
                            ])
                            ->default('full')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set, Forms\Get $get) => 
                                self::calculateTotalAndDP($get('offer'), $get('delivery_cost'), $set)
                            ),
                        TextInput::make('dp_amount')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled()
                            ->default(0)
                            ->live()
                            ->visible(fn (Forms\Get $get) => $get('payment_type') === 'dp'),
                        Hidden::make('max_budget'),
                        Select::make('offering_status')
                            ->options([
                                'pending' => 'Pending',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->hidden()
                            ->required(),
                    ]),
            ]);
    }

    private static function calculateTotalAndDP($offer, $deliveryCost, Forms\Set $set): void
    {
        $offer = $offer ?? 0;
        $deliveryCost = $deliveryCost ?? 0;
        $total = $offer + $deliveryCost;
        
        // Calculate 30% for DP
        $dpAmount = $total * 0.3;
        
        $set('dp_amount', round($dpAmount, 2));
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
                ImageColumn::make('image')
                    ->getStateUsing(fn ($record) => $record->image)
                    ->url(fn ($record) => $record->image 
                        ? Storage::url($record->image) 
                        : null)
                    ->width(100)
                    ->height(100),
                TextColumn::make('offer')
                    ->money('IDR'),
                TextColumn::make('delivery_cost')
                    ->money('IDR'),
                TextColumn::make('payment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dp' => 'info',
                        'full' => 'success',
                    }),
                TextColumn::make('dp_amount')
                    ->money('IDR')
                    ->visible(fn ($record) => $record?->payment_type === 'dp'),
                TextColumn::make('offering_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        'completed' => 'success',
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
                    ->visible(fn (Offering $record) => $record->offering_status === 'pending'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Offering $record) => $record->offering_status === 'pending'),
                // Action::make('accept')
                //     ->label('Accept')
                //     ->color('success')
                //     ->icon('heroicon-o-check')
                //     ->requiresConfirmation()
                //     ->visible(fn (Offering $record) => 
                //         auth()->user()->role === 'Admin' && 
                //         $record->offering_status === 'pending'
                //     )
                //     ->action(function (Offering $record) {
                //         $offerings = Offering::where('tender_id', $record->tender_id)->get();

                //         \DB::transaction(function () use ($record, $offerings) {
                //             $record->update(['offering_status' => 'accepted']);

                //             foreach ($offerings as $offering) {
                //                 if ($offering->id !== $record->id) {
                //                     $offering->update(['offering_status' => 'cancelled']);
                //                 }
                //             }
                //         });
                //     }),
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
                    ])),
                Action::make('create_payment')
                    ->label('Create Payment')
                    ->color('primary')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Offering $record) => 
                        auth()->user()->role === 'Admin' && 
                        $record->offering_status === 'accepted' &&
                        !\App\Models\Payment::where('tender_id', $record->tender_id)
                            ->where('vendor_id', $record->vendor_id)
                            ->exists()
                    )
                    ->url(fn (Offering $record) => route('filament.admin.resources.payments.create', [
                        'tender_id' => $record->tender_id,
                        'vendor_id' => $record->vendor_id
                    ])),
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