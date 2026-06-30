<?php

namespace PHPEMS\Lib\Utils\Messenger;

use PHPEMS\Lib\Rules\Error;

interface MessengerInterface
{
    static function sendMessage(string $to, array $message): bool | Error;
}