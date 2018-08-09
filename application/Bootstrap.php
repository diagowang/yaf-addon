<?php
/**
 * Bootstrap引导程序
 * 所有在Bootstrap类中定义的, 以_init开头的方法, 都会被依次调用
 * 而这些方法都可以接受一个Yaf_Dispatcher实例作为参数.
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{
    /**
     * 把配置存到注册表
     */
    public function _initConfig()
    {
        $config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $config);
    }

    /**
     * 根据域名设置默认module
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initDefaultModuleName(Yaf_Dispatcher $dispatcher)
    {
        $server = $dispatcher->getRequest()->getServer();
        $config = Yaf_Registry::get('config');
        if ($server['SERVER_NAME'] == $config['resources']['modules']['App']['domain']) {
            $dispatcher->getRequest()->setModuleName('App');
        } else if ($server['SERVER_NAME'] == $config['resources']['modules']['MSite']['domain']) {
            $dispatcher->getRequest()->setModuleName('MSite');
        }
    }

    /**
     * 重写session存储机制
     */
    public function _initSessionHandler()
    {
        ini_set('session.save_handler', 'redis');
        ini_set('session.name', 'myName');
        $handler = new \Our\Util\OurSessionHandler();
        session_set_save_handler($handler, true);
    }

    /**
     * 路由定义规则
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/route.ini', 'common');
        if ($config->routes) {
            $router = $dispatcher->getRouter();
            $router->addConfig($config->routes);
        }
    }

    /**
     * 获取url.ini配置的地址
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public static function getUrlIniConfig($name)
    {
        static $config = null;
        if($config === null){
            $config = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/url.ini', ini_get('yaf.environ'));
        }
        $urlConfig = $config->get('config.url');
        if ($urlConfig === null) {
            throw new \Exception('config.url does not exists');
        }
        if($urlConfig[$name] === null){
            throw new \Exception('config.url.'.$name.' does not exists');
        }
        return $urlConfig[$name];
    }
}