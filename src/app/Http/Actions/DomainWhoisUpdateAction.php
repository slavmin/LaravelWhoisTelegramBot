<?php

declare(strict_types=1);

namespace App\Http\Actions;

use Carbon\Carbon;
use App\Models\Domain;

class DomainWhoisUpdateAction
{

    /**
     * @param string $uuid
     * @return Domain|null
     */
    public function __invoke(string $uuid): ?Domain
    {
        $domain = Domain::where('uuid', $uuid)->first();

        if ($domain) {
            $checkDomain = (new \App\Http\Actions\DomainWhoisAction)($domain->name);

            if (!empty($checkDomain->domainName)) {
                $createdDate = !empty($checkDomain->created) ? Carbon::createFromTimestamp(
                    $checkDomain->created
                )->toDateTimeLocalString() : null;
                $expiresDate = !empty($checkDomain->expires) ? Carbon::createFromTimestamp(
                    $checkDomain->expires
                )->toDateTimeLocalString() : null;

                $domainArr = [
                    'registrar' => $checkDomain->registrar,
                    'states' => $checkDomain->states,
                    'created' => $createdDate,
                    'expires' => $expiresDate,
                    'raw_data' => $checkDomain->raw_data,
                ];

                $domain->update(array_filter($domainArr));
            }
        }

        return $domain;
    }
}
