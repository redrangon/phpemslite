<?php

namespace PHPEMS\App\Core\Controller\Master;

use PHPEMS\Lib\Rules\Controller;
use PHPEMS\Lib\Rules\ControllerInterface;

class App extends Controller implements ControllerInterface
{
    static protected array $publicFlows = ['Auth@admin','Json'];
    static public function getRoutes():array
    {
        return [
            'data' => 'Data',
            'setting' => 'Setting',
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

    public function Setting(): array
    {
        $request = DI('request');
        $data = \PHPEMS\App\Core\Service\Model\App::fill([
            'appid' => $request->appid,
            'appname' => $request->appname,
            'appcode' => $request->appcode,
            'appstatus' => $request->appstatus
        ]);
        if($data['appid'])
        {
            $data = array_filter($data,function($value){
                return $value !== null;
            });
        }
        $app = new \PHPEMS\App\Core\Service\Model\App($data);
        $app->save();
        return ['message' => '保存成功'];
    }

    public function Data(): array
    {
        $dirs = array_filter(glob(PEPATH."/app/*"), 'is_dir');
        $dirs = array_map('basename', $dirs);
        $apps = [];
        foreach($dirs as $dir)
        {
            $apps[$dir] = [
                'appid' => 0,
                'appcode' => $dir,
                'appname' => $dir,
                'appstatus' => 0
            ];
        }
        $settings = \PHPEMS\App\Core\Service\Model\App::getAll();
        foreach ($settings as $setting) {
            if(($setting['appcode']??false) && isset($apps[$setting['appcode']]))
            {
                $apps[$setting['appcode']] = [
                    'appid' => $setting['appid'],
                    'appname' => $setting['appname'],
                    'appcode' => $setting['appcode'],
                    'appstatus' => $setting['appstatus']
                ];
            }
        }
        return array_values($apps);
    }

    public function index():array
    {
        return [
            'tpl' => 'App',
        ];
    }
}