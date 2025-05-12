<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Hash;
use App\Notifications\NewUserNotification;


use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']); // Asegurar que la contraseña se almacene encriptada
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->record;
        $password = $this->data['password']; // Obtener la contraseña ingresada en el formulario

        // Enviar notificación por correo
        $user->notify(new NewUserNotification($user, $password));
    }

}
