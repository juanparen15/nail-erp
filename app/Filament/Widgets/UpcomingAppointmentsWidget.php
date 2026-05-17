<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingAppointmentsWidget extends BaseWidget
{
    protected ?string $heading = 'Próximas citas';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->with(['client', 'service'])
                    ->whereDate('appointment_date', '>=', Carbon::today())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->orderBy('appointment_date')
                    ->orderBy('appointment_time')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('appointment_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Hora')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('H:i')),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('client.phone')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Servicio')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'confirmed' => 'info',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Precio')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
