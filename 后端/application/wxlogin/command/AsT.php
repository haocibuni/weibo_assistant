<?php
/**
 * Created by PhpStorm.
 * User: A
 * Date: 2018/11/24
 * Time: 9:45
 */

namespace app\wxlogin\command;

use app\common\controller\Index;
use think\Config;
use app\wxlogin\model\AccessToken;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

/**
 * @method Http($url, array $params)
 */
class AsT extends Command
{
    //默认配置
    protected $config = [
        'url' => "access_token url", //微信获取access_token接口url
        'appid' => 'your appId', // APPId
        'secret' => 'your secret', // 秘钥
        'grant_type' => 'authorization_code', // grant_type，一般情况下固定的
    ];

    /**
     * 构造函数
     * Index constructor.
     */
    public function __construct()
    {
        parent::__construct();
        //可设置配置项 wxmini, 此配置项为数组。
        if ($wx = Config::load(APP_PATH . 'wxlogin/config.php', 'wx_access_token')) {
            $this->config = array_merge($this->config, $wx["wx_access_token"]);
        }
    }

    protected function configure()
    {
        $this->setName('AsT')->setDescription("计划任务 AsT");
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('Date Crontab job start...');
        /*** 这里写计划任务列表集 START ***/

        $this->getAccessToken();

        /*** 这里写计划任务列表集 END ***/
        $output->writeln('Date Crontab job end...');
    }

    /**
     * 获取access_token
     */
    function getAccessToken(){
        $params = [
            'appid' => $this->config['appid'],
            'secret' => $this->config['secret'],
            'grant_type' => $this->config['grant_type']
        ];
        $Index=new Index();
        $r = $Index->Http($this->config["url"], $params);
        //返回的是字符串，需要用json_decode转换成数组
        $data = json_decode($r,true);
        $array = [
            'access_token' => $data["access_token"],
            'expires_time' => $data["expires_in"] + time(),
        ];
        $access_token = new AccessToken();
        $access_token->insert($array);
    }
}