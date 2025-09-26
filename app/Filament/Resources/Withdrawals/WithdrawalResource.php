<?php

namespace App\Filament\Resources\Withdrawals;

use App\Filament\Resources\Withdrawals\Pages\CreateWithdrawal;
use App\Filament\Resources\Withdrawals\Pages\EditWithdrawal;
use App\Filament\Resources\Withdrawals\Pages\ListWithdrawals;
use App\Filament\Resources\Withdrawals\Schemas\WithdrawalForm;
use App\Filament\Resources\Withdrawals\Tables\WithdrawalsTable;
use App\Models\Withdrawal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'id';

    protected static UnitEnum | string | null $navigationGroup = 'Financial Management';
    
    protected static ?string $navigationLabel = 'Withdrawals';
    
    protected static ?string $modelLabel = 'Withdrawal';
    
    protected static ?string $pluralModelLabel = 'Withdrawals';
    
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return WithdrawalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWithdrawals::route('/'),
            'create' => CreateWithdrawal::route('/create'),
            'edit' => EditWithdrawal::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
    
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }
    
    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return "Withdrawal #{$record->id} - {$record->campaign->title}";
    }
    
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Campaign' => $record->campaign->title,
            'Amount' => 'Rp ' . number_format($record->amount, 0, ',', '.'),
            'Status' => ucfirst($record->status),
            'Method' => $record->method === 'bank_transfer' ? 'Bank Transfer' : 'E-Wallet',
        ];
    }
}
