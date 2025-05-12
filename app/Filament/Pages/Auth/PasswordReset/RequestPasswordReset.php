<?php

namespace App\Filament\Pages\Auth\PasswordReset;

use Filament\Forms\Components\TextInput;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Password;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;

//use Filament\Pages\Auth\EditProfile as BaseEditProfile;


class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form->schema([$this->getEmailFormComponent()]);
    }

    public function request(): void
    {
        $data = $this->form->getState();

        $status = Password::sendResetLink(
            $data,
            function($user, $token) {
                Mail::to($user->email)->send(new ResetPassword($token, $user->email));
            }
        );

        if ($status === Password::RESET_LINK_SENT) {
            Notification::make()->success()->title('Password reset link sent')->send();
            
            $this->form->fill();
        } else {
            Notification::make()->danger()->title('Error sending reset link')->send();
        }
    }
}
