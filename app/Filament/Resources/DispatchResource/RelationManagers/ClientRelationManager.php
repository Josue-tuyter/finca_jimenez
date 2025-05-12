<?php

namespace App\Filament\Resources\DispatchResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ClientRelationManager extends RelationManager
{
    protected static string $relationship = 'client';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Correo'),
                tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->label('Telefono'),
                tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable()
                    ->label('Dirección'),
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
                        ExportBulkAction::make()
                            ->label('Exportar excel')
                            ->icon('heroicon-m-table-cells'),
                ]),
            ]);
    }
}
