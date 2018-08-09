<?php

namespace Error;
/**
 * User: JasonWong
 * Date: 2016/7/12 0012
 * Time: 14:39
 */
class CodeConfigModel
{
    const CANNOT_CONNECT_DATABASE = 100001;//连接数据库出错
    const CURL_REQUEST_ERROR = 100002;//curl请求出错
    const PARAMETERS_FORMAT_INCORRECT = 100003;//参数格式错误

    /**
     * 获取错误码信息
     * @param $code
     * @return mixed
     */
    public static function getCodeMsg($code)
    {
        static $config = null;
        if ($config === null) {
            $config = [
                100001 => 'Technical error',
                100002 => 'Technical error',
                100003 => 'The parameters format is incorrect',
            ];
        }
        return $config[$code];
    }
}