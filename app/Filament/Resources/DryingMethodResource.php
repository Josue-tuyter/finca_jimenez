<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DryingMethodResource\Pages;
use App\Filament\Resources\DryingMethodResource\RelationManagers;
use App\Models\Drying_method;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DryingMethodResource extends Resource
{
    protected static ?string $model = Drying_method::class;

    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';

    protected static ?string $modelLabel = 'Metodos de secado';

    protected static ?string $navigationLabel = 'Metodos';

    protected static ?string $navigationGroup = 'otros';
    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->placeholder('Nombre del metodo de secado')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->placeholder('Descripción del metodo de secado')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->searchable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDryingMethods::route('/'),
        ];
    }
}
