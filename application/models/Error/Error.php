<?php
/**
 * 异常错误处理类
 */
namespace Error;
class ErrorModel
{
    public static function throwException(int $code, string $message = null)
    {
        if(!$message){
            $message = CodeConfigModel::getCodeMsg($code);
            if(empty($message)){
                throw new \Exception('Unknown error(' . $code . ').');
            }
        }
        throw new OurExceptionModel($message, $code);
    }

}