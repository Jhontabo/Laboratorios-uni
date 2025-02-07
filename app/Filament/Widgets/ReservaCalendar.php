<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class ReservaCalendar extends FullCalendarWidget
{
    use InteractsWithForms;

    protected static ?string $heading = 'Calendario de Reservas';
    protected static bool $isLazy = false; // Para cargar datos automáticamente

    public ?int $selectedLaboratorio = null; // Laboratorio seleccionado
    public ?int $eventId = null; // Variable para almacenar el ID del evento seleccionado

    protected $listeners = [
        'reserveLab' => 'reserveLab',
        'setEventId' => 'setEventId',
    ];

    public function mount(): void
    {
        $this->selectedLaboratorio = Laboratorio::first()?->id_laboratorio ?? null;
    }

    // Establecer el ID del evento cuando se hace clic en un horario
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
        logger()->info('Se ha seleccionado el evento:', ['eventId' => $this->eventId]);
    }

    // Obtener horarios disponibles
    public function fetchEvents(array $fetchInfo = []): array
    {
        if (!$this->selectedLaboratorio) {
            return [];
        }
    
        if (!isset($fetchInfo['start']) || !isset($fetchInfo['end'])) {
            return [];
        }
    
        $horarios = Horario::where('is_available', 1)
            ->where('id_laboratorio', $this->selectedLaboratorio)
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get();
    
        return $horarios->map(fn($horario) => [
            'id' => $horario->id_horario,
            'title' => 'Disponible - ' . $horario->title,
            'start' => $horario->start_at,
            'end' => $horario->end_at,
            'color' => '#00FF00',
            'extendedProps' => [
                'isAvailable' => $horario->is_available
            ],
        ])->toArray();
    }
    public function getFormSchema(): array
    {
        logger()->info('Valor de eventId antes de buscar horario:', ['eventId' => $this->eventId]);
    
        if (!$this->eventId) {
            logger()->error('No se ha seleccionado un evento para reservar.');
            
            if (!session()->has('notified')) {
                session()->flash('notified', true);
                Notification::make()
                    ->title('Horario no disponible')
                    ->body('No hay un horario disponible en este espacio.')
                    ->danger()
                    ->send();
            }
    
            return [];
        }
    
        $horario = Horario::find($this->eventId);
        logger()->info('Valor de $horario:', ['horario formulario' => $horario]);
    
        if (!$horario || $horario->is_available == 0) {
            logger()->error('El horario seleccionado no existe o ya fue reservado.');
            
            if (!session()->has('notified')) {
                session()->flash('notified', true);
                Notification::make()
                    ->title('Horario no disponible')
                    ->body('Este horario no está disponible.')
                    ->danger()
                    ->send();
            }
    
            return [];
        }
    
        return [
            Select::make('selectedLaboratorio')
                ->label('Seleccionar Laboratorio')
                ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
                ->default($this->selectedLaboratorio)
                ->reactive()
                ->afterStateUpdated(fn() => $this->dispatch('refreshCalendar')),
    
            \Filament\Forms\Components\DateTimePicker::make('start_at')
                ->label('Fecha de Inicio')
                ->default($horario->start_at)
                ->required(),
    
            \Filament\Forms\Components\DateTimePicker::make('end_at')
                ->label('Fecha de Fin')
                ->default($horario->end_at)
                ->required(),
        ];
    }

    public function reserveLab(): void
    {
        if (!$this->eventId) {
            Notification::make()
                ->title('Error')
                ->body('Debe seleccionar un evento válido para reservar.')
                ->danger()
                ->send();
            return;
        }

        $horario = Horario::find($this->eventId);
        logger()->info('Intentando reservar el horario:', ['horario' => $horario]);

        if (!$horario || $horario->is_available == 0) {
            Notification::make()
                ->title('Error de reserva')
                ->body('Este espacio no está disponible para reserva.')
                ->danger()
                ->send();
            return;
        }

        Reserva::create([
            'id_horario' => $horario->id_horario,
            'id_usuario' => Auth::id(),
            'estado' => 'reservado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $horario->update(['is_available' => 0]);

        Notification::make()
            ->title('Reserva exitosa')
            ->body('Reserva realizada con éxito.')
            ->success()
            ->send();

        $this->dispatch('refreshCalendar');
    }

    public static function canView(): bool
    {
        return !request()->routeIs('filament.admin.pages.dashboard');
    }
    

    public function onEventClick(array $event): void
    {
        logger()->info('Evento clickeado:', ['event' => $event]);

        if (!isset($event['id'])) {
            logger()->error('No se ha seleccionado un horario válido.');

            Notification::make()
                ->title('Horario no disponible')
                ->body('No hay un horario disponible en este espacio.')
                ->danger()
                ->send();

            return; // Salir sin hacer más acciones
        }

        // **Eliminar cualquier referencia a abrir el modal**
        $this->eventId = (int) $event['id'];
        logger()->info('ID del horario capturado:', ['eventId' => $this->eventId]);
    }
}