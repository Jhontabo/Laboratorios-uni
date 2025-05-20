<?php

namespace App\Filament\Resources\ReservationHistorysResource\Pages;

use Filament\Resources\Pages\ViewRecord;

class ViewReservationHistory extends ViewRecord
{
    protected static string $resource = \App\Filament\Resources\ReservationHistorysResource::class;

    protected static string $view = 'filament.pages.view-reservation-history';
}
