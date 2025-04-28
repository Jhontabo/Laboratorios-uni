<x-filament-panels::page>
    <!-- Container with flexbox to align the title and the dropdown -->
    <div class="flex items-center justify-between mb-4">
        <!-- Title -->

        <!-- Dropdown to select the widget -->
        <form method="GET" action="{{ url()->current() }}">
            <select name="laboratory" onchange="this.form.submit()"
                class="form-control border-gray-300 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                @foreach ($this->getDropdownOptions() as $key => $label)
                    <option value="{{ $key }}"
                        {{ request()->query('laboratory', 'All') === (string) $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</x-filament-panels::page>
