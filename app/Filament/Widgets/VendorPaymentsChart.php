<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as DatabaseDB;

class VendorPaymentsChart extends ChartWidget
{
    protected static ?string $heading = 'My Monthly Payments';

    protected function getData(): array
    {
        $payments = Payment::select([
            DatabaseDB::raw('MONTH(payments.created_at) as month'),
            DatabaseDB::raw('YEAR(payments.created_at) as year'),
            DatabaseDB::raw('SUM(offerings.offer + offerings.delivery_cost) as total')
        ])
            ->join('offerings', function($join) {
                $join->on('payments.tender_id', '=', 'offerings.tender_id')
                    ->on('payments.vendor_id', '=', 'offerings.vendor_id');
            })
            ->where('payments.vendor_id', Auth::id())
            ->where('payments.payment_status', 1)
            ->where('offerings.offering_status', 'accepted')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                $date = Carbon::createFromDate($row->year, $row->month, 1);
                return [
                    'date' => $date->format('M Y'),
                    'total' => $row->total
                ];
            });

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Received Payments',
                    'data' => $payments->pluck('total')->toArray(),
                    'borderColor' => '#fff',
                    'backgroundColor' => '#f59e0b',
                    'fill' => false,
                ]
            ],
            'labels' => $payments->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                ],
            ],
        ];
    }
} 