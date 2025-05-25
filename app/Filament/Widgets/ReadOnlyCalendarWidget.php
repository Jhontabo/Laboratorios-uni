<?php

namespace App\Filament\Widgets;

use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class ReadOnlyCalendarWidget extends CalendarWidget
{

    public static function canView(): bool
    {
        // 1) Si estamos en la ruta del dashboard, no mostrar
        if (request()->routeIs('filament.pages.dashboard')) {
            return false;
        }

        // 2) En cualquier otra ruta, delegar a la implementación padre
        return parent::canView();
    }


    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }

    public function config(): array
    {
        return array_merge(parent::config(), [
            'selectable'   => false,
            'editable'     => false,
            'eventClick'   => null,
            'eventDrop'    => null,
            'eventResize'  => null,

            // Oculta domingos (0) y sábados (6), mostrando solo L-V
            'hiddenDays'   => [0, 6],

            // Opcional: asegura que la semana empiece en lunes
            'firstDay'     => 1,
        ]);
    }
}
