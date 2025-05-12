<?php

namespace App\Filament\Resources\FermentationPlanningResource\RelationManagers;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class FermentationTrackingsRelationManager extends RelationManager
{
    protected static string $relationship = 'fermentation_tracking';

    protected static ?string $title = 'Seguimiento';
    protected static ?string $label = 'Seguimiento de Fermentación';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_of_tracking')
                    ->placeholder('Fermentación')
                    ->maxLength(255)
                    ->label('Nombre del seguimiento')
                    ->required()
                    ->hint('Este campo es obligatorio')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('name_of_tracking', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ,

                Forms\Components\Select::make('harvest_id')
                    ->label('Nombre de la Cosecha')
                    ->relationship('harvest', 'name')
                    ->preload()
                    ->required()
                    ->hint('Este campo es obligatorio')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, callable $get) {
                        // Obtiene el peso y la temperatura de la cosecha y los asigna
                        $harvest = \App\Models\Harvest::find($state);
                        $weight = $harvest ? $harvest->weight : 0;
                        $temperature = $harvest ? $harvest->temperature : null;
                        $set('weight', $weight);
                        $set('total_weight', $weight + ($get('B_weight') ?? 0));
                    }),
                Forms\Components\TextInput::make('weight')
                    ->label('Peso de la Cosecha')
                    ->suffix('Lb')
                    ->readOnly()
                    ->default(0)
                    ->inputMode('decimal')
                    ->required(),

                Forms\Components\Select::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '<50%'=> 'Muy Baja: Humedad inferior al 50%.', 
                        '50% - 54%'=> 'Baja: Entre 50% y 54%', 
                        '55% - 60%'=> 'Óptima: Entre 55% y 60%',
                        '61% - 65%'=> 'Alta: Entre 61% y 65%',
                        '>65%'=> ' Muy Alta: Más del 65%',
                    ])
                    ->required(),

                    Forms\Components\Select::make('temperature')
                        ->label('Temperatura')
                        ->options([
                            '<40°C'=> 'Muy Baja: Menor a 40°C.', 
                            '40°C - 44°C'=> 'Baja: Entre 40°C y 44°C.', 
                            '45°C - 50°C'=> 'Óptima: Entre 45°C y 50°C.',
                            '51°C - 52°C'=> 'Alta: Entre 51°C y 52°C.',
                            '>52°C'=> 'Muy Alta: Mayor a 52°C',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('Buckest bought')
                        ->label('Cantidad de baldes comprados')
                        ->numeric()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state, callable $get) {
                            // Calcula el peso de los baldes (`B_weight`) y actualiza `total_weight`
                            $bWeight = $state ? $state * 50 : 0;
                            $set('B_weight', $bWeight);
                            $set('total_weight', $get('weight') + $bWeight);
                        })
                        ->dehydrateStateUsing(fn ($state) => null) // No guarda el estado en la base de datos
                        ->hiddenOn('save'), // Se oculta en la lista de datos a guardar

                    Forms\Components\TextInput::make('B_weight')
                        ->label('Peso de baldes comprados')
                        ->inputMode('decimal')
                        ->suffix('Lb')
                        ->numeric()
                        ->default(0) // Valor predeterminado de 0
                        ->readOnly(), // Hace el campo no editable, ya que se llena automáticamente

                    Forms\Components\TextInput::make('total_weight')
                        ->label('Peso Total')
                        ->suffix('Lb')
                        ->default(0)
                        ->required(),

                    Forms\Components\Select::make('location_id')
                        ->label('Lugar de fermentación')
                        ->searchable()
                        ->preload()
                        ->relationship('location', 'name')
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
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                                ,
                            Forms\Components\TextInput::make('description')
                                ->label('Descripción')
                                ->required()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $set('description', strtoupper($state));
                                    }
                                })
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                                ,
                        ])
                        ->required(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name_of_tracking')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('harvest.name')
                    ->label('Cosecha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Peso')
                    ->suffix('Lb')
                    ->sortable(),
                Tables\Columns\TextColumn::make('B_weight')
                    ->label('Peso de baldes')
                    ->suffix('Lb')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_weight')
                    ->label('Peso Total')
                    ->suffix('Lb')
                    ->sortable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humedad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('temperature')
                    ->label('Temperatura')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')                
                    ->label('Lugar')    
                    ->sortable(),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('harvest_id')
                    ->label('Cosecha')
                    ->relationship('harvest', 'name')
                    ->preload(),

                Tables\Filters\SelectFilter::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '<50%'=> 'Muy Baja: Humedad inferior al 50%.', 
                        '50% - 54%'=> 'Baja: Entre 50% y 54%', 
                        '55% - 60%'=> 'Óptima: Entre 55% y 60%',
                        '61% - 65%'=> 'Alta: Entre 61% y 65%',
                        '>65%'=> ' Muy Alta: Más del 65%',
                    ]),

                Tables\Filters\SelectFilter::make('temperature')
                    ->label('Temperatura')
                    ->options([
                        '<40°C'=> 'Muy Baja: Menor a 40°C.', 
                        '40°C - 44°C'=> 'Baja: Entre 40°C y 44°C.', 
                        '45°C - 50°C'=> 'Óptima: Entre 45°C y 50°C.',
                        '51°C - 52°C'=> 'Alta: Entre 51°C y 52°C.',
                        '>52°C'=> 'Muy Alta: Mayor a 52°C',
                    ]),
                
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Lugar de fermentación')
                    ->relationship('location', 'name')
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                                Blade::render('tracking.fermentationTracking_report', ['records' => $records])
                            )->stream();
                        }, 'fermentationTracking_report.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),

            ]);
    }
}
