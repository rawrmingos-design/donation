<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentMethodChart extends ChartWidget
{
    public ?string $heading = 'Payment Methods Usage';
    
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $data = Transaction::select('payment_providers.name as provider_name')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->selectRaw('SUM(transactions.amount) as total_amount')
            ->join('payment_providers', 'transactions.provider_id', '=', 'payment_providers.id')
            ->where('transactions.status', 'success')
            ->groupBy('payment_providers.id', 'payment_providers.name')
            ->orderByDesc('total_amount')
            ->get();

        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue for Midtrans
            'rgba(16, 185, 129, 0.8)',   // Green for Tokopay
            'rgba(245, 158, 11, 0.8)',   // Yellow for others
            'rgba(239, 68, 68, 0.8)',    // Red for others
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Transaction Count',
                    'data' => $data->pluck('transaction_count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                    'borderColor' => array_map(function($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, $data->count())),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('provider_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
                            return context.label + ": " + context.parsed + " transactions";
                        }'
                    ]
                ]
            ],
        ];
    }
}
