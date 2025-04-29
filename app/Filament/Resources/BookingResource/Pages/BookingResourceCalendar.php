<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Widgets\BookingCalendar;
use App\Models\Booking;
use App\Models\Reservation;
use App\Models\Laboratory;
use Filament\Resources\Pages\Page;

class BookingResourceCalendar extends Page
{
    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.pages.reservation';

    public ?int $laboratoryId = null;


    public static function canView(): bool
    {
        // Aquí puedes controlar la visibilidad de la página según los permisos del usuario
        return auth()->user()->hasRole('admin');  // Ejemplo de control de visibilidad basado en roles
    }

    public function mount()
    {
        $this->laboratoryId = request()->query('laboratory');
        logger()->info('laboratoryId in BookingCalendar mount', ['event_data' => $this->laboratoryId]);
        session()->put('lab', $this->laboratoryId);
    }

    public function getFilteredReservations()
    {
        $query = Booking::query();

        if ($this->laboratoryId) {
            $query->where('laboratory_id', $this->laboratoryId);
        }

        return $query->get();
    }

    public function getFooterWidgets(): array
    {
        return [
            BookingCalendar::class,
        ];
    }

    public function getDropdownOptions(): array
    {
        $laboratories = Laboratory::all()->pluck('name', 'id')->toArray();

        $options = [
            'All' => 'All',
        ] + $laboratories;

        return $options;
    }

    public function getLaboratories(): array
    {
        return Laboratory::pluck('name', 'id')->toArray();
    }

}
