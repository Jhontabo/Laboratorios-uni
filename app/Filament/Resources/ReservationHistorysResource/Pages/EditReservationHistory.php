<?php

namespace App\Filament\Resources\ReservationHistorysResource\Pages;

use App\Filament\Resources\ReservationHistorysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReservationHistory extends EditRecord
{
    protected static string $resource = ReservationHistorysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
