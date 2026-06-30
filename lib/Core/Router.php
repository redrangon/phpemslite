<?php

namespace PHPEMS\Lib\Core;
use PHPEMS\Lib\Config\Site\Site;
use PHPEMS\Lib\Core\Request\RequestProvider;
use PHPEMS\Lib\DI\DI;
use PHPEMS\Lib\Http\Cookie;
use PHPEMS\Lib\Rules\Error;

class Router
{
    public static ?Router $instance = null;
    public array $flows = [];
    public static function getInstance(): Router
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function regFlow(array $flow)
    {
        $router = self::getInstance();
        $router->flows = array_merge($router->flows, $flow);
    }

    public static function dispatch()
    {
        $router = self::getInstance();
        $request = DI('request');
        $route = $request->getRoute();
        if($route[0] === 'plugins')
        {
            $class = 'PHPEMS\Plugins\\' . ucfirst($route[1]??'') . '\\Controller\\' . ucfirst($route[2]). '\\' . ucfirst($route[3]);
            $action = $route[4]??'index';
        }
        else
        {
            $class = 'PHPEMS\\App\\' . ucfirst($route[0]) . '\\Controller\\' . ucfirst($route[1]). '\\' . ucfirst($route[2]);
            $action = $route[3]??'index';
        }
        if(class_exists($class))
        {
            $routes = $class::getRoutes();
            $flows = array_merge($router->flows,$class::withFlows($action));
            $flows = array_diff($flows,$class::withOutFlows($action));
            $flows = array_reverse($flows);
            if($routes[$action]??false)
            {
                $next = function() use ($routes,$action,$class) {
                    return (new $class())->{$routes[$action]}();
                };
            }
            else
            {
                $next = function() use ($class){
                    return (new $class())->index();
                };
            }
            foreach($flows as $flow)
            {
                if(str_contains($flow,'@'))
                {
                    $flow = explode('@', $flow);
                    $class = 'PHPEMS\\Lib\\Core\\Flow\\' . ucfirst($flow[0]) ;
                    $method = $flow[1];
                }
                else
                {
                    $class = 'PHPEMS\\Lib\\Core\\Flow\\' . ucfirst($flow) ;
                    $method = 'handle';
                }
                $next = function() use ($class,$method,$next) {
                    return DI($class)->$method($next);
                };
            }
            try
            {
                $result = $next();
                Disclose::export($result);
            }
            catch (\Exception $e)
            {
                Disclose::export(error(['error' => $e->getMessage()]));
                file_put_contents(
                    DI(Site::class)->errorLogPath,
                    date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n",
                    FILE_APPEND
                );
            }
        }else{
            http_response_code(404);
            echo "页面未找到";
            exit;
        }
    }
}