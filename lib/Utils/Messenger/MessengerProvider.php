<?php

namespace PHPEMS\Lib\Utils\Messenger;

use PHPEMS\Lib\Rules\Error;

class MessengerProvider
{
    static public function sendMessage(string $to, array $message): bool | Error
    {
        return true;
        return Mailer::sendMessage($to, $message);
    }
}