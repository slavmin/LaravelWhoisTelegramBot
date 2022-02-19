<?php

declare(strict_types=1);

namespace App\Http\Actions;

use Iodev\Whois\Factory;
use Iodev\Whois\Helpers\DomainHelper;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;

class DomainWhoisAction
{
    /**
     * Handle the incoming request.
     *
     * @param string $domainName
     * @return Object
     */
    public function __invoke(string $domainName): object
    {
        $obj = new \stdClass;

        // Creating default configured client
        $whois = Factory::get()->createWhois();

        try {
            // Getting parsed domain info
            $info = $whois->loadDomainInfo($domainName);
            // Getting raw-text lookup
            $response = $whois->lookupDomain($domainName);
        } catch (\Throwable $e) {
            $obj->errors = $e->getMessage();
            $obj->raw_data = $e->getMessage();
            return $obj;
        }

        if (!empty($info)) {
            $obj->domainName = $info->domainName;
            $obj->registrar = $info->registrar ?? null;
            $obj->states = $info->states ?? null;
            $obj->owner = $info->owner ?? null;
            $obj->created = $info->creationDate ?? null;
            $obj->expires = $info->expirationDate ?? null;
            $obj->raw_data = $response->text ? filter_var($response->text, FILTER_SANITIZE_STRING) : null;
        }

        return $obj;
    }
}
