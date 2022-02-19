<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Commands;

use App\Models\Domain;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Commands\Command;
use App\Telegram\Bot\Helpers\DataFormatHelper;
use App\Telegram\Bot\Validation\DomainNameValidation;

/**
 * Class HelpCommand.
 */
class DomainWhoisBotCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'whois';

    /**
     * @var array<string> Command Aliases
     */
    protected $aliases = [];

    /**
     * @var string Command Description
     */
    protected $description = 'Whois command, Get information about domain';

    /**
     * {@inheritdoc}
     */
    public function handle(): Message
    {
        $input = str_replace('/' . $this->name, '', $this->getUpdate()->getMessage()->get('text'));

        $validated = DomainNameValidation::validate($input);

        if (!$validated->valid) {
            return $this->replyWithMessage(['text' => join(PHP_EOL, array_values($validated->errors))]);
        }

        $response = (new \App\Http\Actions\DomainWhoisAction)($validated->input);

        if (!empty($response->errors)) {
            return $this->replyWithMessage(['text' => mb_substr($response->raw_data, 0, config('telegram.max_length'), mb_internal_encoding())]);
        }

        $userHasDomain = (new \App\Http\Actions\UserHasDomainAction())($validated->input, auth()->user());

        if (!$userHasDomain instanceof Domain) {
            $inlineKeyboard = DataFormatHelper::makeInlineButton(trans('bot.add_domain'), 'addDomain#' . $validated->input);
        }

        if (empty($response->errors) && !empty($response->raw_data)) {
            return $this->replyWithMessage(
                [
                    'text' => mb_substr($response->raw_data, 0, config('telegram.max_length'), mb_internal_encoding()),
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                    'disable_notification' => true,
                    'reply_to_message_id' => null,
                    'reply_markup' => !$userHasDomain instanceof Domain ? json_encode($inlineKeyboard) : null,
                ]
            );
        }

        return $this->replyWithMessage(['text' => trans('bot.something_wrong')]);
    }
}
