<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $user = Auth::user();
        
        if ($user->role === 'Admin') {
            return [
                \App\Filament\Widgets\AdminStatsOverview::class,
                \App\Filament\Widgets\PaymentsChart::class,
                \App\Filament\Widgets\TenderOffersChart::class,
            ];
        } else {
            return [
                \App\Filament\Widgets\VendorStatsOverview::class,
                \App\Filament\Widgets\VendorPaymentsChart::class,
            ];
        }
    }
} 