<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class EditProfile extends Page
{
    protected string $view = 'filament.pages.edit-profile';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Editar perfil';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name'  => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información personal')
                    ->description('Actualiza tu nombre y correo electrónico de acceso.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(table: 'users', column: 'email', ignorable: auth()->user()),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->update($data);

        Notification::make()
            ->title('Perfil actualizado correctamente')
            ->success()
            ->send();
    }
}
