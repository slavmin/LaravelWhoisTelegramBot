<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Commands;

use App\Models\Domain;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Commands\Command;
use App\Telegram\Bot\Validation\DomainNameValidation;

/**
 * Class HelpCommand.
 */
class DomainDeleteBotCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'delete';

    /**
     * @var array<string> Command Aliases
     */
    protected $aliases = [];

    /**
     * @var string Command Description
     */
    protected $description = 'Delete command, remove domain from observable list';

    /**
     * {@inheritdoc}
     */
    public function handle(): Message
    {
        if ($this->getUpdate()->isType('callback_query')) {
            [$callback, $input] = explode('#', $this->getUpdate()->callbackQuery->data);
        } else {
            $input = str_replace('/' . $this->name, '', $this->getUpdate()->getMessage()->get('text'));
        }

        $deleted = false;

        $validated = DomainNameValidation::validate($input);

        if (!$validated->valid) {
            return $this->replyWithMessage(['text' => join(PHP_EOL, array_values($validated->errors))]);
        }

        $userHasDomain = (new \App\Http\Actions\UserHasDomainAction())($validated->input, auth()->user());

        if ($userHasDomain instanceof Domain) {
            $deleted = (new \App\Http\Actions\DomainDeleteAction)($userHasDomain, auth()->user());
        }

        if ($deleted) {
            return $this->replyWithMessage(['text' => trans('bot.domain_deleted')]);
        }

        return $this->replyWithMessage(['text' => trans('bot.something_wrong')]);
    }
}
