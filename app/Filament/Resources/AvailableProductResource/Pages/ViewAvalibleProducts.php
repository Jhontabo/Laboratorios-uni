<?php

namespace App\Filament\Resources\AvailableProductResource\pages;

use App\Filament\Resources\AvailableProductResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAvalibleProducts extends ViewRecord
{
    protected static string $resource = AvailableProductResource::class;
    protected static string $view = 'filament.pages.view-AvailableProduct';
}
