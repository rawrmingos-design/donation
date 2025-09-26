<?php

namespace App\Filament\Resources\PaymentChannels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('fee_fixed')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fee_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
