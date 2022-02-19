<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Models\User;

class UserDeleteAction
{
    /**
     * Handle the incoming request.
     *
     * @param User $user
     * @return bool
     */
    public function __invoke(User $user): bool
    {
        return (bool)$user->delete();
    }
}
