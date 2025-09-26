<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Campaign Information')
                    ->schema([
                        ImageEntry::make('featured_image')
                            ->disk('public')
                            ->height(200)
                            ->width(300)
                            ->defaultImageUrl(url('/images/no-image.png'))
                            ->visible(fn ($record) => !is_null($record->featured_image))
                            ->getStateUsing(function ($record) {
                                if (!$record->featured_image) {
                                    return null;
                                }
                                return $record->getRawOriginal('featured_image');
                            }),

                        TextEntry::make('title')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),

                        TextEntry::make('user.name')
                            ->label('Creator'),

                        TextEntry::make('category.name')
                            ->label('Category'),

                        TextEntry::make('deadline')
                            ->date(),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'active' => 'success',
                                'completed' => 'primary',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Description')
                    ->schema([
                        TextEntry::make('short_desc')
                            ->label('Short Description')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Section::make('Financial Information')
                    ->schema([
                        TextEntry::make('target_amount')
                            ->money('IDR')
                            ->label('Target Amount'),

                        TextEntry::make('collected_amount')
                            ->money('IDR')
                            ->label('Collected Amount'),
                    ])
                    ->columns(2),
            ]);
    }
}
