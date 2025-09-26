<?php

namespace App\Filament\Resources\Withdrawals\Schemas;

use App\Models\User;
use App\Models\Withdrawal;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class WithdrawalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penarikan')
                    ->description('Detail permintaan penarikan dana')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('campaign_id')
                                    ->label('Kampanye')
                                    ->relationship('campaign', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                
                                TextInput::make('amount')
                                    ->label('Jumlah Penarikan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state)),
                                
                                Select::make('method')
                                    ->label('Metode Penarikan')
                                    ->options([
                                        'bank_transfer' => '🏦 Transfer Bank',
                                        'e_wallet' => '📱 E-Wallet',
                                    ])
                                    ->required()
                                    ->native(false),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('fee_amount')
                                    ->label('Biaya Admin')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state))
                                    ->disabled()
                                    ->dehydrated(),
                                
                                TextInput::make('net_amount')
                                    ->label('Jumlah Diterima')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(['.', ','], '', $state))
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                        
                        KeyValue::make('account_info')
                            ->label('Informasi Akun')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Status & Approval')
                    ->description('Manajemen status dan persetujuan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        Withdrawal::STATUS_PENDING => '⏳ Pending',
                                        Withdrawal::STATUS_APPROVED => '✅ Approved',
                                        Withdrawal::STATUS_PROCESSING => '🔄 Processing',
                                        Withdrawal::STATUS_COMPLETED => '✅ Completed',
                                        Withdrawal::STATUS_REJECTED => '❌ Rejected',
                                        Withdrawal::STATUS_CANCELLED => '🚫 Cancelled',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->default(Withdrawal::STATUS_PENDING),
                                
                                Select::make('approved_by')
                                    ->label('Disetujui Oleh')
                                    ->relationship('approvedBy', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->options(User::where('role', 'admin')->pluck('name', 'id')),
                            ]),
                        
                        TextInput::make('reference_number')
                            ->label('Nomor Referensi')
                            ->placeholder('Akan diisi otomatis saat completed')
                            ->columnSpan(1),
                        
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan admin untuk penarikan ini...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Timeline')
                    ->description('Waktu proses penarikan')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('requested_at')
                                    ->label('Waktu Permintaan')
                                    ->required()
                                    ->default(now())
                                    ->native(false),
                                
                                DateTimePicker::make('approved_at')
                                    ->label('Waktu Persetujuan')
                                    ->native(false),
                                
                                DateTimePicker::make('processed_at')
                                    ->label('Waktu Diproses')
                                    ->native(false),
                                
                                DateTimePicker::make('completed_at')
                                    ->label('Waktu Selesai')
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }
}
