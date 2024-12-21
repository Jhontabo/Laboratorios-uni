<?php

namespace App\Imports;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsuariosImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  Collection $collection 
     */

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (User::where('email', $row['email'])->exists()) {
                Notification::make()
                    ->title('Advertencia')
                    ->body("El correo electrónico '{$row['email']}' ya existe. No se importará.")
                    ->warning()
                    ->send();
                continue; // Salta al siguiente registro
            }

            User::create([
                'name' => $row['nombre'],
                'apellido' => $row['apellido'],
                'email' => $row['email'],
                'telefono' => $row['telefono'] ?? null,
                'direccion' => $row['direccion'],
                'estado' => $row['estado']
            ]);
        }
    }
}
