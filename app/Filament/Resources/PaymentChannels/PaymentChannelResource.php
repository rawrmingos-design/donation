<?php

namespace App\Filament\Resources\PaymentChannels;

use App\Filament\Resources\PaymentChannels\Pages\CreatePaymentChannel;
use App\Filament\Resources\PaymentChannels\Pages\EditPaymentChannel;
use App\Filament\Resources\PaymentChannels\Pages\ListPaymentChannels;
use App\Filament\Resources\PaymentChannels\Schemas\PaymentChannelForm;
use App\Filament\Resources\PaymentChannels\Tables\PaymentChannelsTable;
use App\Models\PaymentChannel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PaymentChannelResource extends Resource
{
    protected static ?string $model = PaymentChannel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Payment Channels';

    protected static UnitEnum | string | null $navigationGroup = 'Payment Settings';

    public static function form(Schema $schema): Schema
    {
        return PaymentChannelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentChannelsTable::configure($table);
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
            'index' => ListPaymentChannels::route('/'),
            'create' => CreatePaymentChannel::route('/create'),
            'edit' => EditPaymentChannel::route('/{record}/edit'),
        ];
    }
}
