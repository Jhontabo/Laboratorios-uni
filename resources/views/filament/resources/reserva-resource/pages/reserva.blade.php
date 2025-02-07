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
                events: '/ruta-para-cargar-eventos', // Ruta correcta para cargar los horarios
                selectable: true,
                
                eventClick: function(info) {
                    console.clear(); // ✅ Limpiar consola para que sea más fácil ver los datos
                    
                    console.log("Evento seleccionado:", info.event);
                    console.log("Propiedades extendidas:", info.event.extendedProps);

                    // Verificar si hay un horario disponible
                    if (!info.event.extendedProps || info.event.extendedProps.isAvailable === 0) {
                        console.warn("❌ No hay horario disponible en este espacio.");
                        Livewire.emit("setEventId", null); // Enviar null si no hay horario
                        alert("No hay horario disponible en este espacio.");
                        return;
                    }

                    // ✅ Si hay un horario disponible, enviamos el ID del evento
                    console.log("✅ ID del horario disponible:", info.event.id);
                    Livewire.emit("setEventId", info.event.id);
                }
            });

            calendar.render();
        });
    </script>
</x-filament-panels::page>