@extends('filament::page')

@section('content')
    <div class="filament-widget">
        @livewire('calendar-widget')  {{-- Cargar el widget de calendario --}}
    </div>
@endsection
