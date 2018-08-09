<?php

namespace Our\Util;
/**
 *
 */
class Common
{
    /**
     * 获取http状态码
     * @param int $code
     * @return string
     */
    public static function getHttpStatusCode(int $code)
    {
        $httpStatusCodes = array(
            100 => 'HTTP/1.1 100 Continue',
            101 => 'HTTP/1.1 101 Switching Protocols',
            200 => 'HTTP/1.1 200 OK',
            201 => 'HTTP/1.1 201 Created',
            202 => 'HTTP/1.1 202 Accepted',
            203 => 'HTTP/1.1 203 Non-Authoritative Information',
            204 => 'HTTP/1.1 204 No Content',
            205 => 'HTTP/1.1 205 Reset Content',
            206 => 'HTTP/1.1 206 Partial Content',
            300 => 'HTTP/1.1 300 Multiple Choices',
            301 => 'HTTP/1.1 301 Moved Permanently',
            302 => 'HTTP/1.1 302 Found',
            303 => 'HTTP/1.1 303 See Other',
            304 => 'HTTP/1.1 304 Not Modified',
            305 => 'HTTP/1.1 305 Use Proxy',
            307 => 'HTTP/1.1 307 Temporary Redirect',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            402 => 'HTTP/1.1 402 Payment Required',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            405 => 'HTTP/1.1 405 Method Not Allowed',
            406 => 'HTTP/1.1 406 Not Acceptable',
            407 => 'HTTP/1.1 407 Proxy Authentication Required',
            408 => 'HTTP/1.1 408 Request Time-out',
            409 => 'HTTP/1.1 409 Conflict',
            410 => 'HTTP/1.1 410 Gone',
            411 => 'HTTP/1.1 411 Length Required',
            412 => 'HTTP/1.1 412 Precondition Failed',
            413 => 'HTTP/1.1 413 Request Entity Too Large',
            414 => 'HTTP/1.1 414 Request-URI Too Large',
            415 => 'HTTP/1.1 415 Unsupported Media Type',
            416 => 'HTTP/1.1 416 Requested range not satisfiable',
            417 => 'HTTP/1.1 417 Expectation Failed',
            500 => 'HTTP/1.1 500 Internal Server Error',
            501 => 'HTTP/1.1 501 Not Implemented',
            502 => 'HTTP/1.1 502 Bad Gateway',
            503 => 'HTTP/1.1 503 Service Unavailable',
            504 => 'HTTP/1.1 504 Gateway Time-out'
        );

        return $httpStatusCodes[$code] ?? '';
    }

    public static function getCountry($key)
    {
        static $country = null;
        if ($country === null) {
            $country = include APPLICATION_PATH . '/conf/country.php';
        }
        return $country[$key];
    }

    public static function getComm($key)
    {
        static $comm = null;
        if ($comm === null) {

            $comm = include APPLICATION_PATH . '/conf/comm.php';
        }
        return $comm[$key];
    }

    /**
     * 获取客户端IP
     * @param  boolean $checkProxy
     * @return string
     */
    public static function getClientIp($checkProxy = true)
    {
        if ($checkProxy && isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != null) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if ($checkProxy && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != null) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    /**
     * 对数组中含有特殊字符的数据进行转义，支持多维数组
     * @param array $data
     */
    public static function convertHTMLSpecialChars(array &$data = null)
    {
        if (!empty($data) && is_array($data)) {
            array_walk($data, function (&$value, $key) {
                if (is_array($value)) {
                    self::convertHTMLSpecialChars($value);
                } else {
                    $value = htmlspecialchars($value);
                }
            });
        }
    }

    /**
     * 递归的trim数组的值
     * @param array $data
     * @return bool
     */
    public static function trimArrayRecursive(array &$data)
    {
        return array_walk_recursive($data, function (&$value, $key) {
            $value = trim($value);
        });
    }

    /**
     * IP地址转换为浮点数
     * @param string $dotted
     * @return float|int
     */
    public static function covertIPAddressToNumber(string $dotted)
    {
        $dotted = preg_split("/[.]+/", $dotted);
        $ip = (double)($dotted[0] * 16777216) + ($dotted[1] * 65536) + ($dotted[2] * 256) + ($dotted[3]);
        return $ip;
    }

    /**
     * IP地址浮点数转换为IP地址字符串
     * @param float $number
     * @return string
     */
    public static function covertNumberToIPAddress($number)
    {
        $a = ($number / 16777216) % 256;
        $b = ($number / 65536) % 256;
        $c = ($number / 256) % 256;
        $d = $number - $a * 16777216 - $b * 65536 - $c * 256;
        $dotted = $a . "." . $b . "." . $c . "." . $d;
        return $dotted;
    }



}