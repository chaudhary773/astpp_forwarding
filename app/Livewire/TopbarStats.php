<?php

namespace App\Livewire;

use Illuminate\Support\Number;
use Livewire\Component;

class TopbarStats extends Component
{
    public function render()
    {
        return view('livewire.topbar-stats')->with([
            'serverTime' => now()->toDateTime()->format('M d, Y H:i'),
            'balance' =>  Number::currency(auth()->user()->balance, in: 'USD'),
        ]);
    }
}
