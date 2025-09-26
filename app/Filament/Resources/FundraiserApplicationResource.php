<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FundraiserApplicationResource\Pages;
use App\Models\FundraiserApplication;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Components\Section;
use UnitEnum;
use BackedEnum;
use Filament\Schemas\Schema;


class FundraiserApplicationResource extends Resource
{
    protected static ?string $model = FundraiserApplication::class;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Fundraiser Applications';

    protected static ?string $modelLabel = 'Fundraiser Application';

    protected static ?string $pluralModelLabel = 'Fundraiser Applications';

    protected static UnitEnum | string | null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Application Details')
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($context) => $context === 'edit'),

                        TextInput::make('full_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->required()
                            ->rows(3),

                        TextInput::make('id_card_number')
                            ->label('ID Card Number')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('id_card_photo')
                            ->label('ID Card Photo')
                            ->image()
                            ->directory('fundraiser-applications/id-cards')
                            ->disk('local')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(2048)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('600'),
                    ])
                    ->columns(2),

                Section::make('Motivation & Experience')
                    ->schema([
                        Textarea::make('motivation')
                            ->required()
                            ->rows(4),

                        Textarea::make('experience')
                            ->rows(4),

                        TextInput::make('social_media_links')
                            ->label('Social Media Links')
                            ->url()
                            ->maxLength(255),
                    ]),

                Section::make('Review Information')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending'),

                        Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(3)
                            ->helperText('Internal notes for this application'),

                        DateTimePicker::make('reviewed_at')
                            ->label('Reviewed At')
                            ->disabled(),

                        Select::make('reviewed_by')
                            ->label('Reviewed By')
                            ->relationship('reviewer', 'name')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->visible(fn ($context) => $context === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-check-circle' => 'approved',
                        'heroicon-o-x-circle' => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Applied From'),
                        DatePicker::make('created_until')
                            ->label('Applied Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Fundraiser Application')
                    ->modalDescription('Are you sure you want to approve this fundraiser application?')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Approval Notes')
                            ->placeholder('Optional notes for approval...')
                            ->rows(3),
                    ])
                    ->action(function (FundraiserApplication $record, array $data): void {
                        $record->update([
                            'status' => 'approved',
                            'admin_notes' => $data['admin_notes'] ?? null,
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);

                        // Update user role to creator
                        $record->user->update(['role' => 'creator']);
                    })
                    ->visible(fn (FundraiserApplication $record): bool => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Fundraiser Application')
                    ->modalDescription('Are you sure you want to reject this fundraiser application?')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->placeholder('Please provide a reason for rejection...')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (FundraiserApplication $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                            'reviewed_at' => now(),
                            'reviewed_by' => Auth::id(),
                        ]);
                    })
                    ->visible(fn (FundraiserApplication $record): bool => $record->status === 'pending'),

                Action::make('reset')
                    ->label('Reset to Pending')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Application Status')
                    ->modalDescription('This will reset the application status to pending.')
                    ->action(function (FundraiserApplication $record): void {
                        $record->update([
                            'status' => 'pending',
                            'admin_notes' => null,
                            'reviewed_at' => null,
                            'reviewed_by' => null,
                        ]);

                        // If user was creator, revert to donor
                        if ($record->user->role === 'creator') {
                            $record->user->update(['role' => 'donor']);
                        }
                    })
                    ->visible(fn (FundraiserApplication $record): bool => $record->status !== 'pending'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Approve Selected Applications')
                        ->modalDescription('Are you sure you want to approve all selected applications?')
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'approved',
                                        'reviewed_at' => now(),
                                        'reviewed_by' => Auth::id(),
                                    ]);
                                    
                                    // Update user role to creator
                                    $record->user->update(['role' => 'creator']);
                                }
                            }
                        }),

                    BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Selected Applications')
                        ->modalDescription('Are you sure you want to reject all selected applications?')
                        ->form([
                            Textarea::make('admin_notes')
                                ->label('Rejection Reason')
                                ->placeholder('Please provide a reason for rejection...')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'rejected',
                                        'admin_notes' => $data['admin_notes'],
                                        'reviewed_at' => now(),
                                        'reviewed_by' => Auth::id(),
                                    ]);
                                }
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User Name'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('user.role')
                            ->label('Current Role')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'creator' => 'success',
                                'donor' => 'primary',
                                default => 'gray',
                            }),
                    ])
                    ->columns(3),

                

                Section::make('Motivation & Experience')
                    ->schema([
                        TextEntry::make('motivation')
                            ->columnSpanFull(),
                        TextEntry::make('experience')
                            ->columnSpanFull(),
                        TextEntry::make('social_media_links')
                            ->label('Social Media Links')
                            ->url(fn ($record) => $record->social_media_links)
                            ->openUrlInNewTab(),
                    ]),
                    Section::make('Application Details')
                    ->schema([
                        TextEntry::make('full_name'),
                        TextEntry::make('phone'),
                        TextEntry::make('address'),
                        TextEntry::make('id_card_number')
                            ->label('ID Card Number'),
                        TextEntry::make('id_card_photo')
                            ->label('ID Card Photo')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return 'No photo uploaded';
                                }
                                return 'Photo uploaded: ' . basename($state);
                            })
                            ->badge()
                            ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                            ->suffixAction(
                                Action::make('view_photo')
                                    ->icon('heroicon-o-eye')
                                    ->url(fn ($record) => $record->id_card_photo ? route('fundraiser.id-card.view', ['filename' => basename($record->id_card_photo)]) : null)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record) => !empty($record->id_card_photo))
                            ),
                    ])
                    ->columns(2),

                Section::make('Review Status')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('created_at')
                            ->label('Applied At')
                            ->dateTime(),
                        TextEntry::make('reviewed_at')
                            ->dateTime(),
                        TextEntry::make('reviewer.name')
                            ->label('Reviewed By'),
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListFundraiserApplications::route('/'),
            'create' => Pages\CreateFundraiserApplication::route('/create'),
            'view' => Pages\ViewFundraiserApplication::route('/{record}'),
            'edit' => Pages\EditFundraiserApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'reviewer']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
