<?php
namespace Mysql;
/**
 * User: JasonWong
 * Date: 2017/11/27 0027
 * Time: 10:15
 */
interface InterfaceModel
{
    function beginTransaction();
    function commit();
    function rollback();
}