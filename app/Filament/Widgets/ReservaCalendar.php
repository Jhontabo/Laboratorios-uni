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
    public ?Reserva $reserva = null;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $id_laboratorio = null;
    public ?string $nombre_usuario = null;
    public ?string $apellido_usuario = null;
    public ?string $correo_usuario = null;
    public ?int $eventId = null;

    public function mount()
    {

        $this->id_laboratorio = session()->get('lab');
        //logger()->info('desde ReservaCalendar mount', ['event_data' =>  $this->id_laboratorio]);
    }



    // Método para decidir si el widget debe ser visible
    public static function canView(): bool
    {

        $routesToHideWidget = [
            'filament.admin.pages.dashboard',
            'filament.estudiante.pages.dashboard',
            'filament.docente.pages.dashboard',
            'filament.laboratorista.pages.dashboard'

        ];


        return !in_array(request()->route()->getName(), $routesToHideWidget);
    }

    // Configuración de FullCalendar
    public function config(): array
    {
        return [

            'firstDay' => 1,
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'slotDuration' => '00:30:00',
            'locale' => 'es',
            'initialView' => 'timeGridWeek', // Vista semanal predeterminada
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay', // Opciones de vista
            ],
            'selectable' => false,
        ];
    }


    // Método para obtener eventos de la base de datos
    public function fetchEvents(array $fetchInfo): array
    {

        $this->id_laboratorio = $this->id_laboratorio ?? request()->query('laboratorio');

        //logger()->info('id laboratorio', ['event_data' =>  $this->id_laboratorio]);

        $query = Horario::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->when($this->id_laboratorio, function ($query) { // Reemplazar $laboratorioId por $this->id_laboratorio
                return $query->where('id_laboratorio', $this->id_laboratorio);
            })
            ->with('reservas'); // Asegurar que se carga la relación reservas

        return $query->get()
            ->map(function (Horario $horario) {
                $reservado = $horario->reservas()->where('estado', '!=', Reserva::ESTADO_RECHAZADA)->count() > 0;
                return [
                    'id' => $horario->id_horario,
                    'title' => $reservado ? 'Reservado' : 'Disponible',
                    'start' => $horario->start_at,
                    'end' => $horario->end_at,
                    'color' => $reservado ? '#dc3545' : '#28a745',
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
        //logger()->info('🔔 Evento clickeado:', ['event_data' => json_encode($event)]);

        if (!isset($event['id'])) {
            logger()->error('⚠️ No se ha seleccionado un horario válido.');
            return;
        }

        $horario = Horario::find($event['id']);

        if (!$horario) {
            logger()->error('❌ No se encontró el horario seleccionado.');
            return;
        }

        // Verificar si el horario ya está reservado antes de abrir el modal
        $reservaExistente = Reserva::where('id_horario', $horario->id_horario)
            ->where('estado', '!=', Reserva::ESTADO_RECHAZADA)
            ->exists();

        if ($reservaExistente) {
            Notification::make()
                ->title('Espacio ya reservado')
                ->body('Este espacio ya ha sido reservado y no se puede volver a reservar.')
                ->danger()
                ->send();
            return; // Detiene la ejecución y no abre el modal
        }

        // Si el horario está disponible, asignar valores y abrir el modal
        $this->eventId = $horario->id_horario;
        $this->id_laboratorio = $horario->id_laboratorio;

        logger()->info('Evento seleccionado:', [
            'eventId' => $this->eventId,
            'id_laboratorio' => $this->id_laboratorio
        ]);

        $this->dispatch('refresh'); // Refresca la vista si es necesario
        usleep(300000); // Pausa corta para evitar conflictos
        $this->mountAction('reservar'); // Abre el modal solo si está disponible
    }



    protected function modalActions(): array
    {
        //logger()->info('🛠 Ejecutando modalActions() correctamente'); // 🔥 Log para verificar

        return [
            Action::make('reservar')
                ->label('Reservar')
                ->button()
                ->color('primary')
                ->form(fn() => $this->getFormSchema())
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
            // Verificar si el horario ya está reservado
            $reservaExistente = Reserva::where('id_horario', $this->eventId)
                ->where('estado', '!=', Reserva::ESTADO_RECHAZADA) // Ignora reservas rechazadas
                ->exists();

            if ($reservaExistente) {
                Notification::make()
                    ->title('Error')
                    ->body('Este espacio ya está reservado.')
                    ->danger()
                    ->send();
                return;
            }

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

            //logger()->info('📝 Datos que se intentan insertar en reservas:', $datosReserva);

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
                ->default($this->id_laboratorio)
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
