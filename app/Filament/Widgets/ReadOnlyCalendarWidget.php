<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReadOnlyCalendarWidget extends CalendarWidget
{
  // Se permite inyectar desde @livewire(...) o dejar nulo
  public ?int $laboratory = null;

  public static function canView(): bool
  {
    if (request()->routeIs('filament.pages.dashboard')) {
      return false;
    }

    return parent::canView();
  }

  protected function headerActions(): array
  {
    return [];
  }

  protected function modalActions(): array
  {
    return [];
  }

  public function config(): array
  {
    return array_merge(parent::config(), [
      'selectable'   => false,
      'editable'     => false,
      'eventClick'   => null,
      'eventDrop'    => null,
      'eventResize'  => null,
      'hiddenDays'   => [0, 6],
      'firstDay'     => 1,
    ]);
  }

  public function getEvents(array $fetchInfo = []): array
  {
    // 1) Prioridad: propiedad pública (inyectada desde @livewire)
    // 2) Luego: parámetro en la URL ?laboratory=ID
    // 3) Luego: laboratorio del usuario autenticado
    $labFromQuery = is_numeric(request()->query('laboratory')) ? (int) request()->query('laboratory') : null;
    $effectiveLab = $this->laboratory ?? $labFromQuery ?? (Auth::user()->laboratory_id ?? null);

    // Logging para depuración — revisa storage/logs/laravel.log
    Log::info('ReadOnlyCalendarWidget: effective laboratory', [
      'property_laboratory' => $this->laboratory,
      'query_laboratory' => $labFromQuery,
      'effective' => $effectiveLab,
    ]);

    $query = Schedule::query()->where('type', 'unstructured');

    if (!empty($effectiveLab)) {
      $query->where('laboratory_id', $effectiveLab);
    }

    $events = $query->with('laboratory')->get();

    Log::info('ReadOnlyCalendarWidget: events count', ['count' => $events->count()]);

    return $events->map(fn(Schedule $schedule) => [
      'id'    => $schedule->id,
      'title' => $schedule->title ?? ($schedule->laboratory->name ?? '—'),
      'start' => $schedule->start_at,
      'end'   => $schedule->end_at,
    ])->toArray();
  }
}
