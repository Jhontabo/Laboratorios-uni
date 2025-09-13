<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Laboratory;
use Carbon\Carbon;

class SchedulesSeeder extends Seeder
{
  public function run(): void
  {
    // Buscar laboratorista
    $hugo = User::where('email', 'hespinosa@umariana.edu.co')->first();
    if (!$hugo) {
      $this->command->error('No existe el usuario Hugo Espinosa.');
      return;
    }


    // Buscar laboratorios
    $labQuimica       = Laboratory::where('name', 'Química')->first();
    $labFisica        = Laboratory::where('name', 'Física')->first();
    $labBiologia      = Laboratory::where('name', 'Biología y Biotecnología')->first();
    $labFisicoQuimica = Laboratory::where('name', 'Fisico Química')->first();
    $labFluidos       = Laboratory::where('name', 'Fluidos')->first();
    $labElectronica   = Laboratory::where('name', 'Electrónica')->first();
    $labAutomatiza    = Laboratory::where('name', 'Automatización')->first();
    $labMateriales    = Laboratory::where('name', 'Materiales')->first();
    $labOperaciones   = Laboratory::where('name', 'Operaciones Unitarias')->first();
    $labGeotecnia     = Laboratory::where('name', 'Geotecnia')->first();
    $labTopografia    = Laboratory::where('name', 'Topografía')->first();

    // Validar laboratorios existentes
    $labs = [
      'Química'               => $labQuimica,
      'Física'                => $labFisica,
      'Biología y Biotecnología' => $labBiologia,
      'Fisico Química'        => $labFisicoQuimica,
      'Fluidos'               => $labFluidos,
      'Electrónica'           => $labElectronica,
      'Automatización'        => $labAutomatiza,
      'Materiales'            => $labMateriales,
      'Operaciones Unitarias' => $labOperaciones,
      'Geotecnia'             => $labGeotecnia,
      'Topografía'            => $labTopografia,
    ];
    foreach ($labs as $name => $lab) {
      if (!$lab) {
        $this->command->error("No existe el laboratorio de {$name}.");
        return;
      }
    }

    // Mapear días de semana (Carbon usa 0=domingo, 1=lunes… 6=sábado)
    $daysMap = [
      'monday'    => 1,
      'tuesday'   => 2,
      'wednesday' => 3,
      'thursday'  => 4,
      'friday'    => 5,
      'saturday'  => 6,
    ];

    // ---------------- HORARIOS ----------------

    $horariosQuimica = [
      ['day' => 'monday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Procesos industriales 2 - Ing. Procesos', 'weeks' => 4],
      ['day' => 'monday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Bioquímica IV semestre Ing. Procesos', 'weeks' => 10],
      ['day' => 'monday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Química Ambiental II Semestre Ing. Ambiental', 'weeks' => 10],

      ['day' => 'tuesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Diagnóstico Calidad Agua Ing. Ambiental V Semestre', 'weeks' => 9],
      ['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Remediación de Suelos Ing. Ambiental VII Semestre', 'weeks' => 7],
      ['day' => 'tuesday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Química Orgánica II Semestre Ing. Procesos', 'weeks' => 10],

      ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Química Analítica V Semestre Ing. Procesos', 'weeks' => 10],
      ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Fisicoquímica Ing. Ambiental VII Semestre', 'weeks' => 4],
      ['day' => 'wednesday', 'start' => '13:00', 'end' => '17:00', 'title' => 'Seminario de Investigación Ing. Procesos', 'weeks' => 16],

      ['day' => 'thursday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Química General I Semestre Ing. Ambiental', 'weeks' => 10],
      ['day' => 'thursday', 'start' => '10:00', 'end' => '13:00', 'title' => 'Tratamiento de Agua Grupo 2 Ing. Civil', 'weeks' => 3],
      ['day' => 'thursday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Ing. Procesos', 'weeks' => 10],

      ['day' => 'friday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Química General I Semestre Ing. Procesos', 'weeks' => 10],
    ];

    // -------- HORARIOS DE FÍSICA --------
    $horariosFisica = [
      ['day' => 'monday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Física 3 - Ing. Civil III Semestre', 'weeks' => 7],
      ['day' => 'monday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Física general - Ing. Ambiental III Semestre', 'weeks' => 7],
      ['day' => 'monday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Física 3 - Ing. Civil III Semestre', 'weeks' => 7],

      // Martes
      ['day' => 'tuesday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Física 1 Grupo 1 - Ing. Civil I Semestre', 'weeks' => 13],
      ['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Física 1 Grupo 2 - Ing. Civil I Semestre', 'weeks' => 13],
      ['day' => 'tuesday', 'start' => '13:00', 'end' => '16:00', 'title' => '84013 Mecánica II Sem (606) - Ing. Sistemas', 'weeks' => 15],

      // Miércoles
      ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'EMO III Sem - Ing. Procesos', 'weeks' => 12],
      ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Ondas Lab - Ing. Civil Grupo 1', 'weeks' => 10],
      ['day' => 'wednesday', 'start' => '13:00', 'end' => '16:00', 'title' => '82085 Electromagnetismo - Ing. Mecatrónica II Semestre', 'weeks' => 16],

      // Jueves
      ['day' => 'thursday', 'start' => '07:00', 'end' => '10:00', 'title' => '82008 Física de Movimiento - Ing. Mecatrónica III Semestre', 'weeks' => 16],
      ['day' => 'thursday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Ondas Lab - Ing. Civil Grupo 2', 'weeks' => 10],
      ['day' => 'thursday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Física del Movimiento - Ing. Procesos III Semestre', 'weeks' => 12],

      // Viernes
      ['day' => 'friday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Física 2 - Ing. Civil II Semestre', 'weeks' => 7],
      ['day' => 'friday', 'start' => '10:00', 'end' => '13:00', 'title' => '84022 Electricidad y Magnetismo III Sem (606) - Ing. Sistemas', 'weeks' => 15],
      ['day' => 'friday', 'start' => '13:00', 'end' => '15:00', 'title' => 'Física 2 - Ing. Civil II Semestre', 'weeks' => 7],
    ];


    $horariosBiologia = [
      // Lunes
      ['day' => 'monday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Microbiología - Ing. Procesos II Sem (12 semanas)', 'weeks' => 12],
      ['day' => 'monday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Procesos Industriales III - Ing. Procesos VII Sem (2 semanas)', 'weeks' => 2],

      // Martes
      ['day' => 'tuesday', 'start' => '07:00', 'end' => '09:00', 'title' => 'Riesgo y Medio Ambiente - Ing. Civil VII Sem (3 semanas)', 'weeks' => 3],

      // Miércoles
      ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Biología General - Ing. Ambiental III Sem (15 semanas)', 'weeks' => 15],
      ['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Tratamiento de Aguas - Ing. Civil VII Sem (3 semanas)', 'weeks' => 3],
      ['day' => 'wednesday', 'start' => '13:00', 'end' => '16:00', 'title' => 'Microbiología Ambiental - Ing. Ambiental IV Sem (10 semanas)', 'weeks' => 10],

      // Jueves
      ['day' => 'thursday', 'start' => '10:00', 'end' => '12:00', 'title' => 'Biotecnología - Ing. Procesos VII Sem (12 semanas)', 'weeks' => 12],
    ];

    $horariosFisicoQuimica = [
      // Miércoles
      ['day' => 'wednesday', 'start' => '07:00', 'end' => '10:00', 'title' => 'Operaciones Unitarias ing Ambiental (8 semanas)', 'weeks' => 8],

    ];

    $horariosFluidos = [
      // Lunes
      [
        'day'   => 'monday',
        'start' => '07:00',
        'end'   => '09:00',
        'title' => 'Ingeniería Civil (12 prácticas)',
        'weeks' => 12,
      ],

      // Martes
      [
        'day'   => 'tuesday',
        'start' => '13:00',
        'end'   => '16:00',
        'title' => 'Diagnóstico Calidad de Aire - Ing. Ambiental (7 prácticas)',
        'weeks' => 7,
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '07:00',
        'end'   => '09:00',
        'title' => 'Mecánica de Fluidos - Ing. Ambiental (8 prácticas)',
        'weeks' => 8,
      ],
      [
        'day'   => 'wednesday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'Mecánica de Fluidos - Ing. Procesos V Sem (8 prácticas) - Fisicoquímica Ing. Ambiental (2 prácticas)',
        'weeks' => 8, // puedes dividir si necesitas separar
      ],
      [
        'day'   => 'wednesday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => 'Ingeniería Civil (4 prácticas)',
        'weeks' => 4,
      ],

      // Jueves
      [
        'day'   => 'thursday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Hidrotecnia - Ing. Civil IV Sem (14 prácticas)',
        'weeks' => 14,
      ],

      // Viernes
      [
        'day'   => 'friday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Hidráulica - Ing. Ambiental VI Sem (10 prácticas)',
        'weeks' => 10,
      ],
      [
        'day'   => 'friday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'Control de emisiones - Ing. Ambiental (10 prácticas) + MPAS (3 prácticas Ing. Mecatrónica)',
        'weeks' => 10,
      ],
      [
        'day'   => 'friday',
        'start' => '13:00',
        'end'   => '16:00',
        'title' => 'Ingeniería Civil (12 prácticas)',
        'weeks' => 12,
      ],

      // Sábado
      [
        'day'   => 'saturday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'Reservas - Ing. Procesos',
        'weeks' => 0, // no se especifica número de semanas
      ],
    ];



    $horariosElectronica = [
      // Lunes
      [
        'day'   => 'monday',
        'start' => '07:00',
        'end'   => '09:00',
        'title' => '82070 Electrónica Análoga III semestre (670) - Ing. Mecatrónica',
        'weeks' => 15,
      ],
      [
        'day'   => 'monday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => '82028 (670) Electrónica de Potencia - Ing. Mecatrónica V Sem G1 (8 prácticas)',
        'weeks' => 8,
      ],

      // Martes
      [
        'day'   => 'tuesday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => '84036 Electrónica Digital V Sem (606) - Ing. Sistemas (15 semanas)',
        'weeks' => 15,
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'I4154 Electrónica Interactiva I Sem (606) - Ing. Sistemas (15 semanas)',
        'weeks' => 15,
      ],
      [
        'day'   => 'wednesday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => '84027 Máquinas Eléctricas 4 Sem (606) - Ing. Sistemas (15 semanas)',
        'weeks' => 15,
      ],

      // Jueves
      [
        'day'   => 'thursday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => '82065 (Grupo 1) Circuitos Eléctricos - Ing. Mecatrónica IV Sem (670)',
        'weeks' => 15,
      ],

      // Viernes
      [
        'day'   => 'friday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'I2033 Electrónica Prof. III Microelectrónica para IoT IX Sem (559)',
        'weeks' => 15,
      ],
      [
        'day'   => 'friday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => '82065 (Grupo 2) Circuitos Eléctricos - Ing. Mecatrónica IV Sem (670)',
        'weeks' => 15,
      ],
    ];


    $horariosAutomatizacion = [
      // Lunes
      [
        'day'   => 'monday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'I2032 Elec. Prof. II - Automatización de Procesos Industriales (Ing. Mecatrónica VII Sem G3)',
      ],
      [
        'day'   => 'monday',
        'start' => '14:00',
        'end'   => '15:00',
        'title' => 'Reservado IDEP',
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => '82034 (Grupo 1) - Electronemática (Ing. Mecatrónica)',
      ],
      [
        'day'   => 'wednesday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => '82034 (Grupo 2) - Electronemática (Ing. Mecatrónica)',
      ],

      // Jueves
      [
        'day'   => 'thursday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => '82040 (Grupo 1) - Autómatas Programables (Ing. Mecatrónica)',
      ],
      [
        'day'   => 'thursday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => '82040 (Grupo 2) - Autómatas Programables (Ing. Mecatrónica)',
      ],
    ];

    $horariosMateriales = [
      // Martes
      [
        'day'   => 'tuesday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Materiales (4 semestre) Ing. Civil (12 prácticas)',
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Materiales (4 semestre) Ing. Civil (12 prácticas)',
      ],

      // Jueves
      [
        'day'   => 'thursday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Materiales (4 semestre) Ing. Civil (12 prácticas)',
      ],

      // Viernes
      [
        'day'   => 'friday',
        'start' => '07:00',
        'end'   => '10:00',
        'title' => 'Tecnología del Concreto Ing. Civil V Sem (12 prácticas)',
      ],
    ];


    $horariosOperaciones = [
      // Lunes
      [
        'day'   => 'monday',
        'start' => '07:00',
        'end'   => '12:00',
        'title' => 'Procesos Industriales 2 - Ing. Procesos 7 Sem (8 semanas)',
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '07:00',
        'end'   => '12:00',
        'title' => 'Procesos Industriales 1 - Ing. Procesos 6 Sem (8 semanas)',
      ],

      // Jueves
      [
        'day'   => 'thursday',
        'start' => '07:00',
        'end'   => '12:00',
        'title' => 'Mecatrónica (4)',
      ],

      // Viernes
      [
        'day'   => 'friday',
        'start' => '07:00',
        'end'   => '12:00',
        'title' => 'Procesos Industriales 3 - Ing. Procesos 8 Sem (8 semanas)',
      ],
    ];


    $horariosGeotecnia = [
      // Martes
      [
        'day'   => 'tuesday',
        'start' => '10:00',
        'end'   => '12:00',
        'title' => 'Mecánica de suelos - IV Sem Ing. Civil',
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '07:00',
        'end'   => '09:00',
        'title' => 'Mecánica de suelos - IV Sem Ing. Civil',
      ],
    ];


    $horariosTopografia = [
      // Lunes
      [
        'day'   => 'monday',
        'start' => '13:00',
        'end'   => '15:00',
        'title' => 'Topografía G1 - Ing. Civil',
      ],

      // Miércoles
      [
        'day'   => 'wednesday',
        'start' => '08:00',
        'end'   => '10:00',
        'title' => 'Topografía G1 - Ing. Civil',
      ],

      // Viernes
      [
        'day'   => 'friday',
        'start' => '08:00',
        'end'   => '10:00',
        'title' => 'Topografía G1 - Ing. Civil',
      ],
    ];
    // Fecha base → lunes de esta semana
    $baseDate = Carbon::now()->startOfWeek(Carbon::MONDAY);

    // Función para crear horarios
    $insertHorarios = function ($horarios, $lab, $user, $daysMap, $baseDate) {
      foreach ($horarios as $h) {
        $weeks     = $h['weeks'] ?? 16; // default 16 semanas
        $dayNumber = $daysMap[$h['day']];
        $dayDate   = $baseDate->copy()->addDays($dayNumber - 1);

        $start = Carbon::parse($dayDate->toDateString() . ' ' . $h['start']);
        $end   = Carbon::parse($dayDate->toDateString() . ' ' . $h['end']);

        Schedule::create([
          'laboratory_id'    => $lab->id,
          'user_id'          => $user->id,
          'title'            => $h['title'],
          'start_at'         => $start,
          'end_at'           => $end,
          'description'      => $weeks . ' semanas',
          'color'            => '#5b82f6',
          'type'             => 'structured',
          'recurrence_days'  => (string) $dayNumber,
          'recurrence_until' => $start->copy()->addWeeks($weeks),
        ]);
      }
    };

    // Insertar horarios de cada laboratorio
    $insertHorarios($horariosQuimica, $labQuimica, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosFisica, $labFisica, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosBiologia, $labBiologia, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosFisicoQuimica, $labFisicoQuimica, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosFluidos, $labFluidos, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosElectronica, $labElectronica, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosMateriales, $labMateriales, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosOperaciones, $labOperaciones, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosGeotecnia, $labGeotecnia, $hugo, $daysMap, $baseDate);
    $insertHorarios($horariosTopografia, $labTopografia, $hugo, $daysMap, $baseDate);

    $this->command->info('Horarios de todos los laboratorios insertados correctamente. 🚀');
  }
}
