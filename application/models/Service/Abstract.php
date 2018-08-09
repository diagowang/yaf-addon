<?php
namespace Service;
/**
 * User: JasonWong
 * Date: 2016/6/17 0017
 * Time: 14:40
 */
abstract class AbstractModel
{
    /**
     * 不允许克隆对象
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed!', E_USER_ERROR);
    }
    abstract public static function getInstance();
}