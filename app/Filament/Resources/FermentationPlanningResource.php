<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FermentationPlanningResource\Pages;
use App\Filament\Resources\FermentationPlanningResource\RelationManagers;
use App\Models\Fermentation_planning;
use App\Models\Worker;
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
use Carbon\Carbon;

class FermentationPlanningResource extends Resource
{
    protected static ?string $model = Fermentation_planning::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Planificación de fermentaciones';

    protected static ?string $navigationLabel = 'Fermentación';

    protected static ?string $navigationGroup = 'Gestión de procesos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Número de fermentación')
                    ->placeholder('FERMENTACIÓN')
                    ->readOnly()
                    ->dehydrated(),

                Forms\Components\DatePicker::make('F_date_start')
                    ->label('Fecha de inicio')
                    ->required()
                    ->minDate(today()) // Permite seleccionar desde hoy en adelante
                    ->format('Y-m-d')
                    ->dehydrated(true)
                    ->validationAttribute('fecha de inicio')
                    ->rules(['after_or_equal:today']) // Valida que sea hoy o posterior
                    ->validationMessages(['after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.']),

                Forms\Components\DatePicker::make('F_date_end')
                    ->label('Fecha de finalización')
                    ->required()
                    ->minDate(function (callable $get) {
                        return Carbon::parse($get('F_date_start'))->addDays(1); // La fecha de finalización debe ser al menos un día después de la fecha de inicio
                    })
                    ->format('Y-m-d')
                    ->dehydrated(true)
                    ->validationAttribute('fecha de finalización')
                    ->rules(['after_or_equal:F_date_start']) // Valida que sea igual o posterior a la fecha de inicio
                    ->validationMessages(['after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.']),

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
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo')
                            ->required()
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(10)
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
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
                    ->label('Número de fermentación')
                    ->searchable(),
                Tables\Columns\TextColumn::make('F_date_start')
                    ->label('Fecha de inicio')
                    ->sortable(),
                Tables\Columns\TextColumn::make('F_date_end')
                    ->label('Fecha de finalización')
                    ->sortable(),
                Tables\Columns\TextColumn::make('worker.name')
                    ->label('Trabajador')
                    ->searchable(),
                    
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                ->label('Número de fermentación')
                ->options(
                    Fermentation_planning::pluck('name', 'name')->toArray()
                ),

                Tables\Filters\SelectFilter::make('F_date_start')
                    ->label('Fecha de inicio')
                    ->options(
                        Fermentation_planning::pluck('F_date_start', 'F_date_start')->toArray()
                    ),

                Tables\Filters\SelectFilter::make('F_date_end')
                    ->label('Fecha de finalización')
                    ->options(
                        Fermentation_planning::pluck('F_date_end', 'F_date_end')->toArray()
                    )
                    ,
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
                                Blade::render('planning.P_fermentation_report', ['records' => $records])
                            )->stream();
                        }, 'P_fermentation_report.pdf');
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
            RelationManagers\FermentationTrackingsRelationManager::class,   
            RelationManagers\FermentationRelationManager::class,    
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFermentationPlannings::route('/'),
            'create' => Pages\CreateFermentationPlanning::route('/create'),
            'edit' => Pages\EditFermentationPlanning::route('/{record}/edit'),
        ];
    }
}
