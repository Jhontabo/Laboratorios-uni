<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReservaResource;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\CalendarReserva;
use App\Models\Laboratorio;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class ReservaCalendar extends Page
{
    protected static string $resource = ReservaResource::class;
    
    protected static string $view = 'filament.resources.reserva-resource.pages.reserva';

    public ?int $selectedLaboratorio = null; // Variable para el laboratorio seleccionado

    public function mount(): void
    {
        // Cargar por defecto el primer laboratorio disponible
        $this->selectedLaboratorio = Laboratorio::first()?->id_laboratorio ?? null;
    }

    // Obtener lista de laboratorios para el dropdown
    public function getDropdownOptions(): array
    {
        return Laboratorio::pluck('nombre', 'id_laboratorio')->toArray();
    }

    // Obtener eventos (reservas disponibles) en el calendario
    public function fetchEvents(array $fetchInfo = []): array
    {
        if (!$this->selectedLaboratorio) {
            return [];
        }
    
        // Asegurar que fetchInfo tiene los valores correctos
        if (!isset($fetchInfo['start']) || !isset($fetchInfo['end'])) {
            return [];
        }
    
        $reservas = Reserva::where('status', 'disponible')
            ->where('id_laboratorio', $this->selectedLaboratorio)
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->get();
    
        return $reservas->map(fn($reserva) => [
            'id' => $reserva->id,
            'title' => 'Disponible - ' . $reserva->title,
            'start' => $reserva->start_at,
            'end' => $reserva->end_at,
            'color' => '#00FF00',
        ])->toArray();
    }

    // Permitir a los usuarios hacer una reserva al hacer clic en un espacio disponible
    public function reserveLab(int $eventId): void
    {
        $reserva = Reserva::find($eventId);

        if (!$reserva || $reserva->status !== 'disponible') {
            return; // Evita reservas duplicadas o espacios ya ocupados
        }

        $reserva->update([
            'status' => 'reservado',
            'user_id' => Auth::id(), // Asigna la reserva al usuario autenticado
        ]);

        $this->dispatch('refreshCalendar'); // Recargar el calendario después de la reserva
    }

    public function getLaboratorios()
{
    return \App\Models\Laboratorio::pluck('nombre', 'id_laboratorio')->toArray();
}
}