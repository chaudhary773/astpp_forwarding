<?php

namespace App\Livewire;

use App\Models\DailyCdr;
use Livewire\Component;

class TopbarStats extends Component
{
    public function render()
    {
        return view('livewire.topbar-stats')->with([
            'serverTime' => now()->toDateTime()->format('M d, Y H:i'),
            'total' => DailyCdr::where('date', now()->format('Y-m-d'))->count(),
        ]);
    }
}
