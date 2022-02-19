<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Validation;

use App\Models\User;
use Illuminate\Validation\Rule;

class UserDataValidation
{

    public static function validate(array $args, User $user): object
    {
        $validator = validator(
            ['telegram_username' => $args['telegram_username'], 'telegram_chat_id' => $args['telegram_chat_id']],
            [
                'telegram_username' => ['required', 'min:2', 'max:191', Rule::unique($user->getTable())],
                'telegram_chat_id' => ['required', Rule::unique($user->getTable())],
            ]
        );

        $valid = true;
        $errorMessages = [];

        if ($validator->fails()) {
            $valid = false;
            foreach ($validator->errors()->all() as $error) {
                $errorMessages[] = $error;
            }
        }

        $obj = new \stdClass;
        $obj->valid = $valid;
        $obj->errors = $errorMessages;
        $obj->input = $args;

        return $obj;
    }
}
