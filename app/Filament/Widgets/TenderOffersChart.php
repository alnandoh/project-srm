<?php

namespace App\Filament\Widgets;

use App\Models\Tender;
use App\Models\Offering;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB as DatabaseDB;

class TenderOffersChart extends ChartWidget
{
    protected static ?string $heading = 'Tenders & Offerings Overview';

    protected function getData(): array
    {
        $tenders = Tender::select([
            'tenders.name',
            DatabaseDB::raw('COUNT(offerings.id) as offerings_count')
        ])
            ->leftJoin('offerings', 'tenders.id', '=', 'offerings.tender_id')
            ->groupBy('tenders.id', 'tenders.name')
            ->orderBy('tenders.created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Offerings',
                    'data' => $tenders->pluck('offerings_count')->toArray(),
                    'backgroundColor' => '#f59e0b',
                ]
            ],
            'labels' => $tenders->pluck('name')->toArray(),
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
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
} 