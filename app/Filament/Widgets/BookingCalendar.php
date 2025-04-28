<?php

namespace App\Filament\Widgets;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Booking;
use App\Models\Laboratory;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class BookingCalendar extends FullCalendarWidget
{
    protected static ?string $heading = 'Booking Calendar';
    public Model|string|null $model = Booking::class;
    public ?Booking $booking = null;
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $laboratoryId = null;
    public ?string $userFirstName = null;
    public ?string $userLastName = null;
    public ?string $userEmail = null;
    public ?int $eventId = null;

    public function mount()
    {
        $this->laboratoryId = session()->get('lab');
    }

    // Method to decide if the widget should be visible
    public static function canView(): bool
    {
        $routesToHideWidget = [
            'filament.admin.pages.dashboard',
            'filament.student.pages.dashboard',
            'filament.teacher.pages.dashboard',
            'filament.laboratory.pages.dashboard'
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
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'selectable' => false,
        ];
    }

    // Method to fetch events from the database
    public function fetchEvents(array $fetchInfo): array
    {
        $this->laboratoryId = $this->laboratoryId ?? request()->query('laboratory');

        $query = Booking::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->when($this->laboratoryId, function ($query) {
                return $query->where('laboratory_id', $this->laboratoryId);
            })
            ->with('bookings');

        return $query->get()
            ->map(function (Booking $booking) {
                $reserved = $booking->bookings()->where('status', '!=', Booking::STATUS_REJECTED)->count() > 0;
                return [
                    'id' => $booking->id,
                    'title' => $reserved ? 'Reserved' : 'Available',
                    'start' => $booking->start_at,
                    'end' => $booking->end_at,
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
        if (!isset($event['id'])) {
            logger()->error('⚠️ No valid time slot selected.');
            return;
        }

        $booking = Booking::find($event['id']);

        if (!$booking) {
            logger()->error('❌ Selected booking not found.');
            return;
        }

        $existingBooking = Booking::where('schedule_id', $booking->schedule_id)
            ->where('status', '!=', Booking::STATUS_REJECTED)
            ->exists();

        if ($existingBooking) {
            Notification::make()
                ->title('Space already reserved')
                ->body('This space has already been reserved and cannot be booked again.')
                ->danger()
                ->send();
            return;
        }

        $this->eventId = $booking->id;
        $this->laboratoryId = $booking->laboratory_id;

        logger()->info('Selected event:', [
            'eventId' => $this->eventId,
            'laboratoryId' => $this->laboratoryId
        ]);

        $this->dispatch('refresh');
        usleep(300000); // Short pause to avoid conflicts
        $this->mountAction('reserve');
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
                    $this->reserveTimeSlot();
                }),
        ];
    }

    public function reserveTimeSlot()
    {
        try {
            $existingBooking = Booking::where('schedule_id', $this->eventId)
                ->where('status', '!=', Booking::STATUS_REJECTED)
                ->exists();

            if ($existingBooking) {
                Notification::make()
                    ->title('Error')
                    ->body('This space has already been reserved.')
                    ->danger()
                    ->send();
                return;
            }

            $bookingData = [
                'user_id' => auth()->id(),
                'schedule_id' => $this->eventId ?? null,
                'laboratory_id' => $this->laboratoryId ?? null,
                'user_first_name' => auth()->user()->name ?? 'No name',
                'user_last_name' => auth()->user()->last_name ?? 'No last name',
                'user_email' => auth()->user()->email ?? 'email@example.com',
                'status' => Booking::STATUS_PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $booking = Booking::create($bookingData);

            Notification::make()
                ->title('Booking created')
                ->body('The time slot has been successfully reserved.')
                ->success()
                ->send();

            $this->dispatch('refresh');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Could not complete the booking.')
                ->danger()
                ->send();
        }
    }

    public function getFormSchema(): array
    {
        $booking = Booking::find($this->eventId);

        if (!$booking) {
            logger()->error('❌ Booking not found with ID:', ['id' => $this->eventId]);
            return [];
        }

        return [
            Section::make('Schedule')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->default($booking->start_at)
                                ->label('Start Date and Time')
                                ->required(),

                            DateTimePicker::make('end_at')
                                ->default($booking->end_at)
                                ->label('End Date and Time')
                                ->required(),
                        ]),
                ]),

            Select::make('laboratory_id')
                ->label('Laboratory')
                ->options(Laboratory::pluck('name', 'id')->toArray())
                ->default($this->laboratoryId)
                ->disabled(),

            TextInput::make('schedule_id')
                ->default($booking->id)
                ->hidden(),

            TextInput::make('user_first_name')
                ->label('First Name')
                ->default($booking->user_first_name ?? auth()->user()->name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user_last_name')
                ->label('Last Name')
                ->default($booking->user_last_name ?? auth()->user()->last_name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user_email')
                ->label('Email')
                ->default($booking->user_email ?? auth()->user()->email ?? '')
                ->disabled()
                ->email()
                ->required(),
        ];
    }
}

