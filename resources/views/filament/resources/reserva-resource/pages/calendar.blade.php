<x-filament-panels::page>
    <!-- Contenedor con flexbox para alinear el título y el dropdown -->
    <div class="flex items-center justify-between mb-4">
        <!-- Título -->
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Elegir horariosas</h3>

        <!-- Dropdown para seleccionar el widget -->
        <form method="GET" action="{{ url()->current() }}">
            <select name="widget" onchange="this.form.submit()"
                class="form-control border-gray-300 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                @foreach ($this->getDropdownOptions() as $key => $label)
                    <option value="{{ $key }}"
                        {{ request()->query('widget', 'none') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</x-filament-panels::page>
