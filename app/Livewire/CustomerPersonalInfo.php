<?php
namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

//use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;
//use Jeffgreco13\FilamentBreezy\PersonalInfo;

class CustomerPersonalInfo extends PersonalInfo
{
    public array $only = ['first_name', 'last_name', 'address_1', 'city', 'postal_code', 'email', 'balance', 'telephone_1'];
    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->userClass = get_class($this->user);
        $this->hasAvatars = filament('filament-breezy')->hasAvatars();

        if ($this->hasAvatars) {
            $this->only[] = filament('filament-breezy')->getAvatarUploadComponent()->getStatePath(false);
        }

        $this->form->fill($this->user->only($this->only));
    }



    protected function getProfileFormSchema()
    {
        $groupFields = Forms\Components\Group::make([
            $this->getNameComponent(),
            Forms\Components\TextInput::make('last_name')
                ->label(__('Last Name')),
            $this->getEmailComponent(),
             Forms\Components\TextInput::make('balance')
                ->label(__('Balance'))->readOnly(),
            Forms\Components\TextInput::make('address_1')
                ->label(__('Address')),
            Forms\Components\TextInput::make('city')
                ->label(__('City')),
            Forms\Components\TextInput::make('postal_code')
            ->label(__('Postal Code')),
            Forms\Components\TextInput::make('telephone_1')
                ->label(__('Phone Number')),
        ])->columnSpan(2);

        return ($this->hasAvatars)
            ? [filament('filament-breezy')->getAvatarUploadComponent(), $groupFields]
            : [$groupFields];
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->except('balance')->toArray();
        $this->user->update($data);
        $this->sendNotification();
    }
    protected function getNameComponent(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('first_name')
            ->required();
    }

    protected function getEmailComponent(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('email')
            ->required();
    }


}
