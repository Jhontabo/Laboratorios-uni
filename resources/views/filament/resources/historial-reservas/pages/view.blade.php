<x-filament::page>
    <div class="space-y-6">
        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Detalles de la Reserva #{{ $record->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Creada el {{ $record->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <x-filament::badge :color="match ($record->estado) {
                'pendiente' => 'warning',
                'aceptada' => 'success',
                'rechazada' => 'danger',
            }" class="text-sm">
                {{ match ($record->estado) {
                    'pendiente' => 'Pendiente de Aprobación',
                    'aceptada' => 'Aprobada',
                    'rechazada' => 'Rechazada',
                } }}
            </x-filament::badge>
        </div>

        <!-- Sección de información principal -->
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información del Laboratorio -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-building-office class="w-5 h-5 inline mr-2" />
                        Información del Laboratorio
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Nombre:</span>
                            {{ $record->laboratorio->nombre ?? 'No especificado' }}
                        </p>
                        <p>
                            <span class="font-medium">Ubicación:</span>
                            {{ $record->laboratorio->ubicacion ?? 'No especificada' }}
                        </p>
                        <p>
                            <span class="font-medium">Capacidad:</span>
                            {{ $record->laboratorio->capacidad ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Horario de la Reserva -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-clock class="w-5 h-5 inline mr-2" />
                        Horario Reservado
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Fecha:</span>
                            {{ $record->horario->start_at->format('d/m/Y') ?? 'No especificada' }}
                        </p>
                        <p>
                            <span class="font-medium">Hora:</span>
                            {{ $record->horario->start_at->format('H:i') ?? '00:00' }} -
                            {{ $record->horario->end_at->format('H:i') ?? '00:00' }}
                        </p>
                        <p>
                            <span class="font-medium">Duración:</span>
                            {{ $record->horario->start_at->diffInHours($record->horario->end_at) }} horas
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Sección de información del solicitante -->
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <x-heroicon-o-user class="w-5 h-5 inline mr-2" />
                Información del Solicitante
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="font-medium">Nombre:</p>
                    <p>{{ $record->nombre_usuario }} {{ $record->apellido_usuario }}</p>
                </div>
                <div>
                    <p class="font-medium">Correo Electrónico:</p>
                    <p>{{ $record->correo_usuario }}</p>
                </div>

            </div>
        </x-filament::card>

        <!-- Sección de estado y comentarios -->
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-document-text class="w-5 h-5 inline mr-2" />
                        Detalles Adicionales
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Fecha de Solicitud:</span>
                            {{ $record->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p>
                            <span class="font-medium">Última Actualización:</span>
                            {{ $record->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if ($record->estado === 'rechazada' && $record->razon_rechazo)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            <x-heroicon-o-x-circle class="w-5 h-5 inline mr-2 text-danger-500" />
                            Razón del Rechazo
                        </h3>
                        <p class="bg-danger-50 dark:bg-danger-900/50 p-4 rounded-lg">
                            {{ $record->razon_rechazo }}
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::card>
    </div>
</x-filament::page>
