<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('phone')->required(),
                        TextInput::make('email')->email(),
                        TextInput::make('whatsapp'),
                    ])
                    ->required()
                    ->columnSpan(1),

                Select::make('service_id')
                    ->relationship('service', 'name', fn ($query) => $query->where('active', true))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $service = \App\Models\Service::find($state);
                            if ($service) {
                                $set('total_price', number_format((int) $service->price, 0, ',', '.'));
                            }
                        }
                    })
                    ->required()
                    ->columnSpan(1),

                DatePicker::make('appointment_date')
                    ->required()
                    ->native(false)
                    ->minDate(today())
                    ->columnSpan(1),

                TimePicker::make('appointment_time')
                    ->required()
                    ->seconds(false)
                    ->columnSpan(1),

                Select::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No asistió',
                    ])
                    ->required()
                    ->default('pending')
                    ->columnSpan(1),

                TextInput::make('total_price')
                    ->label('Precio total')
                    ->required()
                    ->prefix('$')
                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                    ->dehydrateStateUsing(fn ($state) => (int) str_replace('.', '', str_replace(',', '', $state ?? '0')))
                    ->rules(['required', 'min:0'])
                    ->columnSpan(1),

                Textarea::make('notes')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
