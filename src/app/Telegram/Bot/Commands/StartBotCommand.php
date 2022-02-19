<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Commands;

use Telegram\Bot\Objects\Message;
use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class StartBotCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var array<string> Command Aliases
     */
    protected $aliases = [];

    /**
     * @var string Command Description
     */
    protected $description = 'Start command, Get a list of commands';

    /**
     * {@inheritdoc}
     */
    public function handle(): Message
    {
        $commands = $this->telegram->getCommands();

        $text = trans('bot.start_description') . PHP_EOL . PHP_EOL;
        foreach ($commands as $name => $handler) {
            $text .= sprintf('/%s - %s' . PHP_EOL, $name, $handler->getDescription());
        }

        return $this->replyWithMessage(compact('text'));
    }
}
