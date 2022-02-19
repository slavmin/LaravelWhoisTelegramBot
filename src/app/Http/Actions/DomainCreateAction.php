<?php

declare(strict_types=1);

namespace App\Http\Actions;

use Carbon\Carbon;
use Iodev\Whois\Helpers\DomainHelper;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Contracts\Auth\Authenticatable;

class DomainCreateAction
{

    /**
     * @param string $domainName
     * @param Authenticatable $user
     * @return Model|null
     */
    public function __invoke(string $domainName, Authenticatable $user): ?Model
    {
        $checkDomain = (new \App\Http\Actions\DomainWhoisAction)($domainName);

        $domain = null;

        if (!empty($checkDomain->domainName)) {
            $domainName = DomainHelper::toUnicode($checkDomain->domainName);

            $createdDate = !empty($checkDomain->created) ? Carbon::createFromTimestamp(
                $checkDomain->created
            )->toDateTimeLocalString() : null;
            $expiresDate = !empty($checkDomain->expires) ? Carbon::createFromTimestamp(
                $checkDomain->expires
            )->toDateTimeLocalString() : null;

            $domainArr = [
                'name' => !empty($domainName) ? mb_strtolower($domainName) : mb_strtolower($checkDomain->domainName),
                'registrar' => $checkDomain->registrar,
                'states' => $checkDomain->states,
                'created' => $createdDate,
                'expires' => $expiresDate,
                'raw_data' => $checkDomain->raw_data,
            ];

            $domain = $user->domains()->create(array_filter($domainArr));
        }

        return $domain;
    }
}
