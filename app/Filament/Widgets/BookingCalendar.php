<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Schedule;
use App\Models\Laboratory;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class BookingCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Reservation Calendar';
    public Model|string|null $model = Schedule::class;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $laboratory_id = null;
    public ?string $user_first_name = null;
    public ?string $user_last_name = null;
    public ?string $user_email = null;
    public ?int $eventId = null;

    public function mount()
    {
        $this->laboratory_id = session()->get('lab');
        //logger()->info('from BookingCalendar mount', ['event_data' =>  $this->laboratory_id]);
    }

    // Method to decide if the widget should be visible
    public static function canView(): bool
    {
        $routesToHideWidget = [
            'filament.admin.pages.dashboard',
            'filament.estudiante.pages.dashboard',
            'filament.docente.pages.dashboard',
            'filament.laboratorista.pages.dashboard'
        ];

        return !in_array(request()->route()->getName(), $routesToHideWidget);
    }

    // FullCalendar configuration
    public function config(): array
    {
        return [
            'firstDay' => 1,
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'slotDuration' => '00:30:00',
            'locale' => 'en',
            'initialView' => 'timeGridWeek', // Default weekly view
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay', // View options
            ],
            'selectable' => false,
        ];
    }

    // Method to fetch events from the database
    public function fetchEvents(array $fetchInfo): array
    {
        $this->laboratory_id = $this->laboratory_id ?? request()->query('laboratory');


        $query = Schedule::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->when($this->laboratory_id, function ($query) {
                return $query->where('laboratory_id', $this->laboratory_id);
            })
            ->with('Bookings'); // Aquí cambiamos 'reservations' por 'Bookings'

        return $query->get()
            ->map(function (Schedule $schedule) {
                $reserved = $schedule->Bookings()->where('status', '!=', Booking::STATUS_REJECTED)->count() > 0;
                return [
                    'id' => $schedule->id,
                    'title' => $reserved ? 'Reserved' : 'Available',
                    'start' => $schedule->start_at,
                    'end' => $schedule->end_at,
                    'color' => $reserved ? '#dc3545' : '#28a745',
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
        //logger()->info('🔔 Event clicked:', ['event_data' => json_encode($event)]);

        if (!isset($event['id'])) {
            logger()->error('⚠️ No valid schedule selected.');
            return;
        }

        $schedule = Schedule::find($event['id']);

        if (!$schedule) {
            logger()->error('❌ Schedule not found.');
            return;
        }

        // Check if the schedule is already reserved before opening the modal
        $existingBooking = $schedule->Bookings()->where('status', '!=', Booking::STATUS_REJECTED)->exists();

        if ($existingBooking) {
            Notification::make()
                ->title('Space already reserved')
                ->body('This space has already been reserved and cannot be reserved again.')
                ->danger()
                ->send();
            return; // Stops execution and doesn't open the modal
        }

        // If the schedule is available, assign values and open the modal
        $this->eventId = $schedule->id;
        $this->laboratory_id = $schedule->laboratory_id;

        logger()->info('Event selected:', [
            'eventId' => $this->eventId,
            'laboratory_id' => $this->laboratory_id
        ]);

        $this->dispatch('refresh'); // Refresh the view if necessary
        usleep(300000); // Short pause to avoid conflicts
        $this->mountAction('reserve'); // Opens the modal only if it's available
    }

    protected function modalActions(): array
    {

        return [
            Action::make('reserve')
                ->label('Reserve')
                ->button()
                ->color('primary')
                ->form(fn() => $this->getFormSchema())
                ->action(function () {
                    $this->reserveSchedule();
                }),
        ];
    }



    public function reserveSchedule()
    {

        try {
            // Check if the schedule is already reserved
            $existingBooking = Booking::where('schedule_id', $this->eventId)
                ->where('status', '!=', Booking::STATUS_REJECTED) // Ignore rejected bookings
                ->exists();

            if ($existingBooking) {
                Notification::make()
                    ->title('Error')
                    ->body('This space is already reserved.')
                    ->danger()
                    ->send();
                return;
            }

            $bookingData = [
                'user_id' => auth()->id(),
                'schedule_id' => $this->eventId ?? null,
                'laboratory_id' => $this->laboratory_id ?? null,
                'first_name' => auth()->user()->name ?? 'No name',
                'last_name' => auth()->user()->last_name ?? 'No last name',
                'email' => auth()->user()->email ?? 'email@example.com',
                'status' => Booking::STATUS_PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Create the booking
            $booking = Booking::create($bookingData);

            Notification::make()
                ->title('Booking created')
                ->body('The schedule has been successfully reserved.')
                ->success()
                ->send();

            $this->dispatch('refresh');
        } catch (\Exception $e) {
            // Log the error and show it in the notification
            logger()->error('❌ Error creating booking:', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);

            Notification::make()
                ->title('Error')
                ->body('Could not complete the booking. Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }


    public function getFormSchema(): array
    {
        //logger()->info('📌 getFormSchema() has been executed');
        $schedule = Schedule::find($this->eventId);

        if (!$schedule) {
            logger()->error('❌ Schedule not found with ID:', ['id' => $this->eventId]);
            return [];
        }

        $this->Booking = Booking::where('schedule_id', $schedule->id_schedule)->first();

        return [
            Section::make('Schedule')
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
                ->default($schedule->id_schedule)
                ->hidden(),

            TextInput::make('user.name')
                ->label('First Name')
                ->default($this->booking?->first_name ?? auth()->user()->name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user.last_name')
                ->label('Last Name')
                ->default($this->booking?->last_name ?? auth()->user()->last_name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user.email')
                ->label('Email')
                ->default($this->booking?->email ?? auth()->user()->email ?? '')
                ->disabled()
                ->email()
                ->required(),
        ];
    }
}
