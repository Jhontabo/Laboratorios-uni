<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Laboratory;
use Filament\Resources\Pages\Page;

class ScheduleCalendar extends Page
{
    protected static string $resource = ScheduleResource::class;

    protected static string $view = 'filament.pages.calendar';
    protected static ?string $modelLabel = 'Horario';
    protected static ?string $pluralLabel = 'Horarios';

    public ?int $laboratoryId = null;

    public function mount()
    {
        $labParam = request()->query('laboratory');
        $this->laboratoryId = is_numeric($labParam) ? (int) $labParam : null;

        logger()->info('ScheduleCalendar mount', ['laboratory_id' => $this->laboratoryId]);
        session()->put('lab', $this->laboratoryId);
    }

    public function getFooterWidgets(): array
    {
        $selectedWidget = request()->query('widget', 'All');

        if ($selectedWidget === 'All') {
            return [CalendarWidget::class];
        }

        $laboratory = Laboratory::where('name', $selectedWidget)->first();

        if ($laboratory) {
            return [CalendarWidget::class]; // You can customize this part to load a specific widget if needed
        }

        return [CalendarWidget::class];
    }

    public function getDropdownOptions(): array
    {
        $laboratories = Laboratory::all()->pluck('name', 'id')->toArray();

        return [
            'All' => 'Todos',
        ] + $laboratories;
    }
}
