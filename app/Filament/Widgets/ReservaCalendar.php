<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Models\Laboratorio;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reserva;
use Filament\Notifications\Notification;

class ReservaCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Calendario de Reservas';
    public Model | string | null $model = Horario::class;
    public ?Reserva $reserva = null; // Agregar esta línea
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $id_laboratorio = null;
    public ?string $nombre_usuario = null;
    public ?string $apellido_usuario = null;
    public ?string $correo_usuario = null;
    public ?int $eventId = null;


    // Método para decidir si el widget debe ser visible
    public static function canView(): bool
    {
        return !request()->routeIs('filament.admin.pages.dashboard'); // Ocultar en el dashboard principal
    }
    // Configuración de FullCalendar
    public function config(): array
    {
        return [
            
            'firstDay' => 1, // Inicia la semana en lunes+
            'slotMinTime' => '06:00:00', // Hora mínima visible
            'slotMaxTime' => '22:00:00', // Hora máxima visible
            'slotDuration' => '00:30:00', // Intervalo de tiempo de cada bloque
            'locale' => 'es',
            'initialView' => 'timeGridWeek', // Vista semanal predeterminada
            
            'selectable' => false,
        ];
    }


    // Método para obtener eventos de la base de datos
    public function fetchEvents(array $fetchInfo): array
    {
        $query = Horario::query()
            ->where('is_available', true)
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']]);

        return $query->get()
            ->map(function (Horario $horario) {
                return [
                    'id' => $horario->id_horario,
                    'title' => $horario->title,
                    'start' => $horario->start_at,
                    'end' => $horario->end_at,
                    'color' => $horario->color ?? '#28a745',
                ];
            })
            ->toArray();
    }



    protected function headerActions(): array
    {
        return [];
    }

    public function onEventClick(array $event): void
{
    logger()->info('🔔 Evento clickeado:', ['event_data' => json_encode($event)]);

    if (!isset($event['id'])) {
        logger()->error('⚠️ No se ha seleccionado un horario válido.');
        return;
    }

    $horario = Horario::find($event['id']);

    if (!$horario) {
        logger()->error('❌ No se encontró el horario seleccionado.');
        return;
    }

    $this->eventId = $horario->id_horario;
    $this->id_laboratorio = $horario->id_laboratorio;

    logger()->info('Evento seleccionado:', [
        'eventId' => $this->eventId,
        'id_laboratorio' => $this->id_laboratorio
    ]);

    $this->dispatch('refresh');
    usleep(300000);
    $this->mountAction('reservar');
}

 
    
    protected function modalActions(): array
    {
        //logger()->info('🛠 Ejecutando modalActions() correctamente'); // 🔥 Log para verificar
    
        return [
            Action::make('reservar')
                ->label('Reservar')
                ->button()
                ->color('primary')
                ->form(fn () => $this->getFormSchema()) 
                ->action(function () {
                    $this->reservarHorario();
                }),
        ];
    }

    public function reservarHorario()
{
    //logger()->info('🔔 Reservando horario...');

    //logger()->info('ID Horario:', ['eventId' => $this->eventId]);
    //logger()->info('ID Laboratorio:', ['id_laboratorio' => $this->id_laboratorio]);

    try {
        // Si `getState()` está fallando, usa directamente `$this->eventId`
        $datosReserva = [
            'id_usuario' => auth()->id(),
            'id_horario' => $this->eventId ?? null,
            'id_laboratorio' => $this->id_laboratorio ?? null,
            'nombre_usuario' => auth()->user()->name ?? 'Sin nombre',
            'apellido_usuario' => auth()->user()->apellido ?? 'Sin apellido',
            'correo_usuario' => auth()->user()->email ?? 'correo@ejemplo.com',
            'estado' => Reserva::ESTADO_PENDIENTE,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        logger()->info('📝 Datos que se intentan insertar en reservas:', $datosReserva);

        // Crear la reserva
        $reserva = Reserva::create($datosReserva);

        logger()->info('✅ Reserva creada con éxito:', $reserva->toArray());

        Notification::make()
            ->title('Reserva creada')
            ->body('Se ha reservado el horario con éxito.')
            ->success()
            ->send();

        $this->dispatch('refresh');

    } catch (\Exception $e) {
        //logger()->error('❌ Error al intentar crear la reserva:', ['error' => $e->getMessage()]);

        Notification::make()
            ->title('Error')
            ->body('No se pudo completar la reserva.')
            ->danger()
            ->send();
    }
}


    public function getFormSchema(): array
{
    //logger()->info('📌 getFormSchema() ha sido ejecutado');
    $horario = Horario::find($this->eventId);
    
    if (!$horario) {
        logger()->error('❌ No se encontró el horario con ID:', ['id' => $this->eventId]);
        return [];
    }

    $this->reserva = Reserva::where('id_horario', $horario->id_horario)->first();

    return [
        Section::make('Horario')
            ->schema([
                Grid::make(2)
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->default($horario->start_at)
                           
                            ->label('Fecha y hora de inicio')
                            ->required(),

                        DateTimePicker::make('end_at')
                            ->default($horario->end_at)
                            
                            ->label('Fecha y hora de fin')
                            ->required(),
                    ]),
            ]),

        Select::make('id_laboratorio')
            ->label('Laboratorio')
            ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
            ->default($horario->id_laboratorio)
            ->disabled(),

        TextInput::make('id_horario')
            ->default($horario->id_horario)
            ->hidden(),

        TextInput::make('nombre_usuario')
            ->label('Nombre')
            ->default($this->reserva?->nombre_usuario ?? auth()->user()->name ?? '')
            ->disabled()
            ->required(),

        TextInput::make('apellido_usuario')
            ->label('Apellido')
            ->default($this->reserva?->apellido_usuario ?? auth()->user()->apellido ?? '')
            ->disabled()
            ->required(),

        TextInput::make('correo_usuario')
            ->label('Correo Electrónico')
            ->default($this->reserva?->correo_usuario ?? auth()->user()->email ?? '')
            ->disabled()
            ->email()
            ->required(),
    ];
}
}                