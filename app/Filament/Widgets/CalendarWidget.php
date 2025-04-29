<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Laboratory;
use Carbon\Carbon;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = 'Schedule Calendar';

    public Model|string|null $model = Schedule::class;

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

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'locale' => 'en',
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $labId = session()->get('lab');
        $query = Schedule::query();

        $query->whereBetween('start_at', [$fetchInfo['start'], $fetchInfo['end']]);

        if (!is_null($labId)) {
            $query->where('laboratory_id', $labId);
        }

        return $query->get()->map(function (Schedule $schedule) {
            return [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'start' => $schedule->start_at,
                'end' => $schedule->end_at,
                'color' => $schedule->color,
            ];
        })->toArray();
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(function (Schedule $record, Form $form, array $arguments) {
                    $form->fill([
                        'title' => $record->title,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'color' => $record->color,
                        'is_available' => $record->is_available,
                        'laboratory_id' => $record->laboratory_id,
                    ]);
                })
                ->action(function (Schedule $record, array $data) {
                    $record->update([
                        'title' => $data['title'],
                        'start_at' => $data['start_at'],
                        'end_at' => $data['end_at'],
                        'color' => $data['color'],
                        'is_available' => $data['is_available'],
                        'laboratory_id' => $data['laboratory_id'],
                    ]);
                }),
            DeleteAction::make(),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at' => $arguments['end'] ?? null,
                    ]);
                }),
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('General Information')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->label('Event Name')
                        ->placeholder('Enter event name'),

                    Textarea::make('description')
                        ->label('Description')
                        ->maxLength(500)
                        ->placeholder('e.g., Advanced programming class')
                        ->helperText('Maximum 500 characters allowed.'),
                ])
                ->columns(2),

            Section::make('Availability and Color')
                ->schema([
                    Toggle::make('is_available')
                        ->label('Available for Reservation')
                        ->onColor('success')
                        ->offColor('danger')
                        ->helperText('Toggle if this space is available for reservation.')
                        ->default(false),

                    ColorPicker::make('color')
                        ->label('Event Color')
                        ->helperText('Select a color for the event.'),

                    Select::make('laboratory_id')
                        ->label('Laboratory')
                        ->options(Laboratory::pluck('name', 'id')->toArray())
                        ->required(),
                ])
                ->columns(3),

            Section::make('Schedule')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DateTimePicker::make('start_at')
                                ->required()
                                ->label('Start Date and Time')
                                ->placeholder('Select start date and time')
                                ->displayFormat('d/m/Y H:i')
                                ->native(false)
                                ->minDate(Carbon::now())
                                ->helperText('Cannot select a past date.')
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state && Carbon::parse($state)->isPast()) {
                                        $set('start_at', null);
                                    }
                                }),

                            DateTimePicker::make('end_at')
                                ->required()
                                ->label('End Date and Time')
                                ->placeholder('Select end date and time')
                                ->displayFormat('d/m/Y H:i')
                                ->native(false)
                                ->minDate(Carbon::now())
                                ->helperText('Must be after the start date.')
                                ->afterStateUpdated(function ($state, callable $set, $get) {
                                    if ($state && Carbon::parse($state)->lessThan(Carbon::parse($get('start_at')))) {
                                        $set('end_at', null);
                                    }
                                }),
                        ]),
                ])
                ->columns(2),
        ];
    }
}
