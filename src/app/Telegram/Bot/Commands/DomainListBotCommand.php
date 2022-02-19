<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Commands;

use Telegram\Bot\Objects\Message;
use Telegram\Bot\Commands\Command;
use App\Telegram\Bot\Helpers\DataFormatHelper;

/**
 * Class HelpCommand.
 */
class DomainListBotCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'list';

    /**
     * @var array<string> Command Aliases
     */
    protected $aliases = [];

    /**
     * @var string Command Description
     */
    protected $description = 'List command, show observable domains list';

    /**
     * {@inheritdoc}
     */
    public function handle(): ?Message
    {
        $userDomains = auth()->user()->domains()->orderBy('expires', 'asc')->get();

        if (blank($userDomains)) {
            return $this->replyWithMessage(['text' => trans('bot.empty_domain_list')]);
        }

        [$domainArr, $buttonArr] = DataFormatHelper::formatDomainList(
            $userDomains,
            ['text' => trans('bot.delete_domain'), 'callback_text' => 'deleteDomain#', 'callback_field' => 'name']
        );

        if (!blank($domainArr)) {
            foreach ($domainArr as $domainID => $domainData) {
                $inlineKeyboard = ['inline_keyboard' => $buttonArr[$domainID]];
                $this->replyWithMessage(
                    [
                        'text' => join("\n", array_values($domainData)),
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true,
                        'disable_notification' => true,
                        'reply_to_message_id' => null,
                        'reply_markup' => json_encode($inlineKeyboard),
                    ]
                );
            }

            return null;
        }

        return $this->replyWithMessage(['text' => trans('bot.something_wrong')]);
    }
}
