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

    // Propiedades del estado
    public ?string $start_at = null;
    public ?string $end_at = null;
    public ?int $laboratory_id = null;
    public ?int $eventId = null;
    public ?Booking $booking = null;

    public function mount(): void
    {
        $this->laboratory_id = session()->get('lab');
    }

    public static function canView(): bool
    {
        $hiddenRoutes = [
            'filament.admin.pages.dashboard',
            'filament.estudiante.pages.dashboard',
            'filament.docente.pages.dashboard',
            'filament.laboratorista.pages.dashboard'
        ];

        return !in_array(request()->route()->getName(), $hiddenRoutes);
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'slotDuration' => '00:30:00',
            'locale' => 'es',
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

        return Schedule::query()
            ->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']])
            ->when($this->laboratory_id, fn($query) => $query->where('laboratory_id', $this->laboratory_id))
            ->with(['Bookings' => function ($query) {
                $query->where('status', '!=', Booking::STATUS_REJECTED);
            }])
            ->get()
            ->map(function (Schedule $schedule) {
                $booking = $schedule->Bookings->first();

                if (!$booking) {
                    return [
                        'id' => $schedule->id,
                        'title' => 'Available',
                        'start' => $schedule->start_at,
                        'end' => $schedule->end_at,
                        'color' => '#28a745',
                        'textColor' => '#ffffff'
                    ];
                }

                // Configuración según estado
                $statusConfig = [
                    Booking::STATUS_PENDING => [
                        'title' => 'Pending Approval',
                        'color' => '#ffc107',
                        'textColor' => '#000000'
                    ],
                    Booking::STATUS_APPROVED => [
                        'title' => 'Reserved',
                        'color' => '#dc3545',
                        'textColor' => '#ffffff'
                    ],
                    Booking::STATUS_RESERVED => [
                        'title' => 'Confirmed',
                        'color' => '#6610f2',
                        'textColor' => '#ffffff'
                    ]
                ];

                $config = $statusConfig[$booking->status] ?? [
                    'title' => 'Booking',
                    'color' => '#6c757d',
                    'textColor' => '#ffffff'
                ];

                return [
                    'id' => $schedule->id,
                    'title' => $config['title'],
                    'start' => $schedule->start_at,
                    'end' => $schedule->end_at,
                    'color' => $config['color'],
                    'textColor' => $config['textColor'],
                    'extendedProps' => [
                        'status' => $booking->status
                    ]
                ];
            })
            ->toArray();
    }

    public function onEventClick(array $event): void
    {
        if (!isset($event['id'])) {
            $this->notifyError('No valid schedule selected');
            return;
        }

        $schedule = Schedule::find($event['id']);
        if (!$schedule) {
            $this->notifyError('Schedule not found');
            return;
        }

        // Verificar reservas aprobadas/reservadas
        $approvedExists = $schedule->Bookings()
            ->whereIn('status', [Booking::STATUS_APPROVED, Booking::STATUS_RESERVED])
            ->exists();

        if ($approvedExists) {
            $this->notifyError('This space is already reserved');
            return;
        }

        // Verificar si el usuario ya tiene una solicitud pendiente
        $userPending = $schedule->Bookings()
            ->where('user_id', auth()->id())
            ->where('status', Booking::STATUS_PENDING)
            ->exists();

        if ($userPending) {
            $this->notifyWarning('You already have a pending request for this time slot');
            return;
        }

        $this->prepareForReservation($schedule);
    }

    private function prepareForReservation(Schedule $schedule): void
    {
        $this->eventId = $schedule->id;
        $this->laboratory_id = $schedule->laboratory_id;
        $this->dispatch('refresh');
        usleep(300000);
        $this->mountAction('reserve');
    }

    protected function modalActions(): array
    {
        return [
            Action::make('reserve')
                ->label('Request Reservation')
                ->form($this->getFormSchema())
                ->action(function () {
                    $this->createBookingRequest();
                })
        ];
    }

    private function createBookingRequest(): void
    {
        try {
            Booking::create([
                'user_id' => auth()->id(),
                'schedule_id' => $this->eventId,
                'laboratory_id' => $this->laboratory_id,
                'first_name' => auth()->user()->name ?? 'No name',
                'last_name' => auth()->user()->last_name ?? 'No last name',
                'email' => auth()->user()->email ?? 'email@example.com',
                'status' => Booking::STATUS_PENDING,
            ]);

            $this->notifySuccess(
                'Request Submitted',
                'Your reservation request has been submitted for approval.'
            );

            $this->dispatch('refresh');
        } catch (\Exception $e) {
            $this->notifyError(
                'Error',
                'Could not submit the reservation request: ' . $e->getMessage()
            );
        }
    }

    public function getFormSchema(): array
    {
        $schedule = Schedule::find($this->eventId);
        if (!$schedule) {
            logger()->error('Schedule not found with ID:', ['id' => $this->eventId]);
            return [];
        }

        return [
            Section::make('Schedule Information')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->default($schedule->start_at)
                                ->label('Start Time')
                                ->required()
                                ->disabled(),
                            DateTimePicker::make('end_at')
                                ->default($schedule->end_at)
                                ->label('End Time')
                                ->required()
                                ->disabled(),
                        ]),
                ]),

            Select::make('laboratory_id')
                ->label('Laboratory')
                ->options(Laboratory::pluck('name', 'id')->toArray())
                ->default($this->laboratory_id)
                ->disabled(),

            TextInput::make('user.name')
                ->label('First Name')
                ->default(auth()->user()->name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user.last_name')
                ->label('Last Name')
                ->default(auth()->user()->last_name ?? '')
                ->disabled()
                ->required(),

            TextInput::make('user.email')
                ->label('Email')
                ->default(auth()->user()->email ?? '')
                ->disabled()
                ->email()
                ->required(),
        ];
    }

    private function notifySuccess(string $title, string $body): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->send();
    }

    private function notifyError(string $title, string $body = ''): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->send();
    }

    private function notifyWarning(string $title, string $body = ''): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->send();
    }
}
