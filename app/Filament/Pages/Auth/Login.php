<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Filament\Schemas\Components\RenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getTitle(): string|Htmlable
    {
        return 'Login - SIMPEL SAPA KAMU';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->hiddenLabel()
            ->placeholder('Username')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes([
                'tabindex' => 1,
                'class' => 'text-center',
                'style' => 'border-radius: 25px; text-align: center;'
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->hiddenLabel()
            ->placeholder('Password')
            ->password()
            ->revealable(false)
            ->required()
            ->extraInputAttributes([
                'tabindex' => 2,
                'class' => 'text-center',
                'style' => 'border-radius: 25px; text-align: center;'
            ]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Ingat Saya')
            ->hidden();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Header Image
                Html::make(new HtmlString('
                    <div style="width: 100vw; position: relative; left: 50%; transform: translateX(-50%); text-align: center; margin-bottom: 0.25rem;">
                        <img src="' . asset('images/pejabat-header.png') . '" alt="Pejabat Kabupaten Malinau" style="max-height: 265px; display: inline-block;">
                    </div>
                    <div style="width: 100vw; position: relative; left: 50%; transform: translateX(-50%); text-align: center; margin-bottom: 0.25rem;">
                        <h1 style="color: #00A3E0; font-size: 2.8rem; font-weight: bold; letter-spacing: 2px; font-family: Arial, sans-serif; margin: 0; display: inline-block;">
                            SIMPEL SAPA KAMU
                        </h1>
                    </div>
                ')),

                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE),
                $this->getFormContentComponent(),
                $this->getMultiFactorChallengeFormContentComponent(),
                RenderHook::make(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER),
            ]);
    }
}
