<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static UnitEnum | string | null $navigationGroup = 'User Management';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                DateTimePicker::make('email_verified_at'),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),

                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('avatar')
                    ->maxLength(255),

                Toggle::make('is_active')
                    ->default(true),

                Toggle::make('is_verified')
                    ->label('Email Verified')
                    ->default(false),

                Select::make('role')
                    ->options([
                        'donor' => 'Donor',
                        'creator' => 'Campaign Creator',
                        'admin' => 'Admin',
                    ])
                    ->default('donor')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'secondary' => 'donor',
                        'primary' => 'creator',
                        'success' => 'admin',
                    ]),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean(),

                TextColumn::make('campaigns_count')
                    ->label('Campaigns')
                    ->counts('campaigns')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'donor' => 'Donor',
                        'creator' => 'Campaign Creator',
                        'admin' => 'Admin',
                    ]),

                TernaryFilter::make('is_verified')
                    ->label('Email Verified'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
