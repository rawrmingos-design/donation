<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryPerformanceChart extends ChartWidget
{
    public ?string $heading = 'Campaign Performance by Category';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = Campaign::select('categories.name as category_name')
            ->selectRaw('COALESCE(SUM(donations.amount), 0) as total_raised')
            ->selectRaw('COUNT(campaigns.id) as campaign_count')
            ->leftJoin('categories', 'campaigns.category_id', '=', 'categories.id')
            ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_raised')
            ->limit(8)
            ->get();

        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(6, 182, 212, 0.8)',    // Cyan
            'rgba(34, 197, 94, 0.8)',    // Emerald
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Total Raised (Rp)',
                    'data' => $data->pluck('total_raised')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderColor' => array_map(function($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, $data->count())),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('category_name')->map(function($name) {
                return $name ?? 'Uncategorized';
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": Rp " + context.parsed.toLocaleString("id-ID");
                        }'
                    ]
                ]
            ],
        ];
    }
}
