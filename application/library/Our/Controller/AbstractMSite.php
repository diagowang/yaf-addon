<?php
/**
 * MSite模块的抽象基类
 */
namespace Our\Controller;

abstract class AbstractMSite extends \Yaf_Controller_Abstract
{
    protected $_server = null;
    protected $_session = null;
    protected $_sessionUserInfo = null;

    public function init()
    {
        $this->_server = $_SERVER;
        $this->_session = \Yaf_Session::getInstance();
        $this->_sessionUserInfo = $this->_session->get(SESSION_USER_INFO_KEY);
    }
}

