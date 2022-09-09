<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 2018/10/6
 * Time: 16:08
 */

namespace app\wxlogin\controller;

use think\Controller;
use app\common\controller\GlobalVariable;
use app\wxlogin\model\UserLoginRecord;
use app\wxlogin\model\UserProposal;

class User extends Controller
{
    protected $user_id;

    protected function _initialize()
    {
        parent::_initialize();
        $session = $this->request->header('session');
        if (empty($session)) {
            return GlobalVariable::promptErrorByJSON("错误", 401, "传入参数不正确");
        }
        $userLoginRecord = new UserLoginRecord();
        $database = $userLoginRecord->where('session', $session)->find();
        $user_id = $database['user_id'];
        $data = isset($database[0]) ? $database[0] : NULL;
        if ($data == NULL) {
            return GlobalVariable::promptErrorByJSON("错误", 401, "传入参数不正确");
        }
        if ($user_id == NULL) {
            return GlobalVariable::promptErrorByJSON("错误", 401, "未注册");
        }
        $this->user_id = $user_id;
    }

    /**
     * [Post] 填写建议和意见
     */
    public function index(){
        $proposal = input("post.proposal", '', 'htmlspecialchars_decode');
        $userProposal = new UserProposal();
        $time = time();
        $array = [
            'user_id' => $this->user_id,
            'proposal' => $proposal,
            'create_time' => $time,
        ];
        $userProposal->insert($array);
        return GlobalVariable::promptOkStatus();
    }
}