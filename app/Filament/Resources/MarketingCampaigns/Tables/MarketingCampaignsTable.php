<?php

namespace App\Filament\Resources\MarketingCampaigns\Tables;

use App\Jobs\SendMarketingCampaign;
use App\Models\MarketingCampaign;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MarketingCampaignsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Campaña')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('target')
                    ->label('Segmento')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'all' => 'Todos',
                        'inactive_3months' => 'Inactivos +3m',
                        'birthday_month' => 'Cumpleaños',
                        default => $state,
                    })
                    ->badge()
                    ->color('info'),

                TextColumn::make('channels')
                    ->label('Canales')
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? implode(', ', array_map('strtoupper', $state))
                        : strtoupper($state)
                    ),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'draft' => 'Borrador',
                        'scheduled' => 'Programada',
                        'sent' => 'Enviada',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sent' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('sent_count')
                    ->label('Enviados')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('sent_at')
                    ->label('Enviada el')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'scheduled' => 'Programada',
                        'sent' => 'Enviada',
                    ]),
            ])
            ->recordActions([
                Action::make('send')
                    ->label('Enviar ahora')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Enviar campaña?')
                    ->modalDescription('Esta acción enviará el mensaje a todos los clientes del segmento seleccionado.')
                    ->visible(fn (MarketingCampaign $record) => in_array($record->status, ['draft', 'scheduled']))
                    ->action(function (MarketingCampaign $record) {
                        SendMarketingCampaign::dispatch($record->id);
                        $record->update(['status' => 'scheduled']);
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
