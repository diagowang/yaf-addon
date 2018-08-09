<?php

/**
 * User: JasonWong
 * Date: 2016/7/21 0021
 * Time: 10:59
 */
namespace Http;

use Error\CodeConfigModel;
use Error\ErrorModel;

abstract class AbstractModel
{
    /**
     * 访问的host
     * @var string
     */
    protected $_host = '';

    /**
     * 发起HTTP请求
     * @param $url
     * @param string $method
     * @param array $params
     * @param int $timeout
     * @param array $extParams
     * @return mixed
     * @throws \Error\OurExceptionModel
     * @throws \Exception
     */
    protected function _request($url, $method = 'GET', $params = array(), $timeout = 30, $extParams = array())
    {
        $url = $this->_host . $url;
        $paramString = $params;
        if (is_array($params)) {
            $paramString = http_build_query($params, '', '&');
        }
        if (strtoupper($method) == 'GET' && $params) {
            $url .= '?' . $paramString;
        }

        $ch = curl_init($url);

        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);//支持毫秒级超时，见鸟哥的博客

        if (!empty($extParams['cookies'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->analyzeCookie($extParams['cookies']));
        }

        //检测是否是https访问
        if (strpos($url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $result = curl_exec($ch);

        //请求失败的处理方法
        if (curl_errno($ch)) {
            ErrorModel::throwException(CodeConfigModel::CURL_REQUEST_ERROR);
        }
        curl_close($ch);
        return $result;
    }

    /**
     * 异步请求
     * @param $url
     * @param array $postData
     * @param array $cookie
     * @return bool
     */
    protected function _requestAsync($url, array $postData, array $cookie)
    {
        $url = $this->_host . $url;
        $method = "GET"; //可以通过POST或者GET传递一些参数给要触发的脚本
        $urlArray = parse_url($url); //获取URL信息，以便平凑HTTP HEADER
        $port = isset($urlArray['port']) ? $urlArray['port'] : 80;

        $fp = fsockopen($urlArray['host'], $port, $errNo, $errStr, 30);
        if (!$fp) {
            return false;
        }
        $getPath = $urlArray['path'] . "?" . $urlArray['query'];
        if (!empty($postData)) {
            $method = "POST";
        }
        $header = $method . " " . $getPath;
        $header .= " HTTP/1.1\r\n";
        $header .= "Host: " . $urlArray['host'] . "\r\n"; //HTTP 1.1 Host域不能省略
        /**//*以下头信息域可以省略
        $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
        $header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
        $header .= "Accept-Language: en-us,en;q=0.5 ";
        $header .= "Accept-Encoding: gzip,deflate\r\n";
         */
        $header .= "Connection:Close\r\n";
        if (!empty($cookie)) {
            $_cookie = strval(null);
            foreach ($cookie as $k => $v) {
                $_cookie .= $k . "=" . $v . "; ";
            }
            $cookie_str = "Cookie: " . base64_encode($_cookie) . "\r\n"; //传递Cookie
            $header .= $cookie_str;
        }
        if (!empty($postData)) {
            $_post = "";
            if (is_string($postData)) {
                $_post .= $postData;
            } else if (is_array($postData)) {
                foreach ($postData as $k => $v) {
                    $_post .= $k . "=" . $v . "&";
                }
            }
            $postStr = "Content-Type: application/x-www-form-urlencoded\r\n"; //POST数据
            //$postStr  = "";//POST数据
            $postStr .= "Content-Length: " . strlen($_post) . "\r\n\r\n"; //POST数据的长度
            $postStr .= $_post . "\r\n"; //传递POST数据
            $header .= $postStr;
        }
        fwrite($fp, $header);
        //echo fread($fp, 1024); //我们不关心服务器返回
        fclose($fp);
        return true;
    }

    /**
     * 解析cookie数组，转换成字符串形式
     * @param array $cookies
     * @return string
     */
    public function analyzeCookie(array $cookies)
    {
        $cookie = '';
        foreach ($cookies as $key => $value) {
            $cookie = $key . '=' . $value . '; ';
        }
        return substr($cookie, 0, strlen($cookie) - 2);
    }

    /**
     * 获取主机地址
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * 设置主机地址
     * @param $host
     */
    public function setHost($host)
    {
        $this->_host = $host;
    }

    /**
     * 禁止克隆
     */
    public function __clone()
    {
        trigger_error('Clone is not allowed!', E_USER_ERROR);
    }

}