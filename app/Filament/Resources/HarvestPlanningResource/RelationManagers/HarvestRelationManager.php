<?php

namespace App\Filament\Resources\HarvestPlanningResource\RelationManagers;

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
use App\Models\Harvest;

class HarvestRelationManager extends RelationManager
{
    protected static string $relationship = 'Harvest';

    protected static ?string $title = 'Finalización';
    protected static ?string $label = 'Registro Final de Cosecha';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Registro final 1')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('name', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_buckests')
                    ->label('N# baldes')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $weight = $state * 50;
                            $set('weight', $weight);
                        }
                    }),
                Forms\Components\TextInput::make('weight')
                    ->label('Peso')
                    ->required()
                    ->numeric()
                    ->suffix('Lb')
                    ->inputMode('decimal')
                    ,
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_buckests')
                    ->label('N# baldes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Peso')
                    ->sortable()
                    ->suffix('Lb'),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label('Nombre')
                    ->options(
                        Harvest::pluck('name', 'name')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('number_buckests')
                    ->label('Numero de baldes')
                    ->options(
                        Harvest::pluck('number_buckests', 'number_buckests')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('weight')
                    ->label('Peso')
                    ->options(
                        Harvest::pluck('weight', 'weight')->toArray()
                    ),
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
                                Blade::render('last.l_harvest_report', ['records' => $records])
                            )->stream();
                        }, 'l_harvest_report.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),
            ]);
    }
}
