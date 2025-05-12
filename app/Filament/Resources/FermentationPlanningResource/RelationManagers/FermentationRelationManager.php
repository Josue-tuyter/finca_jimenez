<?php

namespace App\Filament\Resources\FermentationPlanningResource\RelationManagers;

use App\Models\Fermentation_tracking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class FermentationRelationManager extends RelationManager
{
    protected static string $relationship = 'Fermentation';
    protected static ?string $title = 'Finalización';
    protected static ?string $label = 'Registro Final de Fermentación';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('F_name')
                    ->label('Nombre')
                    ->maxLength(255)
                    ->placeholder('Fermentacion')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('F_name', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ,
                Forms\Components\Select::make('humidity')
                    ->label('Humedad')
                    ->required()
                    ->options([
                        '<50%'=> 'Muy Baja: Humedad inferior al 50%.', 
                        '50% - 54%'=> 'Baja: Entre 50% y 54%', 
                        '55% - 60%'=> 'Óptima: Entre 55% y 60%',
                        '61% - 65%'=> 'Alta: Entre 61% y 65%',
                        '>65%'=> ' Muy Alta: Más del 65%',
                    ]),
                Forms\Components\Select::make('fermentation_tracking_id')
                    ->label('Tracking de Fermentación')
                    ->options(Fermentation_tracking::pluck('name_of_tracking', 'id')) // Ajusta 'name' al campo que representa el nombre del tracking
                    ->searchable()
                    ->required()
                    ->reactive() // Permite que cambie dinámicamente otros campos
                    ->afterStateUpdated(fn (callable $set, $state) => 
                        $set('F_total_weight', Fermentation_tracking::find($state)?->total_weight ?? 0)
                    ),
        
                Forms\Components\TextInput::make('F_total_weight')
                    ->label('Peso Total')
                    ->suffix('Lb')
                    ->numeric(),
                    
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('F_name')
            ->columns([
                Tables\Columns\TextColumn::make('F_name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humedad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('F_total_weight')
                    ->label('Peso total')
                    ->suffix('Lb')
                    ,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '<50%'=> 'Muy Baja: Humedad inferior al 50%.', 
                        '50% - 54%'=> 'Baja: Entre 50% y 54%', 
                        '55% - 60%'=> 'Óptima: Entre 55% y 60%',
                        '61% - 65%'=> 'Alta: Entre 61% y 65%',
                        '>65%'=> ' Muy Alta: Más del 65%',
                    ]),
                
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
                                Blade::render('last.l_fermentation_report', ['records' => $records])
                            )->stream();
                        }, 'Reporte_final_Fermentacion.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),

            ]);
    }
}
