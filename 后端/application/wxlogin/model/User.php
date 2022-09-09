<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 2018/10/6
 * Time: 15:34
 */

namespace app\wxlogin\model;

use app\common\controller\GlobalVariable;
use think\Model;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;

class User extends Model
{
    /**
     * 设置用户信息
     * @param $data
     * @throws DataNotFoundException
     * @throws DbException
     * @throws Exception
     * @throws ModelNotFoundException
     * @throws PDOException
     */
    public function setInfo($data)
    {
        $status = true;
        if (!empty($data)) {
            $open_id = $data['openId'];
            $info = Db::name('user')->where('open_id', $open_id)->find();
            if ($info) {
                $array = [
                    'nick_name' => $data['nickName'],
                    'avatar_url' => $data['avatarUrl'],
                    'sex' => $data['gender'],
//                    'real_name' => $data['realName'],
//                    'phone_number' => $data['phoneNumber'],
                ];
                Db::name('user')->where('open_id', $open_id)->update($array);
//                if ($info['status'] == 0) {
//                    $status = false;
//                } else {
//                    $status = true;
//                }
                return $status;
            } else {
                $array = [
                    'open_id' => $data['openId'],
                    'nick_name' => $data['nickName'],
                    'avatar_url' => $data['avatarUrl'],
                    'sex' => $data['gender'],
//                    'real_name' => $data['realName'],
//                    'phone_number' => $data['phoneNumber'],
                    'create_time' => time(),
//                    'status' => 0
                ];
                $this->insert($array);
                return $status;
            }
        } else {
            return GlobalVariable::promptErrorByJSON("错误", 500, "信息传入为空！");
        }
    }

    /**
     * 用户登录
     * @param $data
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login($data)
    {
        if (!empty($data)) {
            $open_id = $data['openId'];
            $info = Db::name('user')->where('open_id', $open_id)->find();
            if (!empty($info)) {
                $array = [
                    'user_id' => $info['id'],
                    'session' => $data['session3rd'],
                    'session_key' => $data['sessionKey'],
                    'create_time' => $data['watermark']['timestamp']
                ];
                Db::name('UserLoginRecord')->insert($array);
            }
        }
    }

    /**
     * 判断open_id是否存在
     * @param $data
     * @return bool|mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function judgeInfo($data)
    {
        if (!empty($data)) {
            $open_id = $data['openId'];
            $info = Db::name('user')->where('open_id', $open_id)->find();
            if ($info) {
                return true;
            } else {
                return false;
            }
        } else {
            return GlobalVariable::promptErrorByJSON("错误", 500, "信息传入为空！");
        }
    }
}