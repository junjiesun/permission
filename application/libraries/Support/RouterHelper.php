<?php

namespace Lib\Support;
use Kerisy\Core\Router;

class RouterHelper
{
    public  $router;

    public function __construct()
    {
        $this->router = Router::getInstance();
//        var_dump($this->router);
    }
    /*
        route : array('prefix'='Admin', 'module'=>'core' ,'controller'=> 'index', 'action'=>'index')
     */
    public function getUrlByRoute($route = array())
    {
        $prefix     = array_key_exists('prefix', $route) ? $route['prefix'] : $this->router->getDefaultPrefix();
        $module     = array_key_exists('module', $route) ? $route['module'] : $this->router->getDefaultModule();
        $controller = array_key_exists('controller', $route) ? $route['controller'] : $this->router->getDefaultController();
        $action     = array_key_exists('action', $route) ? $route['action'] : $this->router->getDefaultAction();
        
        $routeStr = strtolower($module.'/'.$controller.'/'.$action);
//        var_dump($routeStr);
        $url = '';
        $routeConfig = config('routes');
        foreach ($routeConfig as $key => $routes) 
        {
//            var_dump($routes);
            if($routes['prefix'] == $prefix)
            {   
                foreach ($routes['routes'] as $row) 
                {
                    if(strtolower($row[2]) === strtolower($routeStr))
                    {
                        $url = $row[1];
                        break 2;
                    }
                }
            }
        }
//        var_dump($url);
        return $url;
    }
} 