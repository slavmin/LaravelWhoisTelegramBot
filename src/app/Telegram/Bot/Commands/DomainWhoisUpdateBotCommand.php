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
class DomainWhoisUpdateBotCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'update';

    /**
     * @var array<string> Command Aliases
     */
    protected $aliases = [];

    /**
     * @var string Command Description
     */
    protected $description = 'Update command, update domain whois data';

    /**
     * {@inheritdoc}
     */
    public function handle(): Message
    {
        $input = str_replace('/' . $this->name, '', $this->getUpdate()->getMessage()->get('text'));

        $updated = false;

        $validated = DomainNameValidation::validate($input);

        if (!$validated->valid) {
            return $this->replyWithMessage(['text' => join(PHP_EOL, array_values($validated->errors))]);
        }

        $userHasDomain = (new \App\Http\Actions\UserHasDomainAction())($validated->input, auth()->user());

        if ($userHasDomain instanceof Domain) {
            $updated = (new \App\Http\Actions\DomainWhoisUpdateAction())($userHasDomain->uuid);
        }

        if ($updated) {
            return $this->replyWithMessage(['text' => trans('bot.domain_updated')]);
        }

        return $this->replyWithMessage(['text' => trans('bot.something_wrong')]);
    }
}
