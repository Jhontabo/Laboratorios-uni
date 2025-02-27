<x-filament-panels::page>
    <div>
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Elegir horario</h3>
        <select name="laboratorio"
            onchange="location.href='?laboratorio='+this.value" class="form-control border-gray-300 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
            <option value="">Seleccione un laboratorio</option>
            @foreach ($this->getLaboratorios() as $id => $nombre)
                <option value="{{ $id }}" {{ request()->query('laboratorio') == $id ? 'selected' : '' }}>
                    {{ $nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="calendar"></div>
</x-filament-panels::page>

