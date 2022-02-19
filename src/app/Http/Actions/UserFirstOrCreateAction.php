<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Models\User;
use App\Telegram\Bot\Validation\UserDataValidation;

class UserFirstOrCreateAction
{
    /**
     * Handle the incoming request.
     *
     * @param array $args
     * @return User
     */
    public function __invoke(array $args): ?User
    {
        $user = User::withTrashed()->where('telegram_chat_id', $args['telegram_chat_id'])->first();

        if ($user) {
            return $user;
        }

        $user = new User();

        $validated = UserDataValidation::validate($args, $user);

        if ($validated->valid) {
            $user->telegram_username = $args['telegram_username'];
            $user->telegram_chat_id = $args['telegram_chat_id'];
            $user->save();
        }

        return $user;
    }
}
