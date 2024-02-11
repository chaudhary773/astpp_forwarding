<?php

namespace App\Policies;

use App\Models\User;

class ActivityPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(): bool
    {
        return auth()->id() == 1;
    }

    public function update(): bool
    {
        return auth()->id() == 1;
    }

    public function create(): bool
    {
        return auth()->id() == 1;
    }

    public function delete(): bool
    {
        return auth()->id() == 1;
    }
}
