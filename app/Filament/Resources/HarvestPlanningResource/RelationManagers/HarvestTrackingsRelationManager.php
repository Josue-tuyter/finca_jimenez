<?php

namespace App\Filament\Resources\HarvestPlanningResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class HarvestTrackingsRelationManager extends RelationManager
{
    protected static string $relationship = 'Harvest_tracking';

    protected static ?string $title = 'Seguimiento';
    protected static ?string $label = 'Seguimiento de Cosecha';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('size')
                    ->label('Tamaño')
                    ->options([
                        '12–15 cm'=> 'Pequeñas:12–15 cm.', 
                        '16–20 cm'=> 'Medianas:16–20 cm', 
                        '21–30 cm'=> 'Grandes:21–30 cm o más',
                    ])
                    ->required(),


                Forms\Components\Select::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '<50%'=> 'Muy Bajo menos de 50%', 
                        '50% - 59%'=> 'Bajo:  Entre el 50% y el 59%.', 
                        '60% - 69%'=> 'Medio(óptimo): Entre el 60% y el 69%',
                        '70% - 79%'=> 'Medio-alto (óptimo): Entre el 70% y el 79%.',
                        '>80%'=> 'Muy Alto: Más del 80%.',
                    ])
                    ->required(),
                Forms\Components\Select::make('disease')
                    ->label('Enfermedad')
                    ->options([
                        'Ninguna'=> 'Sin Enfermedad', 
                        'Moniliophthora roreri'=> 'Moniliasis', 
                        'Moniliophthora perniciosa'=> 'Escoba de Bruja', 
                        'Phytophthora spp'=> 'Mancha Negra',

                    ])
                    ->required(),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('size')
            ->columns([
                Tables\Columns\TextColumn::make('size')
                    ->sortable()
                    ->searchable()
                    ->label('Tamaño'),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humedad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disease')
                    ->label('Enfermedad')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '55%'=> 'Muy Bajo 55%', 
                        '55% - 59%'=> 'Bajo:  Entre el 55% y el 59%.', 
                        '66% - 70%'=> 'Normal: Entre el 66% y el 70%.',
                        '>70%'=> 'Muy Alto: Más del 70%.',
                    ])
                    ->label('Peso'),
                Tables\Filters\SelectFilter::make('disease')
                    ->label('Enfermedad')
                    ->options([
                        'Ninguna'=> 'Sin Enfermedad', 
                        'Moniliophthora roreri'=> 'Moniliasis', 
                        'Moniliophthora perniciosa'=> 'Escoba de Bruja', 
                        'Phytophthora spp'=> 'Mancha Negra',
                    ])
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),

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
                                Blade::render('tracking.harvestTracking_report', ['records' => $records])
                            )->stream();
                        }, 'harvestTracking_report.pdf');
                    }),
                    
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),

            ]);
    }
}
