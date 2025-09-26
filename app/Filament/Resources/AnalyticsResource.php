<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnalyticsResource\Pages;
use App\Models\Campaign;
use App\Models\Donation;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;
use BackedEnum;

class AnalyticsResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Analytics Reports';

    protected static UnitEnum | string | null $navigationGroup = 'Analytics';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Campaign::query()
                    ->select('campaigns.*')
                    ->selectRaw('COALESCE(SUM(donations.amount), 0) as total_raised')
                    ->selectRaw('COUNT(donations.id) as donation_count')
                    ->selectRaw('COUNT(DISTINCT donations.donor_id) as unique_donors')
                    ->selectRaw('AVG(donations.amount) as avg_donation')
                    ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
                    ->groupBy('campaigns.id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Campaign')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Creator')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->searchable(),

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
                    ->label('Unique Donors')
                    ->sortable(),

                Tables\Columns\TextColumn::make('avg_donation')
                    ->label('Avg Donation')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Conversion Rate')
                    ->state(function (Campaign $record): string {
                        // Simple conversion rate calculation
                        // This could be enhanced with actual view tracking
                        $rate = $record->donation_count > 0 ? 
                            min(100, ($record->donation_count / max($record->id, 1)) * 10) : 0;
                        return number_format($rate, 1) . '%';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (float) str_replace('%', '', $state) >= 5 => 'success',
                        (float) str_replace('%', '', $state) >= 2 => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'draft',
                        'warning' => 'pending',
                        'success' => 'active',
                        'secondary' => 'completed',
                        'gray' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\Filter::make('high_performing')
                    ->label('High Performing (>75% target)')
                    ->query(fn (Builder $query): Builder => 
                        $query->havingRaw('(COALESCE(SUM(donations.amount), 0) / campaigns.target_amount) * 100 >= 75')
                    ),

                Tables\Filters\Filter::make('recent')
                    ->label('Created This Month')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('campaigns.created_at', '>=', now()->startOfMonth())
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Campaign $record): string => route('campaigns.show', $record->slug))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('analytics')
                    ->label('Detailed Analytics')
                    ->icon('heroicon-m-chart-bar')
                    ->url(fn (Campaign $record): string => "/admin/analytics/campaign/{$record->id}")
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Analytics'),
                ]),
            ])
            ->defaultSort('total_raised', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalytics::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
