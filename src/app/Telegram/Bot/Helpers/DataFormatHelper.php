<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Helpers;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Collection;

class DataFormatHelper
{
    /**
     * @param Collection $domains
     * @param array $callback_args
     * @return array|array[]
     */
    public static function formatDomainList(Collection $domains, array $callback_args = []): array
    {
        $domainArr = $buttonArr = [];

        foreach ($domains as $domain) {
            $domainArr[$domain->id] = static::formatDomain($domain);

            $callback_field = $callback_args['callback_field'];

            $buttonArr[$domain->id] = [
                [
                    [
                        'text' => $callback_args['text'],
                        'callback_data' => $callback_args['callback_text'] . $domain->{$callback_field},
                    ]
                ],
            ];
        }

        return [$domainArr, $buttonArr];
    }

    /**
     * @param Domain $domain
     * @return array|string[]
     */
    public static function formatDomain(Domain $domain): array
    {
        return [
            'name' => trans('bot.domain_name') . ': ' . $domain->name,
            'registrar' => trans('bot.domain_registrar') . ': ' . $domain->registrar,
            'created' => trans('bot.domain_created') . ': ' . $domain->created,
            'expires' => trans('bot.domain_expires') . ': ' . $domain->expires,
            'days_to_expire' => trans('bot.days_to_expire') . ': <b>' . $domain->days_to_expire . '</b>',
        ];
    }

    /**
     * @param string $text
     * @param string $callback_data
     * @return array|\array[][][]
     */
    public static function makeInlineButton(string $text, string $callback_data): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => $text,
                        'callback_data' => $callback_data,
                    ]
                ],
            ]
        ];
    }
}
