<?php

namespace PHPEMS\Lib\Utils\Messenger;



class MessengerProvider
{
    static public function sendMessage(string $to, array $message): bool
    {
        return Mailer::sendMessage($to, $message);
    }
}