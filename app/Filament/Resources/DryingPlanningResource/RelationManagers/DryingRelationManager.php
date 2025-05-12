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

class DryingRelationManager extends RelationManager
{
    protected static string $relationship = 'Drying';

    protected static ?string $title = 'Finalización';
    protected static ?string $label = 'Registro Final de Secado';

    public function form(Form $form): Form
    {
        return $form
            ->schema([ 
                Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nombre')
                ->maxLength(255)
                ->placeholder('SE#')
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('name', strtoupper($state));
                    }
                })
                ->live(onBlur: true)
                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                ,

                    Forms\Components\TextInput::make('number_sacks')
                    ->label('Número de sacos')
                    ->required()
                    ->numeric()
                    ->inputMode('decimal')
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $weight = $state * 100;
                            $set('weight', $weight);
                        }
                    }),
                Forms\Components\TextInput::make('weight')
                    ->label('Peso')
                    ->required()
                    ->suffix('Lb')
                    ->numeric()
                    ->inputMode('decimal')
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
                Tables\Columns\TextColumn::make('number_sacks')
                    ->label('Número de sacos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                ->label('Peso')
                    ->sortable()
                    ->suffix('Lb'),

            ])
            ->filters([
                //
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
                                Blade::render('last.l_drying_report', ['records' => $records])
                            )->stream();
                        }, 'l_drying_report.pdf');
                    }),
                    ExportBulkAction::make()
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-table-cells')
                ]),
            ]);
    }
}
