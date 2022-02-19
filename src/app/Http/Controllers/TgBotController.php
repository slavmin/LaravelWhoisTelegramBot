<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Actions\UserFirstOrCreateAction;

class TgBotController extends Controller
{
    public function __invoke(): void
    {
        $tgBot = new Api(config('telegram.bots.uno.token'));

        $input = $tgBot->getWebhookUpdate();

        // Oh, no...
        if (blank($input->detectType()) || $input->getMessage()->get('is_bot')) {
            return;
        }

        // User authentication
        $userId = $input->getChat()->getId();
        $userName = $input->getChat()->getUsername();

        $appUser = (new UserFirstOrCreateAction)(
            ['telegram_chat_id' => $userId, 'telegram_username' => $userName]
        );

        // Authorize user
        if ($appUser) {
            auth()->onceUsingId($appUser->id);
        }

        // Bot in action
        Telegram::commandsHandler(true);

        if ($input->hasCommand()) {
            return;
        }

        if ($input->isType('callback_query')) {
            $callbackData = $input->callbackQuery->data;
            $callbackCommand = null;

            if (strpos($callbackData, '#') !== false) {
                [$callbackCommand] = explode('#', $callbackData);
            }

            switch ($callbackCommand) {
                case 'addDomain':
                    $tgBot->triggerCommand('add', $input);
                    break;
                case 'deleteDomain':
                    $tgBot->triggerCommand('delete', $input);
                    break;
                default:
                    return;
            }

            return;
        }

        $tgBot->triggerCommand('help', $input);

        return;
    }
}
