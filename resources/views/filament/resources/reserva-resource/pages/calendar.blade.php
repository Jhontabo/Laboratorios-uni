<x-filament-panels::page>
    <!-- Dropdown para seleccionar el widget -->
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ url()->current() }}">
            <select name="widget" onchange="this.form.submit()" class="form-control border-gray-300 rounded-md shadow-sm">
                @foreach ($this->getDropdownOptions() as $key => $label)
                    <option value="{{ $key }}"
                        {{ request()->query('widget', 'none') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Renderizar solo el widget seleccionado -->
    <div>
        @if (count($this->getHeaderWidgets()) > 0)
            @foreach ($this->getHeaderWidgets() as $widget)
                @widget($widget)
            @endforeach
        @else
            <!-- Mensaje opcional si no hay widgets -->
            <p class="text-gray-500">No hay widgets seleccionados.</p>
        @endif
    </div>
</x-filament-panels::page>
