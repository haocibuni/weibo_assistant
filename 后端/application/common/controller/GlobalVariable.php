<?php

/**
 * ┌────────────────────────────────────────────────────────────────────────────────────────┐
 * │      __           ____                                                                 │
 * │     /\ \       __/\  _`\                                   __                          │
 * │     \ \ \     /\_\ \,\L\_\    ___     ___      __      __ /\_\     __      ___         │
 * │      \ \ \  __\/\ \/_\__ \   / __`\ /' _ `\  /'_ `\  /'__`\/\ \  /'__`\  /' _ `\       │
 * │       \ \ \L\ \\ \ \/\ \L\ \/\ \L\ \/\ \/\ \/\ \L\ \/\ \L\ \ \ \/\ \L\.\_/\ \/\ \      │
 * │        \ \____/ \ \_\ `\____\ \____/\ \_\ \_\ \____ \ \___, \ \_\ \__/.\_\ \_\ \_\     │
 * │         \/___/   \/_/\/_____/\/___/  \/_/\/_/\/___L\ \/___/\ \/_/\/__/\/_/\/_/\/_/     │
 * │                                                /\____/    \ \_\                        │
 * │                                                \_/__/      \/_/                        │
 * │                                        __  __     _                                    │
 * │                         /'\_/`\       /\ \/\ \  /' \                                   │
 * │                        /\      \  _ __\ \ `\\ \/\_, \  __  __                          │
 * │                        \ \ \__\ \/\`'__\ \ , ` \/_/\ \/\ \/\ \                         │
 * │                         \ \ \_/\ \ \ \/ \ \ \`\ \ \ \ \ \ \_\ \                        │
 * │                          \ \_\\ \_\ \_\  \ \_\ \_\ \ \_\ \____/                        │
 * │                           \/_/ \/_/\/_/   \/_/\/_/  \/_/\/___/                         │
 * │                                                                                        │
 * └────────────────────────────────────────────────────────────────────────────────────────┘
 */

namespace app\common\controller;

use think\Config;
use think\Controller;
use think\exception\HttpResponseException;
use think\Response;

class GlobalVariable extends Controller
{

    /**
     * 防止直接访问GlobalVariable
     */
    public function _initialize()
    {
        return $this->redirect(url('/index'));
    }

    //HTTP状态码
    public static $httpStatus = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    ];

    public static function switchHTTPStatusCode($statusCode)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: X-Requested-With,Content-Type,XX-Device-Type,Token,GUID,Sid');
        header('Access-Control-Allow-Methods: GET,POST,PATCH,PUT,DELETE,OPTIONS');

        if (!array_key_exists($statusCode, GlobalVariable::$httpStatus)) return;
        $statusCode = 200;
        $httpStatusString = GlobalVariable::$httpStatus[$statusCode];
        $statusString = "Status: " . substr($httpStatusString, 9);
        header($httpStatusString);
        header($statusString);
    }

    /**
     * @return mixed
     * 登陆成功!
     */
    public static function promptOkStatus()
    {
        self::switchHTTPStatusCode(200);
        $result = [
            "status" => "成功",
            "statusCode" => 200,
            "reason" => "成功",
        ];
        self::end($result);
    }

    /**
     * @param $status
     * @param $statusCode
     * @param $reason
     * @return mixed
     * 登录失败!
     */
    public static function promptErrorByJSON($status, $statusCode, $reason)
    {
        GlobalVariable::switchHTTPStatusCode($statusCode);
        $result = [
            "status" => $status,
            "statusCode" => $statusCode,
            "reason" => $reason,
        ];
        self::end($result);
    }

    public static function srcFromCDN($src)
    {
        return "http://cdn.ujnxgzx.com/" . $src;
    }

    public static function srcFromLocalPath($src)
    {
        return "/" . $src;
    }

    public static function get_client_ip($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP']) {//nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        return Config::get('default_return_type');
    }

    public static function promptData($data)
    {
        self::switchHTTPStatusCode(200);
        $result = [
            "status" => "成功",
            "statusCode" => 200,
            "data" => $data
        ];
        self::end($result);
    }

    protected static function end($result)
    {
        $response = Response::create($result, 'json');
        throw new HttpResponseException($response);
    }
}