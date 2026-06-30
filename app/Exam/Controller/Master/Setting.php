<?php

namespace PHPEMS\App\Exam\Controller\Master;

use PHPEMS\App\Core\Service\Model\App;
use PHPEMS\Lib\Core\Request\Json;
use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;
use PHPEMS\Lib\Rules\Error;

class Setting extends Controller implements ControllerInterface
{
    
    static protected array $publicFlows = ['Auth@admin','Json'];

    static public function getRoutes():array
    {
        return [
            'index' => 'Index',
            'setconfig' => 'setConfig',
            'getconfig' => 'getConfig'
        ];
    }

    static public function withFlows($action = 'index'):array
    {
        $flows = [];
        return $flows[$action]??self::$publicFlows;
    }

    static public function withOutFlows($action = 'index'):array
    {
        $outFlows = [];
        return $outFlows[$action]??[];
    }

    public function getConfig():array
    {
        $app = App::getAppByCode('exam');
        if(!$app->appid){
            $config = [
                'watermark' => 0,
                'fontsize' => 16,
                'color' => '#333333',
                'rotate' => 0
            ];
            $data = [
                'appcode' => 'exam',
                'appname' => '考试',
                'appsetting' => json_encode($config, JSON_UNESCAPED_UNICODE)
            ];
            $data = App::fill($data);
            App::getQuery()->insert($data);
        }
        else
        {
            $config = json_decode($app->appsetting, true)??[];
        }
        $config['watermark'] = (bool)$config['watermark'];
        return $config;
    }

    public function setConfig():array|Error
    {
        $config = [
            'watermark' => $this->request->watermark?1:0,
            'fontsize' => $this->request->fontsize??16,
            'color' => $this->request->color??'#333333',
            'rotate' => $this->request->rotate??0
        ];
        $app = App::getAppByCode('exam');
        $app->appsetting = json_encode($config, JSON_UNESCAPED_UNICODE);
        if(!$app->save())return error(['error' => '修改失败']);
        return ['msg'=>'设置成功'];
    }

    public function Index(): Error|array
    {
        return [];
    }
}
