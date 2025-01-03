<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Tender;
use App\Models\Offering;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdminStatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Calculate total payments from accepted offerings
        $totalPayments = Payment::select(DB::raw('SUM(offerings.offer + offerings.delivery_cost) as total'))
            ->join('offerings', function($join) {
                $join->on('payments.tender_id', '=', 'offerings.tender_id')
                    ->on('payments.vendor_id', '=', 'offerings.vendor_id');
            })
            ->where('payments.payment_status', 1)
            ->where('offerings.offering_status', 'accepted')
            ->value('total');
        
        return [
            Stat::make('Total Tenders', Tender::count())
                ->description('All tenders')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'flex flex-col items-center justify-center text-center',
                ]),
                
            Stat::make('Completed Tenders', 
                Offering::where('offering_status', 'completed')
                    ->distinct('tender_id')
                    ->count('tender_id')
            )
                ->description('Finished tenders')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes([
                    'class' => 'flex flex-col items-center justify-center text-center',
                ]),
                
            Stat::make('Total Offerings', Offering::count())
                ->description('All submissions')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'flex flex-col items-center justify-center text-center',
                ]),
                
            Stat::make('Total Payment Value', 'Rp ' . number_format($totalPayments ?? 0, 0, ',', '.'))
                ->description('Total completed payments')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'class' => 'flex flex-col items-center justify-center text-center',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
} 