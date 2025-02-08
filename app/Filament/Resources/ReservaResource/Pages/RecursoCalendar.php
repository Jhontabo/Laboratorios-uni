<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReservaResource;
use App\Filament\Widgets\ReservaCalendar as ReservaCalendarWidget;
use App\Models\Laboratorio;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class RecursoCalendar extends Page
{
    protected static string $resource = ReservaResource::class;
    
    protected static string $view = 'filament.resources.reserva-resource.pages.reserva';

    public ?int $selectedLaboratorio = null; // Variable para el laboratorio seleccionado

    public function mount(): void
    {
        // Cargar por defecto el primer laboratorio disponible
        $this->selectedLaboratorio = Laboratorio::first()?->id_laboratorio ?? null;
    }

    
    // Incluir widgets en la página
    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\ReservaCalendar::class, 
        ];
    }


    public function getLaboratorios()
    {
        return \App\Models\Laboratorio::pluck('nombre', 'id_laboratorio')->toArray();
    }
}