<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Appointment;
use App\Models\AppSetting;
use App\Notifications\AppointmentCancelled;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('appointment_date', 'desc')
            ->columns([
                TextColumn::make('appointment_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('appointment_time')
                    ->label('Hora')
                    ->formatStateUsing(fn ($state) => substr($state, 0, 5)),

                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.phone')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('service.name')
                    ->label('Servicio')
                    ->badge()
                    ->color(fn ($record) => 'gray'),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No asistió',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                        'no_show' => 'No asistió',
                    ]),

                Filter::make('today')
                    ->label('Hoy')
                    ->query(fn (Builder $query) => $query->whereDate('appointment_date', today()))
                    ->toggle(),

                Filter::make('upcoming')
                    ->label('Próximas')
                    ->query(fn (Builder $query) => $query->whereDate('appointment_date', '>=', today())
                        ->whereIn('status', ['pending', 'confirmed']))
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirmar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Appointment $record) => $record->status === 'pending')
                    ->action(fn (Appointment $record) => $record->update(['status' => 'confirmed'])),

                Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('¿Marcar cita como completada?')
                    ->modalDescription(fn (Appointment $record) => "Se registrará la visita de {$record->client->name} y se actualizará su historial.")
                    ->modalSubmitActionLabel('Sí, completar')
                    ->visible(fn (Appointment $record) => $record->status === 'confirmed')
                    ->action(function (Appointment $record) {
                        $record->update(['status' => 'completed']);
                        $record->client->incrementVisits();

                        // ── Programa de lealtad ───────────────────────────
                        $loyaltyOn  = AppSetting::get('loyalty_enabled', '0') === '1';
                        $threshold  = (int) AppSetting::get('loyalty_threshold', 7);

                        if ($loyaltyOn && $threshold > 0) {
                            $visits = $record->client->fresh()->total_visits;
                            if ($visits % $threshold === 0) {
                                Notification::make()
                                    ->title('¡Premio de lealtad ganado!')
                                    ->body("{$record->client->name} completó {$visits} servicios. ¡Su próxima cita es gratis!")
                                    ->success()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    }),

                Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('¿Cancelar esta cita?')
                    ->modalDescription(fn (Appointment $record) => "Se cancelará la cita de {$record->client->name} y se le notificará por WhatsApp/email.")
                    ->modalSubmitActionLabel('Sí, cancelar cita')
                    ->visible(fn (Appointment $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->action(function (Appointment $record) {
                        $record->update(['status' => 'cancelled']);
                        try {
                            $record->client->notify(new AppointmentCancelled($record));
                        } catch (\Exception $e) {
                            logger()->error($e->getMessage());
                        }
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
