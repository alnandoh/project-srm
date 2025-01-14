<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Filament\Notifications\Notification;

class ResetPassword extends RequestPasswordReset
{
    use WithRateLimiting;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    public function request(): void
    {
        $data = $this->form->getState();
        
        // Find the user
        $user = User::where('email', $data['email'])->first();
        
        if ($user) {
            // Set the new password
            $newPassword = '123456ab';
            $user->password = Hash::make($newPassword);
            $user->save();

            Notification::make()
                ->success()
                ->title('Success')
                ->body('Password has been reset successfully. Your new password is: ' . $newPassword)
                ->send();
            
            $this->redirect(route('filament.admin.auth.login'));
        } else {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('We could not find a user with that email address.')
                ->send();
        }
    }
}