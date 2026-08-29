<?php

namespace PHPEMS\Lib\Utils\Messenger;



interface MessengerInterface
{
    static function sendMessage(string $to, array $message): bool | Error;
}