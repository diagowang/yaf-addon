<?php
/**
 * 默认模块的抽象基类
 */
namespace Our\Controller;

use Service\User\LoginModel;

abstract class AbstractIndex extends \Yaf_Controller_Abstract
{
    protected $_server = null;
    protected $_session = null;
    protected $_controllerName = '';//全部小写的controllerName
    protected $_actionName = '';
    protected $_sessionUserInfo = null;

    public function init()
    {
        //这里检测session的有效性以及访问控制权限
        $this->_server = $_SERVER; //$this->getRequest()->getServer();
        $this->_session = \Yaf_Session::getInstance();
        $serverInfo = $this->getRequest()->getServer();
        $this->_controllerName = strtolower($this->getRequest()->getControllerName());
        $this->_actionName = $this->getRequest()->getActionName();
        $this->_sessionUserInfo = $this->_session->get(SESSION_USER_INFO_KEY);
        if (!$this->_isOpenACL() && !$this->_sessionUserInfo) {
            //TODO 自动登录
            if (!$this->_sessionUserInfo) {
                if (!$this->getRequest()->isXmlHttpRequest() && strpos($serverInfo['HTTP_ACCEPT'], 'json') === false) {
                    $this->redirect('/user/login');
                    exit;
                } else {//异步ajax请求
                    header('Content-Type: application/json; charset=UTF-8');
                    exit(json_encode(array('success' => false, 'code' => '1000', 'msg' => '此次登录已失效，请重新登录！', 'redirect_url' => '/user/login')));
                }
            }
        }
        $this->getView()->assign('sessionUserInfo', $this->_sessionUserInfo);
    }

    /**
     * 检测是否是开放访问控制列表
     * @return bool
     */
    private function _isOpenACL()
    {
        static $config = null;
        if ($config === null) {
            $config = include APPLICATION_PATH . '/conf/open.acl.index.php';
        }
        if (isset($config[$this->_controllerName]) && in_array($this->_actionName, $config[$this->_controllerName])) {
            return true;
        }
        return false;
    }

}

