<?php

namespace App\Filament\Resources\MarketingCampaigns\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MarketingCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Título de la campaña')
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->label('Mensaje')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                CheckboxList::make('channels')
                    ->label('Canales de envío')
                    ->options([
                        'email' => 'Email',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->default(['email'])
                    ->required()
                    ->columnSpan(1),

                Select::make('target')
                    ->label('Segmento de clientes')
                    ->options([
                        'all' => 'Todos los clientes',
                        'inactive_3months' => 'Inactivos +3 meses',
                        'birthday_month' => 'Cumpleaños del mes',
                    ])
                    ->required()
                    ->default('all')
                    ->columnSpan(1),

                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'Borrador',
                        'scheduled' => 'Programada',
                    ])
                    ->required()
                    ->default('draft')
                    ->columnSpan(1),

                DateTimePicker::make('scheduled_at')
                    ->label('Programar para')
                    ->helperText('Déjalo vacío para envío inmediato')
                    ->visible(fn ($get) => $get('status') === 'scheduled')
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
