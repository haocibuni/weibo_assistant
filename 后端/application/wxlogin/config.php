<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 2018/10/4
 * Time: 9:18
 */

return array(
    'wx_session_key' => array(
        'url' => "https://api.weixin.qq.com/sns/jscode2session", //微信获取session_key接口url
        'appid' => 'wxc859e116e42ec727', // APPId
        'secret' => 'bd9bad7e2e7e8c052ebb595aa0688264', // 秘钥
        'grant_type' => 'authorization_code', // grant_type，一般情况下固定的
    ),
    'wx_access_token' => array(
        'url' => "https://api.weixin.qq.com/cgi-bin/token", //微信获取access_token接口url
        "appid" => "wxc859e116e42ec727",  // APPId
        "secret" => "bd9bad7e2e7e8c052ebb595aa0688264", // 秘钥
        "grant_type" => "client_credential",
    ));