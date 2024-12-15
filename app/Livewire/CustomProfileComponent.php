<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

use Illuminate\Support\Facades\Auth;

use App\Models\User; // Importa el modelo User
use Filament\Notifications\Notification;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class CustomProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        // Obtén los datos del usuario autenticado
        $user = Auth::user();

        // Prellena los campos con los datos del usuario
        $this->form->fill([
            'telefono' => $user->telefono, // Campo de teléfono en la tabla users
            'direccion' => $user->direccion, // Campo de dirección en la tabla users
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de contacto')
                    ->aside()
                    ->description('Actualiza tu número telefónico y dirección de residencia.')
                    ->schema([
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->placeholder('Ingresa tu número telefónico')
                            ->required()
                            ->maxLength(15),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Ingresa tu dirección de residencia')
                            ->required()
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // Obtén el estado del formulario
        $data = $this->form->getState();
        /** @var \App\Models\User $user */
        // Guarda los datos en la tabla users para el usuario autenticado
        $user = Auth::user();
        $user->update([
            'telefono' => $data['telefono'], // Actualiza el campo teléfono
            'direccion' => $data['direccion'], // Actualiza el campo dirección
        ]);

        // Enviar una notificación de éxito con Filament
        Notification::make()
            ->title('Información actualizada')
            ->success()
            ->body('Tu número de teléfono y dirección han sido actualizados correctamente.')
            ->send();
    }

    public function render(): View
    {
        return view('livewire.custom-profile-component');
    }
}
