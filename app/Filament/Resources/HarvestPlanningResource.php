<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HarvestPlanningResource\Pages;
use App\Filament\Resources\HarvestPlanningResource\RelationManagers;
//use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Harvest_planning;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HarvestPlanningResource extends Resource
{
    protected static ?string $model = Harvest_planning::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $modelLabel = 'Planificación de cosecha';

    protected static ?string $navigationLabel = 'Cosecha';

    protected static ?string $navigationGroup = 'Gestión de procesos'; // Grupo general principal

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Número de cosecha')
                ->placeholder('COSECHA')
                ->readOnly()
                ->dehydrated()                
            ,
    
                Forms\Components\DatePicker::make('date_start')
                    ->label('Fecha de inicio')
                    ->required()
                    ->format('Y-m-d')
                    // ->minDate(today()) // Permite seleccionar desde hoy en adelante
                    // ->dehydrated(true)
                    // ->validationAttribute('fecha de inicio')
                    // ->rules(['after_or_equal:today']) // Valida que sea hoy o posterior
                    // ->validationMessages(['after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.'])
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('date_end', null); // Resetea la fecha de finalización si cambia la fecha de inicio
                    }),
        
                Forms\Components\DatePicker::make('date_end')
                    ->label('Fecha de finalización')
                    ->required()
                    ->minDate(function (callable $get) {
                        return Carbon::parse($get('date_start')); // Permite seleccionar la misma fecha de inicio o posterior
                    })
                    ->format('Y-m-d')
                    ->dehydrated(true)
                    ->validationAttribute('fecha de finalización')
                    ->rules(['after_or_equal:date_start']) // Valida que sea igual o posterior a la fecha de inicio
                    ->validationMessages(['after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.'])
                    ->reactive(),
        
                Forms\Components\Select::make('worker_id')
                ->relationship('worker', 'name')
                ->preload()
                ->label('Trabajador')
                ->searchable()
                ->options(function () {
                    return Worker::all()->filter(function ($worker) {
                        return !$worker->isOccupied();
                    })->pluck('name', 'id');
                })
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('name', strtoupper($state));
                            }
                        })
                        ->live(onBlur: true)
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->label('Correo')
                            ->placeholder('Correo del usuario')
                            ->required()
                            ->unique()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->rules(['email:rfc,dns']) // Validates email format and DNS records
                            ->validationMessages([
                                'required' => 'El correo es obligatorio.',
                                'email' => 'El correo debe ser una dirección válida.',
                                'unique' => 'El correo ya está registrado.',
                            ])
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('email', strtoupper($state));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                            ,
                        Forms\Components\TextInput::make('cedula')
                            ->required()
                            ->minLength(10)
                            ->maxLength(10)
                            ->label('Cédula')
                            ->placeholder('Ejemplo: 0305863624')
                            ->rules(['required', 'regex:/^[0-9]{10}$/'])
                            ->validationAttribute('cedula')
                            ->afterStateUpdated(function (?string $state, callable $set) {
                                if ($state !== null && strlen($state) === 10 && ctype_digit($state)) {
                                    $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
                                    $suma = 0;
            
                                    for ($i = 0; $i < 9; $i++) {
                                        $valor = $coeficientes[$i] * intval($state[$i]);
                                        $suma += ($valor >= 10) ? ($valor - 9) : $valor;
                                    }
            
                                    $digitoVerificador = (($suma % 10) === 0) ? 0 : (10 - ($suma % 10));
            
                                    if ($digitoVerificador !== intval($state[9])) {
                                        $set('cedula', '');
                                        $set('error', 'La cédula ingresada no es válida.');
                                    } else {
                                        $set('error', null);
                                    }
                                } else {
                                    $set('error', 'La cédula debe contener solo 10 dígitos numéricos.');
                                }
                            })
                            ->reactive()
                            ->suffix(function (callable $get) {
                                return $get('error') ? '❌' : '✔️';
                            })
                            ->validationMessages([
                                'regex' => 'La cédula debe contener solo 10 dígitos numéricos',
                            ]),
                    Forms\Components\TextInput::make('phone')
                        ->label('Teléfono')
                        ->tel()
                        ->maxlength(10)
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        ->required(),
                ])
                ->required(),
                Forms\Components\Select::make('parcel_id')
                ->relationship('parcel', 'name')
                ->preload()
                ->label('Parcela')
                ->searchable()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(50)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('name', strtoupper($state));
                            }
                        })
                        ->live(onBlur: true)
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                    Forms\Components\TextInput::make('description')
                        ->label('Descripción')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('description', strtoupper($state));
                            }
                        })
                        ->live(onBlur: true)
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                    Forms\Components\TextInput::make('length')
                        ->label('Extención (ha)')
                        ->numeric()
                        ->inputMode('decimal')
                        ->required(),
                ])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Número de cosecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_start')
                    ->label('Fecha de inicio')
                    ->sortable()
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('date_end')
                    ->label('Fecha de finalización')
                    ->sortable()
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('worker.name')
                    ->label('Trabajador')
                    ->searchable(),
                Tables\Columns\TextColumn::make('parcel.name')
                    ->label('Parcela')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('date_start')
                    ->label('Fecha de inicio')
                    ->options(
                        Harvest_planning::pluck('date_start', 'date_start')->toArray()
                    ),

                Tables\Filters\SelectFilter::make('date_end')
                    ->label('Fecha de finalización')
                    ->options(
                        Harvest_planning::pluck('date_end', 'date_end')->toArray()
                    )
                    ,
                Tables\Filters\SelectFilter::make('worker_id')
                    ->label('Trabajador')
                    ->relationship('worker', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('parcel_id')
                    ->label('Parcela')
                    ->relationship('Parcel', 'name')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('Export')
                    ->label('Exportar PDF')
                    ->icon('heroicon-o-document')
                    ->openUrlInNewTab()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        return response()->streamDownload(function () use ($records) {
                            echo Pdf::loadHTML(
                                Blade::render('planning.P_harvest_report', ['records' => $records])
                            )->stream();
                        }, 'Planificacion_de_cosecha.pdf');
                    }),
                    ExportBulkAction::make()
                        ->label('Exportar Excel')
                        ->icon('heroicon-o-table-cells')
                ]),
            ]);
            
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\HarvestTrackingsRelationManager::class,
            RelationManagers\HarvestRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHarvestPlannings::route('/'),
            'create' => Pages\CreateHarvestPlanning::route('/create'),
            'edit' => Pages\EditHarvestPlanning::route('/{record}/edit'),
        ];
    }
}
