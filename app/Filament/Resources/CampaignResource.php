<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static UnitEnum|string|null $navigationGroup = 'Donation'; 

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Campaigns';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Campaign Information')
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(300)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(350)
                            ->unique(Campaign::class, 'slug', ignoreRecord: true),

                        Select::make('user_id')
                            ->label('Campaign Creator')
                            ->options(User::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Textarea::make('short_desc')
                            ->label('Short Description')
                            ->maxLength(500)
                            ->rows(3),

                        RichEditor::make('description')
                            ->columnSpanFull(),

                            ImageEntry::make('featured_image')
                            ->label('Current Featured Image')
                            ->disk('public')
                            ->height(200)
                            ->width(500)
                            ->defaultImageUrl(url('/images/no-image.png'))
                            ->getStateUsing(function ($record) {
                                if (!$record->featured_image) {
                                    return null;
                                }
                                return $record->getRawOriginal('featured_image');
                            })
                            ->visible(fn ($record) => !is_null($record->featured_image))
                            ->columnSpanFull(),

                        FileUpload::make('featured_image')
                            ->image()
                            ->disk('public')
                            ->directory('campaigns')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(2048)
                            ->nullable()
                            ->deletable(true)
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->imagePreviewHeight('250')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->loadingIndicatorPosition('center')
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated')
                            ->removeUploadedFileButtonPosition('right')
                            ->uploadButtonPosition('left')
                            ->uploadProgressIndicatorPosition('left')
                            ->hint('Upload gambar dengan rasio 16:9 untuk hasil terbaik')
                            ->hintColor('primary')
                            ->helperText('Format yang didukung: JPEG, PNG, GIF, WebP. Maksimal 2MB.')
                            ->storeFileNamesIn('featured_image_original_name'),
                    ])
                    ->columns(2),

                Section::make('Campaign Details')
                    ->components([
                        TextInput::make('target_amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->minValue(100000),

                        TextInput::make('collected_amount')
                            ->numeric()
                            ->prefix('IDR')
                            ->default(0)
                            ->disabled(),

                        Select::make('currency')
                            ->options([
                                'IDR' => 'Indonesian Rupiah',
                                'USD' => 'US Dollar',
                            ])
                            ->default('IDR')
                            ->required(),

                        Select::make('goal_type')
                            ->options([
                                'amount' => 'Amount',
                                'flexible' => 'Flexible',
                            ])
                            ->default('amount')
                            ->required(),

                        DatePicker::make('deadline')
                            ->after('today'),

                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'paused' => 'Paused',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),

                        Toggle::make('allow_anonymous')
                            ->label('Allow Anonymous Donations')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')
                    ->disk('public')
                    ->size(60)
                    ->visibility('public')
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->getStateUsing(function ($record) {
                        if (!$record->featured_image) {
                            return null;
                        }
                        // Return the full path as stored in database
                        return $record->getRawOriginal('featured_image');
                    }),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('user.name')
                    ->label('Creator')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('target_amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('collected_amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('progress_percentage')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'active',
                        'warning' => 'paused',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'paused' => 'Paused',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'view' => Pages\ViewCampaign::route('/{record}'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
