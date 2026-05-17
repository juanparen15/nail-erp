<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.change-password';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Cambiar contraseña';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Actualizar contraseña')
                    ->description('Ingresa tu contraseña actual y la nueva para actualizarla.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Contraseña actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->currentPassword(),

                        Forms\Components\TextInput::make('password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->confirmed(),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar nueva contraseña')
                            ->password()
                            ->revealable()
                            ->required(),
                    ])
                    ->columns(1)
                    ->maxWidth('lg'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);

        Notification::make()
            ->title('Contraseña actualizada correctamente')
            ->success()
            ->send();

        $this->form->fill();
    }
}
