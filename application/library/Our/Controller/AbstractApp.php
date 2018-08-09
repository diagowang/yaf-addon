<?php
/**
 * App模块的抽象基类
 */
namespace Our\Controller;

abstract class AbstractApp extends \Yaf_Controller_Abstract
{
    protected $_server = null;
    protected $_session = null;
    protected $_controllerName = '';//全部小写的controllerName
    protected $_actionName = '';
    protected $_sessionUserInfo = null;
    protected $_deviceInfo = null;
    public function init()
    {
    }
}

