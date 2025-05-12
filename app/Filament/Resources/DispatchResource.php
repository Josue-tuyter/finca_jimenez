<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DispatchResource\Pages;
use App\Filament\Resources\DispatchResource\RelationManagers;
use App\Models\Dispatch;
use App\Models\Drying;
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
use app\models\Client;
use Filament\Notifications\Notification;

class DispatchResource extends Resource
{
    protected static ?string $model = Dispatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-on-square-stack';

    protected static ?string $modelLabel = 'Despacho';

    protected static ?string $navigationLabel = 'Despachos';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Despachos y Gráficos';



    public static function form(Form $form): Form
    {
        $lastDryingSacks = Drying::latest('created_at')->value('available_sacks') ?? 0;

        // Show notification if no sacks available
        if ($lastDryingSacks <= 0) {
            Notification::make()
                ->warning()
                ->title('No hay sacos disponibles')
                ->body('No hay sacos disponibles para despachar en este momento.')
                ->persistent()
                ->send();
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la entrega')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('name', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ,
                Forms\Components\TextInput::make('number_sacks')
                    ->required()
                    ->numeric()
                    ->label('Número de sacos')
                    ->extraAttributes(['style' => 'font-size: 4rem;']) // Aumenta el tamaño del texto
                    ->default(0) // Cambiamos el valor predeterminado a 0
                    ->disabled($lastDryingSacks <= 0) // Disable if no sacks available
                    ->rule(function () use ($lastDryingSacks) {
                        return "max:$lastDryingSacks"; // Validación usando available_sacks
                    })
                    ->hint(function () use ($lastDryingSacks) {
                        return $lastDryingSacks > 0 
                            ? "Sacos disponibles para despachar: $lastDryingSacks"
                            : "No hay sacos disponibles para despachar";
                    }), // Mostramos available_sacks
                Forms\Components\DatePicker::make('delivery_date')
                    ->required()
                    ->format('Y-m-d')
                    ->dehydrated(true)
                    ->label('Fecha de entrega'),
                Forms\Components\Select::make('client_id')
                ->relationship('client', 'name')
                    ->label('Nombre del cliente')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->required()
                            ->maxLength(10)
                            ,
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->label('Dirección'),
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre de la entrega')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_sacks')
                    ->label('Número de sacos')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha de entrega')
                    ->sortable(),

            Tables\Columns\TextColumn::make('client.name')
                    ->label('Nombre del cliente')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('number_sacks')
                    ->label('Número de sacos')
                    ->options(Dispatch::pluck('number_sacks', 'number_sacks')->toArray()),
                Tables\Filters\SelectFilter::make('delivery_date')
                    ->label('Fecha de entrega')
                    ->options(Dispatch::pluck('delivery_date', 'delivery_date')->toArray()),
                
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
                                Blade::render('planning.dispatch', ['records' => $records])
                            )->stream();
                        }, 'dispatch.pdf');
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
            RelationManagers\ClientRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDispatches::route('/'),
            'create' => Pages\CreateDispatch::route('/create'),
            'edit' => Pages\EditDispatch::route('/{record}/edit'),
        ];
    }
}
