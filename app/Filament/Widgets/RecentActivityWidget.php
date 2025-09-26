<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RecentActivityWidget extends BaseWidget
{
    static ?string $heading = 'Recent Activity';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // Get recent activities
        $activities = $this->getRecentActivities();

        return $table
            ->query(
                // Create a fake query builder for the activities
                Donation::query()->whereRaw('1 = 0') // Empty query
            )
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'donation' => 'success',
                        'campaign' => 'info',
                        'user' => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Activity')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->placeholder('—'),
                    
                Tables\Columns\TextColumn::make('user')
                    ->label('User')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }

    protected function getRecentActivities(): Collection
    {
        $activities = collect();

        // Recent donations
        $recentDonations = Donation::with(['campaign', 'donor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'donation',
                    'description' => "New donation to \"{$donation->campaign->title}\"",
                    'amount' => $donation->amount,
                    'user' => $donation->donor->name ?? 'Anonymous',
                    'created_at' => $donation->created_at,
                ];
            });

        // Recent campaigns
        $recentCampaigns = Campaign::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($campaign) {
                return [
                    'type' => 'campaign',
                    'description' => "New campaign created: \"{$campaign->title}\"",
                    'amount' => null,
                    'user' => $campaign->user->name,
                    'created_at' => $campaign->created_at,
                ];
            });

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'description' => "New user registered",
                    'amount' => null,
                    'user' => $user->name,
                    'created_at' => $user->created_at,
                ];
            });

        return $activities
            ->merge($recentDonations)
            ->merge($recentCampaigns)
            ->merge($recentUsers)
            ->sortByDesc('created_at')
            ->take(10);
    }
}
