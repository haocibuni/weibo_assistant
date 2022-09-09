<?php
/**
 * Created by PhpStorm.
 * User: xgzx
 * Date: 2018/7/17
 * Time: 9:16
 */

namespace app\common\service;

use think\Db;
use think\Session;

class Validate extends \think\Validate
{
    protected $regex = [
        'mobile'=>'/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|16[6]|(17[0,3,5-8])|(18[0-9])|19[89])\d{8}$/',
    ];
    /**
     * 已废弃
     * 利用用户id，过期时间生成Token
     * @param $id
     * @param $expire_time
     * @return string
     */
    public static function getToken($id, $expire_time)
    {
        return hash("sha256", $expire_time . $id);
    }
}