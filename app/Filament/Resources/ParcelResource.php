<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelResource\Pages;
use App\Filament\Resources\ParcelResource\RelationManagers;
use App\Models\Parcel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ParcelResource extends Resource
{
    protected static ?string $model = Parcel::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $modelLabel = 'Parcelas';

    protected static ?string $navigationLabel = 'Parcelas';

    protected static ?string $navigationGroup = 'otros';

    protected static ?int $navigationSort = 5;





    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(50),
            Forms\Components\TextInput::make('description')
                ->label('Descripción')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('length')
                ->label('Extención (ha)')
                ->numeric()
                ->inputMode('decimal')
                ->required(),
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
                Tables\Columns\TextColumn::make('length')
                ->label('Extención (ha)')
                ->suffix('ha')
                ->searchable()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                ->label('Nombre')
                ->preload()
                ->options(
                    Parcel::pluck('name', 'name')->toArray()
                ),
                Tables\Filters\SelectFilter::make('length')
                ->label('Longitud (ha)')
                ->preload()
                ->options(                
                    Parcel::pluck('length', 'length')->toArray()
                )
                ->preload(),
                Tables\Filters\SelectFilter::make('description')
                ->label('Descripción')
                ->options(Parcel::pluck('description', 'description')->toArray())
                ->preload(),
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
            'index' => Pages\ManageParcels::route('/'),
        ];
    }
}
