<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Horario;
use App\Models\Laboratorio;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;

class ReservaCalendar extends FullCalendarWidget
{
    use InteractsWithForms;

    protected static ?string $heading = 'Calendario de Reservas';
    protected static bool $isLazy = false; // Para cargar datos automáticamente

    public ?int $selectedLaboratorio = null; // Laboratorio seleccionado

    public function mount(): void
    {
        $this->selectedLaboratorio = Laboratorio::first()?->id_laboratorio ?? null;
    }

    // Obtener horarios disponibles
    public function fetchEvents(array $fetchInfo = []): array
    {
        if (!$this->selectedLaboratorio) {
            return [];
        }

        // Validar que las claves necesarias existen
        if (!isset($fetchInfo['start']) || !isset($fetchInfo['end'])) {
            return [];
        }

        // Buscar los horarios disponibles en la tabla horarios
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
        ])->toArray();
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('selectedLaboratorio')
                ->label('Seleccionar Laboratorio')
                ->options(Laboratorio::pluck('nombre', 'id_laboratorio')->toArray())
                ->default($this->selectedLaboratorio)
                ->reactive()
                ->afterStateUpdated(fn() => $this->dispatch('refreshCalendar')),
        ];
    }

    // Reservar un horario disponible
    public function reserveLab(int $eventId): void
    {
        $horario = Horario::find($eventId);

        if (!$horario || $horario->is_available == 0) {
            return; // Evitar reservas duplicadas
        }

        // Registrar la reserva en la tabla reservas
        $reserva = Reserva::create([
            'id_horario' => $horario->id_horario,
            'id_usuario' => Auth::id(),
            'estado' => 'reservado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Cambiar el estado del horario a ocupado
        $horario->update(['is_available' => 0]);

        // Refrescar el calendario
        $this->dispatch('refreshCalendar');
    }

    public static function canView(): bool
    {
        // Ocultar el widget en el dashboard principal
        return !request()->routeIs('filament.admin.pages.dashboard');
    }
}