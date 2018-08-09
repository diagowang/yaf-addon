<?php

/**
 * User: JasonWong
 * Date: 2016/3/31 0031
 * Time: 17:20
 */
class UserController extends \Our\Controller\AbstractIndex
{
    public function indexAction()
    {
    }

    /**
     * 用户注册
     */
    public function registerAction()
    {
        \Yaf_Dispatcher::getInstance()->disableView();
        $service = \Service\User\RegisterModel::getInstance();
        $result = $service->register($this->getRequest()->getPost());
        exit(json_encode($result));
    }

    public function logoutAction()
    {
        \Service\User\LogoutModel::getInstance()->logout();
        session_destroy();
        $this->redirect('/index/index');
    }
    public function aboutUsAction()
    {
    }
}