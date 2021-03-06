<?php

namespace App\Models;

use Illuminate\Support\Collection;
use App\Exceptions\UnluckyUserNotFoundException;

class Picker
{
    /**
     * Attempt to pick a user from a collection of users
     *
     * @return \App\Models\User
     * @throws \App\Exceptions\UnluckyUserNotFoundException
     */
    public static function pick(Collection $users)
    {
        if ($users->isEmpty() || !($user = $users->random())) {
            throw new UnluckyUserNotFoundException('Could not pick a user');
        }

        return $user;
    }
}
