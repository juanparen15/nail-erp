<?php

namespace App\Filament\Pages;

use App\Models\BlockedDate;
use App\Models\BusinessSchedule;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BusinessSettings extends Page
{
    protected string $view = 'filament.pages.business-settings';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Configuración';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Configuración del Negocio';

    public ?array $data = [];

    public function mount(): void
    {
        $schedules = BusinessSchedule::orderBy('day_of_week')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'day_name' => BusinessSchedule::$dayNames[$s->day_of_week] ?? '',
                'open_time' => $s->open_time,
                'close_time' => $s->close_time,
                'slot_interval_minutes' => $s->slot_interval_minutes,
                'active' => $s->active,
            ])
            ->toArray();

        $this->form->fill(['schedules' => $schedules]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Horarios de Atención')
                    ->description('Configura los días y horas en que el salón atiende al público')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Repeater::make('schedules')
                            ->label('')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Hidden::make('id'),

                                TextInput::make('day_name')
                                    ->label('Día')
                                    ->disabled()
                                    ->dehydrated(false),

                                TimePicker::make('open_time')
                                    ->label('Apertura')
                                    ->seconds(false)
                                    ->required(),

                                TimePicker::make('close_time')
                                    ->label('Cierre')
                                    ->seconds(false)
                                    ->required(),

                                Select::make('slot_interval_minutes')
                                    ->label('Intervalo de slots')
                                    ->options([
                                        15 => 'Cada 15 min',
                                        20 => 'Cada 20 min',
                                        30 => 'Cada 30 min',
                                        45 => 'Cada 45 min',
                                        60 => 'Cada 60 min',
                                    ])
                                    ->required(),

                                Toggle::make('active')
                                    ->label('Activo')
                                    ->inline(false),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function saveSchedules(): void
    {
        $data = $this->form->getState();

        foreach ($data['schedules'] as $schedule) {
            BusinessSchedule::where('id', $schedule['id'])->update([
                'open_time' => $schedule['open_time'],
                'close_time' => $schedule['close_time'],
                'slot_interval_minutes' => $schedule['slot_interval_minutes'],
                'active' => $schedule['active'],
            ]);
        }

        Notification::make()
            ->title('Horarios guardados correctamente')
            ->success()
            ->send();
    }

    public function removeBlockedDate(int $id): void
    {
        BlockedDate::find($id)?->delete();

        Notification::make()
            ->title('Fecha desbloqueada')
            ->success()
            ->send();
    }

    public function getBlockedDatesProperty()
    {
        return BlockedDate::orderBy('date')->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveSchedules')
                ->label('Guardar horarios')
                ->icon('heroicon-o-check')
                ->color('success')
                ->action('saveSchedules'),

            Action::make('addBlockedDate')
                ->label('Bloquear fecha')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->modalWidth('sm')
                ->modalHeading('Bloquear fecha')
                ->modalDescription('Los clientes no podrán reservar citas en esta fecha.')
                ->form([
                    DatePicker::make('date')
                        ->label('Fecha')
                        ->required()
                        ->minDate(today())
                        ->native(false),
                    TextInput::make('reason')
                        ->label('Motivo (opcional)')
                        ->placeholder('Festivo, vacaciones, capacitación...'),
                ])
                ->action(function (array $data): void {
                    BlockedDate::firstOrCreate(
                        ['date' => $data['date']],
                        ['reason' => $data['reason'] ?? null]
                    );
                    Notification::make()
                        ->title('Fecha bloqueada')
                        ->success()
                        ->send();
                }),
        ];
    }
}
