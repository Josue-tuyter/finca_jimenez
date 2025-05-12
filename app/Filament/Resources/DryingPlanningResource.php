<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DryingPlanningResource\Pages;
use App\Filament\Resources\DryingPlanningResource\RelationManagers\DryingTrackingsRelationManager;
use App\Filament\Resources\DryingPlanningResource\RelationManagers;
use App\Models\Drying_planning;
use App\Models\Worker;
use Carbon\Carbon;
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

class DryingPlanningResource extends Resource
{
    protected static ?string $model = Drying_planning::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    protected static ?string $modelLabel = 'Planificación de Secado';

    protected static ?string $navigationLabel = 'Secado';


protected static ?string $navigationGroup = 'Gestión de procesos';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Número de secado')
                    ->placeholder('SECADO')
                    ->readOnly()
                    ->dehydrated()
                ,

                Forms\Components\DatePicker::make('D_date_start')
                    ->label('Fecha de inicio')
                    ->required()
                    ->format('d-m-y')
                    // ->minDate(today()) // Cambiado para permitir solo la fecha de hoy o posteriores
                    // ->dehydrated(true)
                    // ->validationAttribute('fecha de inicio')
                    // ->rules(['after_or_equal:today']) // Regla para validar que sea hoy o posterior
                    // ->validationMessages(['after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.'])
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('D_date_end', null);
                    }),

                Forms\Components\DatePicker::make('D_date_end')
                    ->label('Fecha de finalización')
                    ->required()
                    ->minDate(function (callable $get) {
                        return Carbon::parse($get('D_date_start'))->addDays(2);
                    })
                    ->format('d-m-y')
                    ->dehydrated(true)
                    ->validationAttribute('fecha de finalización')
                    //->rules(['after_or_equal:today'])
                    //->validationMessages(['before_or_equal' => 'La fecha de finalización tiene que ser 2 días mayor a la fecha de inicio.'])
                    ->reactive()
                    ,

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
                            Forms\Components\TextInput::make('email')
                                ->label('Correo')
                                ->required()
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('Phone')
                                ->label('Teléfono')
                                ->tel()
                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                ->required()
                                ->maxLength(10),
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label('Número de secado')
                ->searchable(),
                Tables\Columns\TextColumn::make('D_date_start')
                ->label('Fecha de inicio')
                ->sortable(),
                Tables\Columns\TextColumn::make('D_date_end')
                ->label('Fecha de finalización')
                ->sortable(),
                Tables\Columns\TextColumn::make('worker.name')
                ->label('Trabajador')
                ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label('Número de secado')
                    ->options(
                        Drying_planning::pluck('name', 'name')->toArray()
                    )
                    ->searchable(),
                Tables\Filters\Filter::make('D_date_start')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Fecha de incio desde')
                            ->format('Y-m-d'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Fecha de incio hasta')
                            ->format('Y-m-d'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('D_date_start', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('D_date_start', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Fecha de incio desde ' .Carbon::parse($data['from'])->format('Y-m-d');
                        }
                        
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Fecha de incio hasta' . Carbon::parse($data['until'])->format('Y-m-d');
                        }
                        
                        return $indicators;
                    }),
                
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
                                Blade::render('planning.P_Drying_report', ['records' => $records])
                            )->stream();
                        }, 'P_Drying_report.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar excel')
                    ->icon('heroicon-m-table-cells'),
                    
                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DryingTrackingsRelationManager::class,
            RelationManagers\DryingRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDryingPlannings::route('/'),
            'create' => Pages\CreateDryingPlanning::route('/create'),
            'edit' => Pages\EditDryingPlanning::route('/{record}/edit'),
        ];
    }
}
