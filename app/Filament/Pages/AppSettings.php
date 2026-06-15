<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class AppSettings extends Page
{
    protected string $view = 'filament.pages.app-settings';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Ajustes generales';
    protected static ?string $title = 'Ajustes generales';
    protected static ?int $navigationSort = 11;

    public ?array $data = [];

    public function mount(): void
    {
        $s = AppSetting::allCached();

        $this->form->fill([
            // ── Correo ────────────────────────────────────────────────────
            'mail_host'         => $s['mail_host']         ?? config('mail.mailers.smtp.host', ''),
            'mail_port'         => $s['mail_port']         ?? config('mail.mailers.smtp.port', 587),
            'mail_username'     => $s['mail_username']     ?? config('mail.mailers.smtp.username', ''),
            'mail_password'     => $s['mail_password']     ?? '',
            'mail_encryption'   => $s['mail_encryption']   ?? config('mail.mailers.smtp.encryption', 'tls'),
            'mail_from_address' => $s['mail_from_address'] ?? config('mail.from.address', ''),
            'mail_from_name'    => $s['mail_from_name']    ?? config('mail.from.name', 'Kate Nails'),

            // ── Notificaciones ────────────────────────────────────────────
            'admin_notification_email' => $s['admin_notification_email'] ?? ($s['mail_from_address'] ?? ''),

            // ── WhatsApp / Twilio ─────────────────────────────────────────
            'twilio_sid'            => $s['twilio_sid']            ?? env('TWILIO_ACCOUNT_SID', ''),
            'twilio_token'          => $s['twilio_token']          ?? '',
            'twilio_whatsapp_from'  => $s['twilio_whatsapp_from']  ?? env('TWILIO_WHATSAPP_FROM', ''),

            // ── Lealtad ───────────────────────────────────────────────────
            'loyalty_enabled'   => ($s['loyalty_enabled']   ?? '0') === '1',
            'loyalty_threshold' => (int) ($s['loyalty_threshold'] ?? 7),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Correo electrónico ────────────────────────────────────
                Section::make('Correo electrónico')
                    ->description('Credenciales SMTP para el envío de campañas y notificaciones por email.')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        TextInput::make('mail_host')
                            ->label('Servidor SMTP')
                            ->placeholder('smtp.gmail.com')
                            ->required(),

                        TextInput::make('mail_port')
                            ->label('Puerto')
                            ->numeric()
                            ->default(587)
                            ->required(),

                        TextInput::make('mail_username')
                            ->label('Usuario / Email')
                            ->placeholder('tu@gmail.com')
                            ->required(),

                        TextInput::make('mail_password')
                            ->label('Contraseña / App Password')
                            ->password()
                            ->revealable(),

                        Select::make('mail_encryption')
                            ->label('Cifrado')
                            ->options([
                                'tls'  => 'TLS (recomendado)',
                                'ssl'  => 'SSL',
                                ''     => 'Ninguno',
                            ])
                            ->default('tls')
                            ->required(),

                        TextInput::make('mail_from_address')
                            ->label('Correo remitente')
                            ->email()
                            ->placeholder('noreply@katenails.com')
                            ->required(),

                        TextInput::make('mail_from_name')
                            ->label('Nombre remitente')
                            ->placeholder('Kate Nails')
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── Notificaciones ────────────────────────────────────────
                Section::make('Notificaciones')
                    ->description('Define a qué correo deseas recibir el aviso cada vez que una clienta registre una cita.')
                    ->icon('heroicon-o-bell-alert')
                    ->schema([
                        TextInput::make('admin_notification_email')
                            ->label('Correo del administrador')
                            ->email()
                            ->placeholder('admin@katenails.com')
                            ->helperText('Recibirás un correo con los detalles cada vez que se registre una nueva cita. Si lo dejas vacío, se usará el correo remitente.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // ── WhatsApp / Twilio ─────────────────────────────────────
                Section::make('WhatsApp — Twilio')
                    ->description('Credenciales de Twilio para el envío de mensajes de WhatsApp en campañas y notificaciones.')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextInput::make('twilio_sid')
                            ->label('Account SID')
                            ->placeholder('ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->required(),

                        TextInput::make('twilio_token')
                            ->label('Auth Token')
                            ->password()
                            ->revealable()
                            ->required(),

                        TextInput::make('twilio_whatsapp_from')
                            ->label('Número de WhatsApp')
                            ->placeholder('whatsapp:+14155238886')
                            ->helperText('Incluye el prefijo "whatsapp:" y el código de país.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── Programa de lealtad ───────────────────────────────────
                Section::make('Programa de lealtad')
                    ->description('Incentiva la fidelidad de tus clientas premiando su frecuencia de visitas.')
                    ->icon('heroicon-o-gift')
                    ->schema([
                        Toggle::make('loyalty_enabled')
                            ->label('Activar programa de lealtad')
                            ->helperText('Al activarlo, se notificará cuando una clienta alcance el número de servicios configurado.')
                            ->columnSpanFull(),

                        TextInput::make('loyalty_threshold')
                            ->label('Servicios para premio')
                            ->helperText('Número de servicios completados para que el siguiente sea gratis.')
                            ->numeric()
                            ->default(7)
                            ->minValue(2)
                            ->maxValue(100)
                            ->suffix('servicios'),
                    ])
                    ->columns(2)
                    ->collapsible(),

            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar ajustes')
                ->icon('heroicon-m-check')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        AppSetting::setMany($data);
        AppSetting::reloadConfig();

        Notification::make()
            ->title('Ajustes guardados correctamente')
            ->body('Los cambios se aplicarán de inmediato en campañas y notificaciones.')
            ->success()
            ->send();
    }
}
