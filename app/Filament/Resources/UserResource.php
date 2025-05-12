<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Usuarios';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nombre')
                    ->maxLength(255)
                    ->placeholder('Nombre del usuario')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('name', strtoupper($state));
                        }
                    })
                    ->live(onBlur: true)
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ,
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->label('Correo')
                    ->placeholder('Correo del usuario')
                    ->required()
                    //->unique()
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
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Contraseña')
                    ->required()
                    ->maxLength(255)
                    ->revealable(),
                Forms\Components\TextInput::make('number')
                    ->tel()
                    ->required()
                    ->minLength(10)
                    ->maxLength(10)
                    ->label('Número de teléfono')
                    ->placeholder('Ejemplo: 0912345678')
                    ->rules(['required', 'regex:/^[0-9]{10}$/'])
                    ->validationMessages([
                        'regex' => 'El número debe contener solo 10 dígitos numéricos',
                    ]),
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
                    ])
                    ,

                    Forms\Components\Select::make('role')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->label('Rol')
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Get permissions for selected roles
                        $permissions = \Spatie\Permission\Models\Role::whereIn('id', $state)
                            ->with('permissions')
                            ->get()
                            ->pluck('permissions')
                            ->flatten()
                            ->pluck('name')
                            ->unique()
                            ->toArray();
                            
                        $set('permissions', $permissions);
                    })
                    ->default(function ($record) {
                        return $record?->roles->pluck('id')->toArray() ?? [];
                    }),

                Forms\Components\Select::make('permissions')
                    ->multiple()
                    ->options(function (callable $get) {
                        // Show permissions of selected roles
                        $roleIds = $get('role');
                        if (!$roleIds) return [];
                        
                        return \Spatie\Permission\Models\Role::whereIn('id', $roleIds)
                            ->with('permissions')
                            ->get()
                            ->pluck('permissions')
                            ->flatten()
                            ->pluck('name', 'name')
                            ->toArray();
                    })
                    ->default(function ($record) {
                        return $record?->permissions->pluck('name')->toArray() ?? [];
                    })
                    ->label('Permisos')
                    ->disabled()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cedula')
                    ->label('Cédula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('number')
                    ->label('telf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('name')
                    ->label('Nombre')
                    ->options(
                        User::all()->pluck('name', 'id')
                    ),
                    Tables\Filters\SelectFilter::make('email')
                    ->label('Correo electrónico')
                    ->options(
                        User::all()->pluck('email','email')
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('verify')
                ->label('Verificar')
                ->icon('heroicon-o-check-badge')
                ->action(function (User $record) {
                    $record->email_verified_at = now();
                    $record->save();
                }),
                Tables\Actions\Action::make('Unverify')
                    ->label('Desverificar') 
                    ->icon('heroicon-o-x-circle')
                    ->action(function (User $record) {
                        $record->email_verified_at = null;
                        $record->save();
                    }),
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
                                Blade::render('planning.users', ['records' => $records])
                            )->stream();
                        }, 'Usuarios.pdf');
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
