<?php

namespace App\Filament\Resources\DryingPlanningResource\RelationManagers;


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

class DryingTrackingsRelationManager extends RelationManager
{
    protected static string $relationship = 'drying_trackings';

    protected static ?string $title = 'Seguimiento';
    protected static ?string $label = 'Seguimiento de Secado';

    public function form(Form $form): Form

    
    {
        return $form 
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre secado')
                    ->placeholder('SECADO')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('name', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ,
                    
                Forms\Components\Select::make('fermentations')
                    ->label('Fermentaciones a Secar')
                    ->multiple()
                    ->relationship('fermentations', 'F_name')
                    //->options(\App\Models\Fermentation::all()->pluck('F_name', 'id')->toArray())
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "Lote: {$record->F_name} - Peso: {$record->f_total_weight} Lb")
                    ->live() // Esto hace que se actualice automáticamente en Livewire
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('total_weight', \App\Models\Fermentation::whereIn('id', $state)->sum('f_total_weight') . " Lb")
                    )
                    ->dehydrateStateUsing(fn ($state) => null), // Evita que se envíe en el formulario

                Forms\Components\TextInput::make('total_weight')
                    ->label('Peso Total')
                    ->disabled()
                    ->default('0 Lb')
                    ->dehydrated(false),
                    
                Forms\Components\Select::make('drying_method_id')
                    ->relationship('drying_method', 'name')
                    ->label('Metodo de secado')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->required(),
                Forms\Components\Select::make('humidity')
                    ->label('Humedad')
                    ->required()
                    ->options([
                        '<6%' => 'Muy Bajo: inferior al 6%', 
                        '6% - 8%' => 'Óptimo: Entre el 6% y el 8% (rango ideal)', 
                        '8% - 10%' => 'Alto: Entre el 8% y el 10%', 
                        '>10%' => 'Muy Alto: Mayor al 10%',
                    ])
                    ,
                Forms\Components\Select::make('color')
                    ->required()
                    ->options([
                        'Gris' => 'Gris/Moteado (secado incompleto, fermentación incompleta)',
                        'Purpura' => 'Púrpura/Violáceo (inicio del secado)',
                        'Marron claro' => 'Marrón Claro (progreso en el secado)',
                        'Marron oscuro' => 'Marrón Oscuro (secado óptimo, buena calidad)',
                        'Chocolate' => 'Chocolate Oscuro (secado y fermentación completos)',
                    ]),
                Forms\Components\Select::make('moho')
                    ->required()
                    ->options([
                        'Sin moho'=> 'Libre de  moho',
                        'Con moho'=> 'Contiene moho',
                    ]),
                Forms\Components\Select::make('textura')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options([
                        'Pegajosa' => 'Pegajosa (indica exceso de humedad)',
                        'Aspera' => 'Áspera (posible secado insuficiente o mal controlado)',
                        'Poco aspera' => 'Ligeramente Áspera (textura adecuada, secado en buen estado)',
                        'Suave' => 'Suave (sin asperezas, indica buena calidad de secado)',
                        'Quebradiza' => 'Quebradiza (cacao demasiado seco, puede afectar la calidad)',
                    ])
                    ->label('Textura'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('drying_method.name')
                    ->label('Metodo de secado')
                    ->searchable()
                    ,

                Tables\Columns\TextColumn::make('fermentations.F_name')
                    ->label('Fermentaciones')
                    ->listWithLineBreaks()
                    ->searchable(),

                Tables\Columns\TextColumn::make('humidity')
                    ->label('Humedad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('moho')
                    ->label('Moho')
                    ->searchable(),
                Tables\Columns\TextColumn::make('textura')
                    ->label('Textura')
                    ->searchable(),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('color')
                ->label('Color')
                ->options([
                    'Gris' => 'Gris/Moteado (secado incompleto, posible moho)',
                    'Purple' => 'Púrpura/Violáceo (inicio del secado, fermentación incompleta)',
                    'Marron claro' => 'Marrón Claro (progreso en el secado)',
                    'Marron oscuro' => 'Marrón Oscuro (secado óptimo, buena calidad)',
                    'Chocolate' => 'Chocolate Oscuro (secado y fermentación completos)'
                ]),
                Tables\Filters\SelectFilter::make('humidity')
                    ->label('Humedad')
                    ->options([
                        '<6%' => 'Muy Bajo: inferior al 6%', 
                        '6% - 8%' => 'Óptimo: Entre el 6% y el 8%', 
                        '8% - 10%' => 'Alto: Entre el 8% y el 10%', 
                        '>10%' => 'Muy Alto: Mayor al 10%',
                    ]),
                Tables\Filters\SelectFilter::make('moho')
                    ->label('Moho')
                    ->options([
                        'Sin moho'=> 'Libre de moho',
                        'Con moho'=> 'Contiene moho',
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
                                Blade::render('tracking.DryingTracking_report', ['records' => $records])
                            )->stream();
                        }, 'DryingTracking_report.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),
            ]);
    }
}
