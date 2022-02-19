<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Models\Domain;
use Illuminate\Contracts\Auth\Authenticatable;

class DomainDeleteAction
{
    /**
     * Handle the incoming request.
     *
     * @param Domain $domain
     * @param Authenticatable $user
     * @return bool
     */
    public function __invoke(Domain $domain, Authenticatable $user): bool
    {
        return (bool)$user->domains()->where('uuid', $domain->uuid)->delete();
    }
}
