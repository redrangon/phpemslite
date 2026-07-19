<?php

namespace PHPEMS\Lib\Core\Request;


use PHPEMS\Lib\Config\Site\Site;


class RequestProvider
{
    private ?RequestInterface $request = null;

    public function __construct(?string $request = null)
    {
        if($request === null)
        {
            if(DI(Site::class)->responseType == 'json')$this->request = DI(Json::class);
            else $this->request = DI(Request::class);
        }
        else $this->request = DI($request);
    }

    public function getInstance(): RequestInterface
    {
        return $this->request;
    }
}