<x-filament-panels::page>
    <!-- Contenedor con flexbox para alinear el título y el dropdown -->
    <div class="flex items-center justify-between mb-4">
        <!-- Título -->
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Reserva de Laboratorios</h3>

        <!-- Dropdown para seleccionar laboratorio -->
        <form method="GET" action="{{ url()->current() }}">
            <select name="laboratorio" onchange="this.form.submit()"
                class="form-control border-gray-300 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                @foreach ($this->getLaboratorios() as $id => $nombre)
                    <option value="{{ $id }}"
                        {{ request()->query('laboratorio', 'none') == $id ? 'selected' : '' }}>
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Contenedor para el calendario -->
    <div id="calendar">

    
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                events: @json($this->fetchEvents()),
                selectable: true,
                select: function(info) {
                    alert('Seleccionaste: ' + info.startStr + ' hasta ' + info.endStr);
                }
            });
            calendar.render();
        });
    </script>
</x-filament-panels::page>