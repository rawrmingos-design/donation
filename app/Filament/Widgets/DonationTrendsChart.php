<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DonationTrendsChart extends ChartWidget
{
    public ?string $heading = 'Donation Trends (Last 30 Days)';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            $amount = Donation::whereDate('created_at', $date->toDateString())->sum('amount');
            $count = Donation::whereDate('created_at', $date->toDateString())->count();
            
            return [
                'date' => $date->format('M d'),
                'amount' => $amount,
                'count' => $count,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Donation Amount (Rp)',
                    'data' => $data->pluck('amount')->toArray(),
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Number of Donations',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Amount (Rp)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Count',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
