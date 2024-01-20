<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use \Jeffgreco13\FilamentBreezy\Livewire\UpdatePassword;

class CustomerUpdatePassword extends UpdatePassword
{
        public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make("current_password")
                    ->label(__('filament-breezy::default.password_confirm.current_password'))
                    ->required()
                    ->password()
                    ->revealable()
                    ->rule("current_password")
                    ->visible(filament('filament-breezy')->getPasswordUpdateRequiresCurrent()),
                Forms\Components\TextInput::make("new_password")
                    ->label(__('filament-breezy::default.fields.new_password'))
                    ->password()
                    ->revealable()
                    ->rules(filament('filament-breezy')->getPasswordUpdateRules())
                    ->required(),
                Forms\Components\TextInput::make("new_password_confirmation")
                    ->label(__('filament-breezy::default.fields.new_password_confirmation'))
                    ->password()
                    ->revealable()
                    ->same("new_password")
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = collect($this->form->getState())->only('new_password')->all();
      //  dd($data);
        $this->user->update([
            'secret' => $data['new_password'],
            'password' => Hash::make($data['new_password'])
        ]);

        session()->forget('password_hash_' . Filament::getCurrentPanel()->getAuthGuard());
        Filament::auth()->login($this->user);
        $this->reset(["data"]);
        Notification::make()
            ->success()
            ->title(__('filament-breezy::default.profile.password.notify'))
            ->send();
    }
}
