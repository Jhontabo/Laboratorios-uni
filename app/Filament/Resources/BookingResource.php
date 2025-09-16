<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages\ListBookings;
use App\Filament\Resources\BookingResource\Pages\ViewCalendar;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\{
  DateTimePicker,
  Placeholder,
  Radio,
  Section,
  Select,
  TextInput,
  Hidden
};
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
  protected static ?string $model = Schedule::class;

  protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
  protected static ?string $modelLabel = 'Reserva de Espacio';
  protected static ?string $navigationLabel = 'Reservar Espacio';
  protected static ?string $navigationGroup = 'Gestion de Reservas';

  public static function canViewAny(): bool
  {
    $user = Auth::user();
    return $user
      && ! $user->hasRole('LABORATORISTA')
      && ! $user->hasRole('COORDINADOR');
  }

  public static function table(Table $table): Table
  {
    $today = Carbon::now()->startOfDay();
    $limit = Carbon::now()->addMonth()->endOfDay();

    return $table
      ->query(
        Schedule::where('type', 'unstructured')
          ->whereBetween('start_at', [$today, $limit])
          ->orderBy('start_at')
          ->with('laboratory')
      )
      ->columns([
        TextColumn::make('laboratory.name')
          ->label('Espacio Académico')
          ->sortable()
          ->searchable(),

        TextColumn::make('start_at')
          ->label('Inicio')
          ->sortable()
          ->dateTime('d/m/Y H:i'),

        TextColumn::make('end_at')
          ->label('Fin')
          ->sortable()
          ->dateTime('d/m/Y H:i'),

        TextColumn::make('title')
          ->label('Título/Descripción')
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->actions([
        TableAction::make('reservar')
          ->label('Reservar')
          ->button()
          ->modalHeading('Solicitud de Reserva')
          ->modalWidth('lg')
          ->form([
            Section::make('Detalles de la práctica')->schema([
              Radio::make('project_type')
                ->label('Tipo de proyecto')
                ->options([
                  'Trabajo de grado' => 'Trabajo de grado',
                  'Investigación profesoral' => 'Investigación profesoral',
                ])
                ->columns(2)
                ->required(),

              Placeholder::make('laboratory_display')
                ->label('Espacio académico')
                ->content(fn(Schedule $record) => $record->laboratory->name ?? 'No asignado'),

              Hidden::make('laboratory_id')
                ->default(fn(Schedule $record) => $record->laboratory_id)
                ->required(),

              Select::make('academic_program')
                ->label('Programa académico')
                ->options([
                  'Ingeniería de Sistemas' => 'Ingeniería de Sistemas',
                  'Ingeniería Industrial' => 'Ingeniería de Industrial',
                  'Contaduría Pública' => 'Contaduría Pública',
                  'Administración de Empresas' => 'Administración de Empresas',
                ])
                ->required(),

              Select::make('semester')
                ->label('Semestre')
                ->options(array_combine(range(1, 10), range(1, 10)))
                ->required(),

              Select::make('applicants')
                ->label('Nombre de los solicitantes')
                ->multiple()
                ->searchable()
                ->options(function () {
                  return User::all()
                    ->mapWithKeys(fn($user) => [
                      $user->id => "{$user->name} {$user->last_name} - {$user->email}"
                    ])
                    ->toArray();
                })
                ->getSearchResultsUsing(function (string $search) {
                  return User::where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->limit(10)
                    ->get()
                    ->mapWithKeys(fn($user) => [
                      $user->id => "{$user->name} {$user->last_name} - {$user->email}"
                    ])
                    ->toArray();
                })
                ->required(),

              TextInput::make('research_name')
                ->label('Nombre de la investigación')
                ->required(),

              Select::make('advisor')
                ->label('Nombre del asesor')
                ->searchable()
                ->options(function () {
                  return User::all()
                    ->mapWithKeys(fn($user) => [
                      $user->id => "{$user->name} {$user->last_name} - {$user->email}"
                    ])
                    ->toArray();
                })
                ->getSearchResultsUsing(function (string $search) {
                  return User::where('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->limit(10)
                    ->get()
                    ->mapWithKeys(fn($user) => [
                      $user->id => "{$user->name} {$user->last_name} - {$user->email}"
                    ])
                    ->toArray();
                })
                ->required(),
            ]),

            Section::make('Materiales y equipos')->schema([
              Select::make('products')
                ->label('Productos disponibles')
                ->multiple()
                ->searchable()
                ->options(
                  fn() => Product::with('laboratory')
                    ->get()
                    ->mapWithKeys(fn($p) => [
                      $p->id => "{$p->name} — {$p->laboratory->name}",
                    ])->toArray()
                )
                ->required(),
            ]),

            Section::make('Horario solicitado')->schema([
              DateTimePicker::make('start_at')
                ->label('Inicio')
                ->default(fn(Schedule $record) => $record->start_at)
                ->readOnly(),

              DateTimePicker::make('end_at')
                ->label('Fin')
                ->default(fn(Schedule $record) => $record->end_at)
                ->after('start_at')
                ->readOnly(),
            ]),
          ])
          ->action(function (Schedule $record, array $data, TableAction $action): void {
            $user = Auth::user();

            // Obtener nombres de usuarios seleccionados
            $applicantNames = User::whereIn('id', $data['applicants'])
              ->get()
              ->map(fn($user) => "{$user->name} {$user->last_name}")
              ->implode(', ');

            $advisorUser = User::find($data['advisor']);
            $advisorName = $advisorUser ? "{$advisorUser->name} {$advisorUser->last_name}" : '';

            // Convertir array de productos a JSON
            $productsJson = json_encode($data['products']);

            Booking::create([
              'schedule_id' => $record->id,
              'user_id' => $user->id,
              'name' => $user->name,                    // Cambiado: name en lugar de first_name
              'last_name' => $user->last_name,
              'email' => $user->email,
              'project_type' => $data['project_type'],
              'laboratory_id' => $data['laboratory_id'],
              'academic_program' => $data['academic_program'],
              'semester' => $data['semester'],
              'applicants' => $applicantNames,          // Solo texto, no IDs
              'research_name' => $data['research_name'],
              'advisor' => $advisorName,                // Solo nombre, no ID
              'products' => $productsJson,              // JSON encoded
              'start_at' => $data['start_at'],
              'end_at' => $data['end_at'],
              'status' => Booking::STATUS_PENDING,
            ]);

            $action->success('¡Solicitud enviada y pendiente de aprobación!');
          }),

        ViewAction::make('ver_calendario')
          ->label('Ver Calendario')
          ->icon('heroicon-o-calendar')
          ->url(static::getUrl('calendar'))
          ->openUrlInNewTab(),
      ]);
  }

  public static function getWidgets(): array
  {
    return [
      CalendarWidget::class,
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => ListBookings::route('/'),
      'calendar' => ViewCalendar::route('/calendar'),
    ];
  }
}
