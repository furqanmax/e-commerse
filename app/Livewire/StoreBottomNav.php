<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class StoreBottomNav extends Component
{
    public function render(): View
    {
        return view('livewire.store-bottom-nav');
    }
}
