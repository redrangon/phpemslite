<?php

namespace PHPEMS\App\Attach\Controller\App;

use Exception;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

use PHPEMS\Lib\Utils\FileProvider;

class Upload extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth','Json'];
    static public function getRoutes():array
    {
        return [
            'index' => 'Index'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??static::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function Index():array
    {
        $service = new FileProvider();
        $file = $this->request->getFile('file');
        try{
            $result = $service->upload($file);
            if($result['success'])
            {
                return [
                    'path' => $result['path']
                ];
            }
            else error($result['error']);
        }catch (Exception $e){
            return ['error' => $e->getMessage()];
        }
    }
}