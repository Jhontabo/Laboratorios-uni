<?php

namespace App\Filament\Resources\ReservaResource\Pages;

use App\Filament\Resources\ReservaResource;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\CalendarWidget;

class CalendarioReserva extends Page
{
    protected static string $resource = ReservaResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.calendario-reserva';

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }
}
