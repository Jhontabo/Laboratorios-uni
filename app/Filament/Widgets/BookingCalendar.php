<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class BookingCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Booking Calendar';
    public Model | string | null $model = Booking::class;
    public ?Booking $booking = null;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $laboratory_id = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?int $eventId = null;

    public function mount()
    {
        $this->laboratory_id = session()->get('lab');
    }

    public static function canView(): bool
    {
        $routesToHideWidget = [
            'filament.admin.pages.dashboard',
            'filament.student.pages.dashboard',
            'filament.teacher.pages.dashboard',
            'filament.labtech.pages.dashboard',
        ];

        return !in_array(request()->route()->getName(), $routesToHideWidget);
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'slotDuration' => '00:30:00',
            'locale' => 'en',
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'selectable' => false,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $this->laboratory_id = $this->laboratory_id ?? request()->query('laboratory');

        $query = Schedule::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->when($this->laboratory_id, function ($query) {
                return $query->where('laboratory_id', $this->laboratory_id);
            })
            ->with('bookings');

        return $query->get()
            ->map(function (Schedule $schedule) {
                $isBooked = $schedule->bookings()->where('status', '!=', Booking::STATUS_REJECTED)->count() > 0;
                return [
                    'id' => $schedule->id,
                    'title' => $isBooked ? 'Booked' : 'Available',
                    'start' => $schedule->start_at,
                    'end' => $schedule->end_at,
                    'color' => $isBooked ? '#dc3545' : '#28a745',
                ];
            })
            ->toArray();
    }

    protected function headerActions(): array
    {
        return [];
    }

    public function onEventClick(array $event): void
    {
        if (!isset($event['id'])) {
            logger()->error('Invalid event selected.');
            return;
        }

        $schedule = Schedule::find($event['id']);

        if (!$schedule) {
            logger()->error('Schedule not found.');
            return;
        }

        $existingBooking = Booking::where('schedule_id', $schedule->id)
            ->where('status', '!=', Booking::STATUS_REJECTED)
            ->exists();

        if ($existingBooking) {
            Notification::make()
                ->title('Already Booked')
                ->body('This time slot has already been booked.')
                ->danger()
                ->send();
            return;
        }

        $this->eventId = $schedule->id;
        $this->laboratory_id = $schedule->laboratory_id;

        $this->dispatch('refresh');
        usleep(300000);
        $this->mountAction('book');
    }

    protected function modalActions(): array
    {
        return [
            Action::make('book')
                ->label('Book')
                ->button()
                ->color('primary')
                ->form(fn() => $this->getFormSchema())
                ->action(function () {
                    $this->bookSchedule();
                }),
        ];
    }

    public function bookSchedule()
    {
        try {
            $existingBooking = Booking::where('schedule_id', $this->eventId)
                ->where('status', '!=', Booking::STATUS_REJECTED)
                ->exists();

            if ($existingBooking) {
                Notification::make()
                    ->title('Error')
                    ->body('This slot has already been booked.')
                    ->danger()
                    ->send();
                return;
            }

            $bookingData = [
                'user_id' => auth()->id(),
                'schedule_id' => $this->eventId,
                'laboratory_id' => $this->laboratory_id,
                'first_name' => auth()->user()->name ?? 'No name',
                'last_name' => auth()->user()->last_name ?? 'No last name',
                'email' => auth()->user()->email ?? 'email@example.com',
                'status' => Booking::STATUS_PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $booking = Booking::create($bookingData);

            Notification::make()
                ->title('Booking Created')
                ->body('The booking was created successfully.')
                ->success()
                ->send();

            $this->dispatch('refresh');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to complete the booking.')
                ->danger()
                ->send();
        }
    }

    public function getFormSchema(): array
    {
        $schedule = Schedule::find($this->eventId);

        if (!$schedule) {
            logger()->error('Schedule not found.', ['id' => $this->eventId]);
            return [];
        }

        $this->booking = Booking::where('schedule_id', $schedule->id)->first();

        return [
            Section::make('Schedule Details')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->default($schedule->start_at)
                                ->label('Start Date and Time')
                                ->required(),

                            DateTimePicker::make('end_at')
                                ->default($schedule->end_at)
                                ->label('End Date and Time')
                                ->required(),
                        ]),
                ]),

            Select::make('laboratory_id')
                ->label('Laboratory')
                ->options(Laboratory::pluck('name', 'id')->toArray())
                ->default($this->laboratory_id)
                ->disabled(),

            TextInput::make('schedule_id')
                ->default($schedule->id)
                ->hidden(),

            TextInput::make('first_name')
                ->label('First Name')
                ->default($this->booking?->first_name ?? auth()->user()->name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('last_name')
                ->label('Last Name')
                ->default($this->booking?->last_name ?? auth()->user()->last_name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->default($this->booking?->email ?? auth()->user()->email ?? '')
                ->disabled()
                ->email()
                ->required(),
        ];
    }
}

