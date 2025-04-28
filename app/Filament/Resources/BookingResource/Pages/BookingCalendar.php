<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Filament\Widgets\ReservationCalendar;
use App\Models\Reservation;
use App\Models\Laboratory;
use Filament\Resources\Pages\Page;

class BookingCalendar extends Page
{
    protected static string $resource = ReservationResource::class;

    protected static string $view = 'filament.resources.reservation-resource.pages.reservation';

    public ?int $laboratoryId = null;

    public function mount()
    {
        $this->laboratoryId = request()->query('laboratory');
        logger()->info('laboratoryId in BookingCalendar mount', ['event_data' => $this->laboratoryId]);
        session()->put('lab', $this->laboratoryId);
    }

    public function getFilteredReservations()
    {
        $query = Reservation::query();

        if ($this->laboratoryId) {
            $query->where('id_laboratory', $this->laboratoryId);
        }

        return $query->get();
    }

    public function getFooterWidgets(): array
    {
        return [
            ReservationCalendar::class,
        ];
    }

    public function getDropdownOptions(): array
    {
        $laboratories = Laboratory::all()->pluck('name', 'id_laboratory')->toArray();

        $options = [
            'All' => 'All',
        ] + $laboratories;

        return $options;
    }

    public function getLaboratories(): array
    {
        return Laboratory::pluck('name', 'id_laboratory')->toArray();
    }
}
