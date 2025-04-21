<?php

namespace App\Filament\Resources\ProductoDisponibleResource\Pages;

use App\Filament\Resources\ProductoDisponibleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductoDisponible extends ViewRecord
{
    protected static string $resource = ProductoDisponibleResource::class;
    protected static string $view = 'filament.resources.producto-disponible-resource.pages.view-producto';
}
