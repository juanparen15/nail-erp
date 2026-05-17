<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del servicio')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(2),

                Select::make('duration_minutes')
                    ->label('Duración')
                    ->options([
                        30 => '30 minutos',
                        45 => '45 minutos',
                        60 => '1 hora',
                        75 => '1h 15min',
                        90 => '1h 30min',
                        120 => '2 horas',
                    ])
                    ->required()
                    ->default(60)
                    ->columnSpan(1),

                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->columnSpan(1),

                ColorPicker::make('color')
                    ->label('Color en calendario')
                    ->required()
                    ->default('#ec4899')
                    ->columnSpan(1),

                Toggle::make('active')
                    ->label('Activo')
                    ->default(true)
                    ->columnSpan(1),

                Textarea::make('description')
                    ->label('Descripción')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
