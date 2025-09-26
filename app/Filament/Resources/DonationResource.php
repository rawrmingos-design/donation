<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationResource\Pages;
use App\Models\Donation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class DonationResource extends Resource
{
    protected static ?string $model = Donation::class;

    protected static UnitEnum|string|null $navigationGroup = 'Donation';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Donations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campaign_id')
                    ->relationship('campaign', 'title')
                    ->required()
                    ->searchable(),

                Select::make('donor_id')
                    ->relationship('donor', 'name')
                    ->required()
                    ->searchable(),

                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('IDR'),

                Select::make('currency')
                    ->options([
                        'IDR' => 'Indonesian Rupiah',
                        'USD' => 'US Dollar',
                    ])
                    ->default('IDR')
                    ->required(),

                Textarea::make('message')
                    ->maxLength(1000)
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('donor.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('donor.email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('message')
                    ->limit(50)
                    ->toggleable(),

                BadgeColumn::make('transaction.status')
                    ->label('Payment Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'success',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('campaign')
                    ->relationship('campaign', 'title'),

                Filter::make('amount')
                    ->form([
                        TextInput::make('amount_from')
                            ->numeric()
                            ->prefix('IDR'),
                        TextInput::make('amount_to')
                            ->numeric()
                            ->prefix('IDR'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['amount_from'], fn ($query, $amount) => $query->where('amount', '>=', $amount))
                            ->when($data['amount_to'], fn ($query, $amount) => $query->where('amount', '<=', $amount));
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'view' => Pages\ViewDonation::route('/{record}'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
