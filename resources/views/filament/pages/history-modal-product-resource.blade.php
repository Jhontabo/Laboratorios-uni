<div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm">
    @if($history->isEmpty())
        <div class="p-4 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium">No hay registros de historial</h3>
            <p class="mt-1 text-sm">Este equipo no tiene eventos registrados en su historial.</p>
        </div>
    @else
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semestre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante responsable</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programa académico</th>

                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($history as $record)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ match($record->reason) {
                                'damaged' => 'Dañado',
                                'maintenance' => 'Mantenimiento',
                                'lost' => 'Perdido',
                                'obsolete' => 'Obsoleto',
                                'other' => 'Otro',
                                default => $record->reason
                            } }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $record->observations ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $record->decommission_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $record->registeredBy->name }} {{ $record->registeredBy->last_name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $record->semester ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->responsibleUser)
                                {{ $record->responsibleUser->name }} {{ $record->responsibleUser->last_name }}
                            @else
                                No registrado
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($record->responsibleUser)
                                {{ $record->responsibleUser->document_number ?? 'Sin documento' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $record->academic_program ?? 'N/A' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
