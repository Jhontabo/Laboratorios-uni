<div class="filament-calendar-widget">
    <div wire:ignore class="w-full">
        <div id="calendar-{{ $this->getId() }}" class="filament-fullcalendar h-[600px]"></div>
    </div>
</div>

@pushOnce('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
@endPushOnce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar-{{ $this->getId() }}');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay'
                },
                events: @json($this->getEvents()),
                locale: 'es',
                slotMinTime: '07:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                expandRows: true,
                slotEventOverlap: false
            });
            calendar.render();
        });
    </script>
@endPush

@pushOnce('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endPushOnce