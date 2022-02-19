<?php

declare(strict_types=1);

namespace App\Telegram\Bot\Validation;


class DomainNameValidation
{
    public static function validate(?string $input): object
    {
        $input = filter_var($input, FILTER_SANITIZE_STRING);
        $input = mb_strtolower(trim($input));

        $validator = validator(
            ['domain' => $input],
            ['domain' => ['required', 'min:4', 'max:191', 'regex:/^((?!-)[\p{L}0-9-]{1,63}(?<!-)\.)+[\p{L}]{2,6}$/iu']]
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
        $obj->input = $input;

        return $obj;
    }

}
