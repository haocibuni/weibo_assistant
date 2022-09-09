<?php

namespace app\wxlogin\controller;
/**
 * error code 说明.
 * <ul>

 *    <li>-41001: encodingAesKey 非法</li>
 *    <li>-41003: aes 解密失败</li>
 *    <li>-41004: 解密后得到的buffer非法</li>
 *    <li>-41005: base64加密失败</li>$RequestTokenFailed
 *    <li>-41006: base64解密失败</li>
 * </ul>
 */

class ErrorCode
{
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $RequestTokenFailed = -41005;
    public static $SignNotMatch = -41006;
    public static $EncryptDataNotMatch = -41007;
}
