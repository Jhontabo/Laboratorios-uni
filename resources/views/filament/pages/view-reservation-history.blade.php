<x-filament::page>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Reservation Details #{{ $record->id }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Created on {{ $record->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

            <x-filament::badge :color="match ($record->status) {
                'pending' => 'warning',
                'accepted' => 'success',
                'rejected' => 'danger',
            }" class="text-sm">
                {{ match ($record->status) {
                    'pending' => 'Pending Approval',
                    'accepted' => 'Approved',
                    'rejected' => 'Rejected',
                } }}
            </x-filament::badge>
        </div>

        <!-- Main Information Section -->
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Laboratory Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-building-office class="w-5 h-5 inline mr-2" />
                        Laboratory Information
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Name:</span>
                            {{ $record->laboratory->name ?? 'Not specified' }}
                        </p>
                        <p>
                            <span class="font-medium">Location:</span>
                            {{ $record->laboratory->location ?? 'Not specified' }}
                        </p>
                        <p>
                            <span class="font-medium">Capacity:</span>
                            {{ $record->laboratory->capacity ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Reservation Schedule -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-clock class="w-5 h-5 inline mr-2" />
                        Reserved Schedule
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Date:</span>
                            {{ $record->schedule->start_at->format('d/m/Y') ?? 'Not specified' }}
                        </p>
                        <p>
                            <span class="font-medium">Time:</span>
                            {{ $record->schedule->start_at->format('H:i') ?? '00:00' }} -
                            {{ $record->schedule->end_at->format('H:i') ?? '00:00' }}
                        </p>
                        <p>
                            <span class="font-medium">Duration:</span>
                            {{ $record->schedule->start_at->diffInHours($record->schedule->end_at) }} hours
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Applicant Information Section -->
        <x-filament::card>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <x-heroicon-o-user class="w-5 h-5 inline mr-2" />
                Applicant Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="font-medium">First Name:</p>
                    <p>{{ $record->first_name }} </p>
                </div>

                <div>
                    <p class="font-medium">Last Name:</p>
                    <p>{{ $record->last_name }}</p>
                    <br>
                <div>
                    <p class="font-medium">Email:</p>
                    <p>{{ $record->email }}</p>
                </div>

            </div>
        </x-filament::card>

        <!-- Status and Comments Section -->
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <x-heroicon-o-document-text class="w-5 h-5 inline mr-2" />
                        Additional Details
                    </h3>

                    <div class="space-y-2">
                        <p>
                            <span class="font-medium">Request Date:</span>
                            {{ $record->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p>
                            <span class="font-medium">Last Updated:</span>
                            {{ $record->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                @if ($record->status === 'rejected' && $record->rejection_reason)
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            <x-heroicon-o-x-circle class="w-5 h-5 inline mr-2 text-danger-500" />
                            Reason for Rejection
                        </h3>
                        <p class="bg-danger-50 dark:bg-danger-900/50 p-4 rounded-lg">
                            {{ $record->rejection_reason }}
                        </p>
                    </div>
                @endif
            </div>
        </x-filament::card>
    </div>
</x-filament::page>
