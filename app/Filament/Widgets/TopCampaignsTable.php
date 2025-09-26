<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ViewAction;

class TopCampaignsTable extends BaseWidget
{
    static ?string $heading = 'Top Performing Campaigns';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Campaign::query()
                    ->select('campaigns.*')
                    ->selectRaw('COALESCE(SUM(donations.amount), 0) as total_raised')
                    ->selectRaw('COUNT(donations.id) as donation_count')
                    ->selectRaw('COUNT(DISTINCT donations.donor_id) as unique_donors')
                    ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
                    ->groupBy('campaigns.id')
                    ->orderByDesc('total_raised')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->height(50)
                    ->width(80)
                    ->defaultImageUrl(url('/images/no-image.png')),
                    
                Tables\Columns\TextColumn::make('title')
                    ->label('Campaign Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creator')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total_raised')
                    ->label('Total Raised')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('target_amount')
                    ->label('Target')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->state(function (Campaign $record): string {
                        $percentage = $record->target_amount > 0 
                            ? ($record->total_raised / $record->target_amount) * 100 
                            : 0;
                        return number_format($percentage, 1) . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (float) str_replace('%', '', $state) >= 100 => 'success',
                        (float) str_replace('%', '', $state) >= 75 => 'info',
                        (float) str_replace('%', '', $state) >= 50 => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('donation_count')
                    ->label('Donations')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unique_donors')
                    ->label('Donors')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'draft',
                        'warning' => 'pending',
                        'success' => 'active',
                        'secondary' => 'completed',
                        'gray' => 'rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Campaign $record): string => route('campaigns.show', $record->slug))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('total_raised', 'desc');
    }
}
