<?php
/**
 * Created by PhpStorm.
 * User: Gustav
 * Date: 2018/7/16
 * Time: 13:45
 */

namespace app\common\controller;

use app\common\controller\GlobalVariable;
use HttpResponseException;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;
use think\Response;
use think\Session;

class UserController extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $token = Request::instance()->header('token');
        try {
            $data = Db::name('user_manager_login_info')->where(['token' => $token])->find();
        } catch (DataNotFoundException $e) {
            return GlobalVariable::promptErrorByJSON("错误", 500, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            return GlobalVariable::promptErrorByJSON("错误", 500, $e->getMessage());
        } catch (DbException $e) {
            return GlobalVariable::promptErrorByJSON("错误", 500, $e->getMessage());
        }
        $bool_a = !$data || $data['expire_time'] < time();
        if ($bool_a) {
            return GlobalVariable::promptErrorByJSON("错误", 401, "未登录");
        }
    }
}

