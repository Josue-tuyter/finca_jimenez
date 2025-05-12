<?php
namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExportColumn;
use Filament\Facades\Filament;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkersExport;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;











class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Trabajadores';

    protected static ?string $navigationLabel = 'Trabajadores';

    protected static ?string $navigationGroup = 'otros';
    protected static ?int $navigationSort = 5;
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                ->email()
                ->label('Correo')
                ->placeholder('Correo del usuario')
                ->required()
                ->unique()
                ->maxLength(255)
                ->live(onBlur: true)
                ->rules(['email:rfc,dns']) // Validates email format and DNS records
                ->validationMessages([
                    'required' => 'El correo es obligatorio.',
                    'email' => 'El correo debe ser una dirección válida.',
                    'unique' => 'El correo ya está registrado.',
                ])
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('email', strtoupper($state));
                    }
                })
                ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                ,
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('cedula')
                ->required()
                ->minLength(10)
                ->maxLength(10)
                ->label('Cédula')
                ->placeholder('Ejemplo: 0305863624')
                ->rules(['required', 'regex:/^[0-9]{10}$/'])
                ->validationAttribute('cedula')
                ->afterStateUpdated(function (?string $state, callable $set) {
                    if ($state !== null && strlen($state) === 10 && ctype_digit($state)) {
                        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
                        $suma = 0;

                        for ($i = 0; $i < 9; $i++) {
                            $valor = $coeficientes[$i] * intval($state[$i]);
                            $suma += ($valor >= 10) ? ($valor - 9) : $valor;
                        }

                        $digitoVerificador = (($suma % 10) === 0) ? 0 : (10 - ($suma % 10));

                        if ($digitoVerificador !== intval($state[9])) {
                            $set('cedula', '');
                            $set('error', 'La cédula ingresada no es válida.');
                        } else {
                            $set('error', null);
                        }
                    } else {
                        $set('error', 'La cédula debe contener solo 10 dígitos numéricos.');
                    }
                })
                ->reactive()
                ->suffix(function (callable $get) {
                    return $get('error') ? '❌' : '✔️';
                })
                ->validationMessages([
                    'regex' => 'La cédula debe contener solo 10 dígitos numéricos',
                ]),
            Forms\Components\TextInput::make('phone')
                ->label('Teléfono')
                ->tel()
                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                ->required()
                ->maxLength(10)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cedula')
                    ->label('Cédula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                ->label('Correo')
                ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('email')
                    ->label('Correo')
                    ->preload()
                    ->options(
                        Worker::pluck('email', 'email')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('name')
                    ->label('Nombre')
                    ->preload()
                    ->options(
                        Worker::pluck('name', 'name')->toArray()
                    ),
                Tables\Filters\SelectFilter::make('phone')
                    ->label('Teléfono')
                    ->preload()
                    ->options(
                        Worker::pluck('phone', 'phone')->toArray()
                    ),
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
            'index' => Pages\ManageWorkers::route('/'),
        ];
    }
}
