<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 2018/10/6
 * Time: 13:25
 */

namespace app\wxlogin\model;

use think\Model;
use think\Db;

class UserLoginRecord extends Model
{
    public function findDataBase($session)
    {
        $database = Db::name('user_login_record')->where('session', $session)->find();
        return $database;
    }
}