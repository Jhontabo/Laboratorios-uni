<?php

namespace App\Filament\Resources\horarioResource\Pages;

use App\Filament\Resources\HorarioResource;
use Filament\Resources\Pages\Page;
use App\Filament\Widgets\CalendarWidget;


class Calendar extends Page
{
    protected static string $resource = HorarioResource::class;

    protected static string $view = 'filament.resources.reserva-resource.pages.calendar';

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }
}
