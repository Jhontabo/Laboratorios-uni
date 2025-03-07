<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReservaResource;

use App\Filament\Widgets\ReservaCalendar;


use App\Models\Laboratorio;

class RecursoCalendar extends Page
{
    protected static string $resource = ReservaResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.reserva';


    public ?int $id_laboratorio = null;

    public function mount()
    {
        $this->id_laboratorio = request()->query('laboratorio');
        logger()->info('id laboratorio RecursoCalendar mount', ['event_data' =>  $this->id_laboratorio]);
        session()->put('lab', $this->id_laboratorio);
    }

        public function getFilteredReservas()
        {
            // Si tenemos $id_laboratorio, filtramos. De lo contrario, mostramos todo.
            $query = Reserva::query();
    
            if ($this->id_laboratorio) {
                $query->where('id_laboratorio', $this->id_laboratorio);
            }
    
            return $query->get();
        }
    

        public function getFooterWidgets(): array
        {
            //logger()->info('id laboratorio RecursoCalendar getFooterWidgets', ['event_data' =>  $this->id_laboratorio]);
            return [
                ReservaCalendar::class,
            ];
        }
        

        public function getDropdownOptions(): array
        {
            // Obtenemos los laboratorios y usamos el id como clave
            $laboratorios = Laboratorio::all()->pluck('nombre', 'id_laboratorio')->toArray();
        
            // Agregamos las opciones adicionales
            $options = [
                'Todos' => 'Todos',
                'Reserva' => 'Reserva',
            ] + $laboratorios;
        
            return $options;
        }
    /**
     * Método para obtener la lista de laboratorios
     */
    public function getLaboratorios(): array
    {
        return Laboratorio::pluck('nombre', 'id_laboratorio')->toArray();
    }
}
