<?php

namespace PHPEMS\App\Core\Controller\Utils;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\Captcha;
use PHPEMS\Lib\Utils\Messenger\MessengerProvider;

class Index extends Controller implements ControllerInterface
{
    static public function getRoutes():array
    {
        return [
            'captcha' => 'Captcha',
            'send' => 'SendMessage',
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        return ['Json'];
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function SendMessage():array | Error
    {
        $to = $this->request->email??null;
        $message = [
            'subject' => '',
            'content' => '',
        ];
        $result = MessengerProvider::sendMessage($to,$message);
        if($result === true)return [];
        else return $result;
    }

    public function captcha():array
    {
        $captcha = new Captcha();
        $image = $captcha->create();
        $id = $captcha->getId();
        return ['id' => $id, 'image' => $image];
    }

    public function index(): array
    {
        return [];
    }
}