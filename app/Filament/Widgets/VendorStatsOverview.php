<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Offering;
use App\Models\Payment;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class VendorStatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $vendor_id = Auth::id();
        $ratings = Rating::where('vendor_id', $vendor_id);
        
        // Calculate vendor's total revenue from completed payments
        $totalRevenue = Payment::select(DB::raw('SUM(offerings.offer + offerings.delivery_cost) as total'))
            ->join('offerings', function($join) {
                $join->on('payments.tender_id', '=', 'offerings.tender_id')
                    ->on('payments.vendor_id', '=', 'offerings.vendor_id');
            })
            ->where('payments.vendor_id', $vendor_id)
            ->where('payments.payment_status', 1)
            ->where('offerings.offering_status', 'accepted')
            ->value('total');
        
        return [
            Stat::make('My Offerings', Offering::where('vendor_id', $vendor_id)->count())
                ->description('Total submissions')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
                
            Stat::make('Accepted Offerings', 
                Offering::where('vendor_id', $vendor_id)
                    ->where('offering_status', 'accepted')
                    ->count()
            )
                ->description('Accepted submissions')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
                
            Stat::make('Total Revenue', 'Rp ' . number_format($totalRevenue ?? 0, 0, ',', '.'))
                ->description('From completed payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
                
            Stat::make('Work Quality Rating',
                number_format($ratings->avg('work_quality'), 1) ?: 'N/A'
            )
                ->description('Performance score')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
                
            Stat::make('Communication Rating',
                number_format($ratings->avg('communication'), 1) ?: 'N/A'
            )
                ->description('Communication score')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
            Stat::make('Timelines Rating',
                number_format($ratings->avg('timelines'), 1) ?: 'N/A'
            )
                ->description('timelines score')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'flex flex-col text-center items-center justify-center',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
} 