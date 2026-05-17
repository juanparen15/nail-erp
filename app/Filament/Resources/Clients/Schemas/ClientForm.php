<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(1),

                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('whatsapp')
                    ->label('WhatsApp')
                    ->tel()
                    ->helperText('Si es diferente al teléfono')
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->columnSpan(1),

                Textarea::make('notes')
                    ->label('Notas internas')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
