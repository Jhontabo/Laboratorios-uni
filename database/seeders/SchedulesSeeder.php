<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Laboratory;
use Carbon\Carbon;

class SchedulesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // 1. Buscar al usuario responsable
    $user = User::where('email', 'hespinosa@umariana.edu.co')->first();
    if (!$user) {
      $this->command->error('No se encontró el usuario hespinosa@umariana.edu.co. Abortando seeder.');
      return;
    }

    // 2. Estructura de datos centralizada con horarios y colores por laboratorio
    // Se usan los nombres exactos del LaboratoriesSeeder
    $labSchedulesData = [
      'Quimica' => [
        'color' => '#E74C3C', // Rojo
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '09:00', 'title' => 'Procesos industriales 2 - Ing. Procesos', 'weeks' => 4],
          ['day' => 'monday',    'start' => '10:00', 'end' => '12:00', 'title' => 'Bioquímica IV semestre Ing. Procesos', 'weeks' => 10],
          ['day' => 'monday',    'start' => '13:00', 'end' => '15:00', 'title' => 'Química Ambiental II Semestre Ing. Ambiental', 'weeks' => 10],
          ['day' => 'tuesday',   'start' => '07:00', 'end' => '10:00', 'title' => 'Diagnóstico Calidad Agua Ing. Ambiental V Semestre', 'weeks' => 9],
          ['day' => 'tuesday',   'start' => '10:00', 'end' => '12:00', 'title' => 'Remediación de Suelos Ing. Ambiental VII Semestre', 'weeks' => 7],
          ['day' => 'tuesday',   'start' => '13:00', 'end' => '15:00', 'title' => 'Química Orgánica II Semestre Ing. Procesos', 'weeks' => 10],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Química Analítica V Semestre Ing. Procesos', 'weeks' => 10],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Fisicoquímica Ing. Ambiental VII Semestre', 'weeks' => 4],
          ['day' => 'wednesday', 'start' => '13:00', 'end' => '17:00', 'title' => 'Seminario de Investigación Ing. Procesos', 'weeks' => 16],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '10:00', 'title' => 'Química General I Semestre Ing. Ambiental', 'weeks' => 10],
          ['day' => 'thursday',  'start' => '10:00', 'end' => '13:00', 'title' => 'Tratamiento de Agua Grupo 2 Ing. Civil', 'weeks' => 3],
          ['day' => 'thursday',  'start' => '13:00', 'end' => '15:00', 'title' => 'Ing. Procesos', 'weeks' => 10],
          ['day' => 'friday',    'start' => '07:00', 'end' => '10:00', 'title' => 'Química General I Semestre Ing. Procesos', 'weeks' => 10],
        ]
      ],
      'Fisica' => [
        'color' => '#3498DB', // Azul
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '09:00', 'title' => 'Física 3 - Ing. Civil III Semestre', 'weeks' => 7],
          ['day' => 'monday',    'start' => '10:00', 'end' => '12:00', 'title' => 'Física general - Ing. Ambiental III Semestre', 'weeks' => 7],
          ['day' => 'monday',    'start' => '13:00', 'end' => '15:00', 'title' => 'Física 3 - Ing. Civil III Semestre', 'weeks' => 7],
          ['day' => 'tuesday',   'start' => '07:00', 'end' => '09:00', 'title' => 'Física 1 Grupo 1 - Ing. Civil I Semestre', 'weeks' => 13],
          ['day' => 'tuesday',   'start' => '10:00', 'end' => '12:00', 'title' => 'Física 1 Grupo 2 - Ing. Civil I Semestre', 'weeks' => 13],
          ['day' => 'tuesday',   'start' => '13:00', 'end' => '16:00', 'title' => '84013 Mecánica II Sem (606) - Ing. Sistemas', 'weeks' => 15],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'EMO III Sem - Ing. Procesos', 'weeks' => 12],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Ondas Lab - Ing. Civil Grupo 1', 'weeks' => 10],
          ['day' => 'wednesday', 'start' => '13:00', 'end' => '16:00', 'title' => '82085 Electromagnetismo - Ing. Mecatrónica II Semestre', 'weeks' => 16],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '10:00', 'title' => '82008 Física de Movimiento - Ing. Mecatrónica III Semestre', 'weeks' => 16],
          ['day' => 'thursday',  'start' => '10:00', 'end' => '12:00', 'title' => 'Ondas Lab - Ing. Civil Grupo 2', 'weeks' => 10],
          ['day' => 'thursday',  'start' => '13:00', 'end' => '15:00', 'title' => 'Física del Movimiento - Ing. Procesos III Semestre', 'weeks' => 12],
          ['day' => 'friday',    'start' => '07:00', 'end' => '09:00', 'title' => 'Física 2 - Ing. Civil II Semestre', 'weeks' => 7],
          ['day' => 'friday',    'start' => '10:00', 'end' => '13:00', 'title' => '84022 Electricidad y Magnetismo III Sem (606) - Ing. Sistemas', 'weeks' => 15],
          ['day' => 'friday',    'start' => '13:00', 'end' => '15:00', 'title' => 'Física 2 - Ing. Civil II Semestre', 'weeks' => 7],
        ]
      ],
      'Biologia' => [
        'color' => '#2ECC71', // Verde
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '09:00', 'title' => 'Microbiología - Ing. Procesos II Sem', 'weeks' => 12],
          ['day' => 'monday',    'start' => '10:00', 'end' => '12:00', 'title' => 'Procesos Industriales III - Ing. Procesos VII Sem', 'weeks' => 2],
          ['day' => 'tuesday',   'start' => '07:00', 'end' => '09:00', 'title' => 'Riesgo y Medio Ambiente - Ing. Civil VII Sem', 'weeks' => 3],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Biología General - Ing. Ambiental III Sem', 'weeks' => 15],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Tratamiento de Aguas - Ing. Civil VII Sem', 'weeks' => 3],
          ['day' => 'wednesday', 'start' => '13:00', 'end' => '16:00', 'title' => 'Microbiología Ambiental - Ing. Ambiental IV Sem', 'weeks' => 10],
          ['day' => 'thursday',  'start' => '10:00', 'end' => '12:00', 'title' => 'Biotecnología - Ing. Procesos VII Sem', 'weeks' => 12],
        ]
      ],
      'Fisico-Quimica' => [
        'color' => '#F1C40F', // Amarillo
        'schedules' => [
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Operaciones Unitarias - Ing. Ambiental', 'weeks' => 8],
        ]
      ],
      'Fluidos' => [
        'color' => '#34495E', // Azul oscuro
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '09:00', 'title' => 'Ingeniería Civil', 'weeks' => 12],
          ['day' => 'tuesday',   'start' => '13:00', 'end' => '16:00', 'title' => 'Diagnóstico Calidad de Aire - Ing. Ambiental', 'weeks' => 7],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Mecánica de Fluidos - Ing. Ambiental', 'weeks' => 8],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Mecánica de Fluidos - Ing. Procesos V Sem', 'weeks' => 8],
          ['day' => 'wednesday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Ingeniería Civil', 'weeks' => 4],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '10:00', 'title' => 'Hidrotecnia - Ing. Civil IV Sem', 'weeks' => 14],
          ['day' => 'friday',    'start' => '07:00', 'end' => '10:00', 'title' => 'Hidráulica - Ing. Ambiental VI Sem', 'weeks' => 10],
          ['day' => 'friday',    'start' => '10:00', 'end' => '12:00', 'title' => 'Control de emisiones - Ing. Ambiental', 'weeks' => 10],
          ['day' => 'friday',    'start' => '13:00', 'end' => '16:00', 'title' => 'Ingeniería Civil', 'weeks' => 12],
          ['day' => 'saturday',  'start' => '10:00', 'end' => '12:00', 'title' => 'Reservas - Ing. Procesos', 'weeks' => 16], // Asumiendo 16 semanas por defecto
        ]
      ],
      'Electronica' => [
        'color' => '#9B59B6', // Morado
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '09:00', 'title' => '82070 Electrónica Análoga III sem (670) - Ing. Mecatrónica', 'weeks' => 15],
          ['day' => 'monday',    'start' => '13:00', 'end' => '15:00', 'title' => '82028 (670) Electrónica de Potencia - Ing. Mecatrónica V Sem G1', 'weeks' => 8],
          ['day' => 'tuesday',   'start' => '07:00', 'end' => '10:00', 'title' => '84036 Electrónica Digital V Sem (606) - Ing. Sistemas', 'weeks' => 15],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'I4154 Electrónica Interactiva I Sem (606) - Ing. Sistemas', 'weeks' => 15],
          ['day' => 'wednesday', 'start' => '13:00', 'end' => '15:00', 'title' => '84027 Máquinas Eléctricas 4 Sem (606) - Ing. Sistemas', 'weeks' => 15],
          ['day' => 'thursday',  'start' => '13:00', 'end' => '15:00', 'title' => '82065 (G1) Circuitos Eléctricos - Ing. Mecatrónica IV Sem', 'weeks' => 15],
          ['day' => 'friday',    'start' => '10:00', 'end' => '12:00', 'title' => 'I2033 Electrónica Prof. III Microelectrónica para IoT IX Sem', 'weeks' => 15],
          ['day' => 'friday',    'start' => '13:00', 'end' => '15:00', 'title' => '82065 (G2) Circuitos Eléctricos - Ing. Mecatrónica IV Sem', 'weeks' => 15],
        ]
      ],
      'Automatizacion' => [
        'color' => '#E67E22', // Naranja
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '10:00', 'title' => 'Elec. Prof. II - Automatización de Procesos (Ing. Mecatrónica)'],
          ['day' => 'monday',    'start' => '14:00', 'end' => '15:00', 'title' => 'Reservado IDEP'],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => '82034 (G1) - Electronemática (Ing. Mecatrónica)'],
          ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => '82034 (G2) - Electronemática (Ing. Mecatrónica)'],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '10:00', 'title' => '82040 (G1) - Autómatas Programables (Ing. Mecatrónica)'],
          ['day' => 'thursday',  'start' => '10:00', 'end' => '12:00', 'title' => '82040 (G2) - Autómatas Programables (Ing. Mecatrónica)'],
        ]
      ],
      'Materiales' => [
        'color' => '#7F8C8D', // Gris
        'schedules' => [
          ['day' => 'tuesday',   'start' => '07:00', 'end' => '10:00', 'title' => 'Materiales (4 sem) Ing. Civil', 'weeks' => 12],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Materiales (4 sem) Ing. Civil', 'weeks' => 12],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '10:00', 'title' => 'Materiales (4 sem) Ing. Civil', 'weeks' => 12],
          ['day' => 'friday',    'start' => '07:00', 'end' => '10:00', 'title' => 'Tecnología del Concreto Ing. Civil V Sem', 'weeks' => 12],
        ]
      ],
      'Operaciones' => [
        'color' => '#1ABC9C', // Turquesa
        'schedules' => [
          ['day' => 'monday',    'start' => '07:00', 'end' => '12:00', 'title' => 'Procesos Industriales 2 - Ing. Procesos 7 Sem', 'weeks' => 8],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '12:00', 'title' => 'Procesos Industriales 1 - Ing. Procesos 6 Sem', 'weeks' => 8],
          ['day' => 'thursday',  'start' => '07:00', 'end' => '12:00', 'title' => 'Mecatrónica', 'weeks' => 4],
          ['day' => 'friday',    'start' => '07:00', 'end' => '12:00', 'title' => 'Procesos Industriales 3 - Ing. Procesos 8 Sem', 'weeks' => 8],
        ]
      ],
      'Geotecnia' => [
        'color' => '#A93226', // Marrón
        'schedules' => [
          ['day' => 'tuesday',   'start' => '10:00', 'end' => '12:00', 'title' => 'Mecánica de suelos - IV Sem Ing. Civil'],
          ['day' => 'wednesday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Mecánica de suelos - IV Sem Ing. Civil'],
        ]
      ],
      'Topografia' => [
        'color' => '#2980B9', // Azul Petróleo
        'schedules' => [
          ['day' => 'monday',    'start' => '13:00', 'end' => '15:00', 'title' => 'Topografía G1 - Ing. Civil'],
          ['day' => 'wednesday', 'start' => '08:00', 'end' => '10:00', 'title' => 'Topografía G1 - Ing. Civil'],
          ['day' => 'friday',    'start' => '08:00', 'end' => '10:00', 'title' => 'Topografía G1 - Ing. Civil'],
        ]
      ],
    ];

    // 3. Obtener todos los laboratorios necesarios en una sola consulta
    $labNames = array_keys($labSchedulesData);
    $laboratories = Laboratory::whereIn('name', $labNames)->get()->keyBy('name');

    // 4. Mapeo de días y fecha base para los cálculos
    $baseDate = Carbon::now()->startOfWeek();
    $daysMap = [
      'monday'    => Carbon::MONDAY,
      'tuesday'   => Carbon::TUESDAY,
      'wednesday' => Carbon::WEDNESDAY,
      'thursday'  => Carbon::THURSDAY,
      'friday'    => Carbon::FRIDAY,
      'saturday'  => Carbon::SATURDAY,
    ];

    $this->command->info('Insertando horarios para los laboratorios...');

    // 5. Bucle principal para crear los horarios
    foreach ($labSchedulesData as $labName => $data) {
      // Verificar si el laboratorio existe
      if (!isset($laboratories[$labName])) {
        $this->command->warn("-> Advertencia: No se encontró el laboratorio '{$labName}'. Se omitirán sus horarios.");
        continue; // Saltar al siguiente laboratorio
      }

      $lab = $laboratories[$labName];
      $schedules = $data['schedules'];
      $color = $data['color'];
      $count = 0;

      foreach ($schedules as $scheduleData) {
        $weeks = $scheduleData['weeks'] ?? 16; // 16 semanas por defecto si no se especifica
        $dayNumber = $daysMap[$scheduleData['day']];

        $startDateTime = $baseDate->copy()->addDays($dayNumber - 1)->setTimeFromTimeString($scheduleData['start']);
        $endDateTime = $baseDate->copy()->addDays($dayNumber - 1)->setTimeFromTimeString($scheduleData['end']);

        Schedule::create([
          'laboratory_id'    => $lab->id,
          'user_id'          => $user->id,
          'title'            => $scheduleData['title'],
          'start_at'         => $startDateTime,
          'end_at'           => $endDateTime,
          'description'      => "Duración: {$weeks} semanas.",
          'color'            => $color, // Color asignado al laboratorio
          'type'             => 'structured',
          'recurrence_days'  => (string) $dayNumber,
          'recurrence_until' => $weeks > 0 ? $startDateTime->copy()->addWeeks($weeks) : null,
        ]);
        $count++;
      }
      $this->command->info("-> Se insertaron {$count} horarios para el laboratorio de {$labName}.");
    }

    $this->command->info('✅ Horarios de todos los laboratorios insertados correctamente. 🚀');
  }
}
