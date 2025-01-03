<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Illuminate\Support\Facades\Password;

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

        $status = Password::sendResetLink(
            $data
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->notify('success', __($status));
            $this->redirect(route('filament.admin.auth.login'));
        } else {
            $this->notify('danger', __($status));
        }
    }
}