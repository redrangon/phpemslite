<?php

namespace PHPEMS\App\Attach\Controller\App;

use Exception;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;
use PHPEMS\Lib\Utils\FileProvider;

class Upload extends Controller implements ControllerInterface
{
    static public function getRoutes():array
    {
        return [
            'index' => 'Index'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        return ['Auth','Json'];
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [
            'index' => ['Auth']
        ];
        return $outFlows[$action]??[];
    }

    public function Index():array | Error
    {
        $request = DI('request');
        $service = new FileProvider();
        $file = $request->getFile('file');
        try{
            $result = $service->upload($file);
            if($result['success'])
            {
                return [
                    'path' => $result['path']
                ];
            }
            else return error(['error' => $result['error']]);
        }catch (Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}