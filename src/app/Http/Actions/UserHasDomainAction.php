<?php

declare(strict_types=1);

namespace App\Http\Actions;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Contracts\Auth\Authenticatable;

class UserHasDomainAction
{
    /**
     * Handle the incoming request.
     *
     * @param string $domainName
     * @param Authenticatable $user
     * @return Model|null
     */
    public function __invoke(string $domainName, Authenticatable $user): ?Model
    {
        return $user->domains()->where('name', $domainName)->first();
    }
}
