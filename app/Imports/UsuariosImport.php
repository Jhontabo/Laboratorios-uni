<?php

namespace App\Imports;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;

class UsuariosImport implements ToCollection, WithHeadingRow
{
    /**
     * Procesa la colección de datos
     */
    public function collection(Collection $rows)
    {
        // Primero validamos los encabezados
        $requiredHeaders = ['nombre', 'apellido', 'email', 'direccion', 'estado'];
        $firstRow = $rows->first();

        if (!$firstRow) {
            $this->showError();
            return;
        }

        foreach ($requiredHeaders as $header) {
            if (!isset($firstRow[$header])) {
                $this->showError();
                return;
            }
        }

        // Luego procesamos cada fila
        foreach ($rows as $row) {
            try {
                // Validación básica de campos requeridos
                if (
                    empty($row['nombre']) || empty($row['apellido']) || empty($row['email']) ||
                    empty($row['direccion']) || empty($row['estado'])
                ) {
                    $this->showError();
                    return;
                }

                // Validación de formato de email
                if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->showError();
                    return;
                }

                // Validación de usuario existente
                if (User::where('email', $row['email'])->exists()) {
                    $this->showError();
                    return;
                }

                // Validación de estado
                if (!in_array($row['estado'], ['activo', 'inactivo'])) {
                    $this->showError();
                    return;
                }

                // Creación del usuario
                $user = User::create([
                    'name' => $row['nombre'],
                    'apellido' => $row['apellido'],
                    'email' => $row['email'],
                    'telefono' => $row['telefono'] ?? null,
                    'direccion' => $row['direccion'],
                    'estado' => $row['estado']
                ]);

                $user->assignRole('ESTUDIANTE');
            } catch (\Exception $e) {
                $this->showError();
                return;
            }
        }

        // Si todo sale bien
        Notification::make()
            ->title('Importación exitosa')
            ->body('Los usuarios fueron importados correctamente')
            ->success()
            ->send();
    }

    /**
     * Muestra el mensaje de error genérico
     */
    private function showError()
    {
        Notification::make()
            ->title('Error en la importación')
            ->body('Formato no compatible. Verifique que el archivo cumpla con los requisitos.')
            ->danger()
            ->persistent()
            ->send();
    }
}
