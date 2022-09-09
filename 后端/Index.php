<?php

namespace app\index\controller;

use app\common\controller\GlobalVariable;
use think\Db;

class Index
{
    public function index()
    {
        // 关键字爬虫启动
        $set_charset = 'export LANG=zh_CN.UTF-8;';
        $time = time();
        $openId = input("get.openId");
        $keyword = input("get.keyword");
        $timestamp = input("get.timestamp");
        $user_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('id');
    	$kw_depth = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('kw_depth');
    	if($kw_depth>80){
    		$kw_depth=4;
    	}
    	else if($kw_depth<=40){
    		$kw_depth=2;
    	}
    	else{
    		$kw_depth=3;
    	}
        $cmd = 'cd /www/wwwroot/weixin.haocibuni.cn/public/python/keyword_spider/sina/spiders; 
             python3 -m scrapy crawl weibo_spider -a keyword="' . $keyword . '"  -a userid=' . $user_id . ' -a kwdepth=' . $kw_depth . ' -a  searchtime=' . $time . ' 2>&1 ';
        $do = exec($set_charset . $cmd, $ret, $out);
        
        $up = [];
        $down = [];
        $neutral = [];
        $ave = 0;
        $selectKeyword = Db::name('keyword')
        ->where([
            'keyword' => $keyword,
            'search_time' => $time,
            'user_id' => $user_id
        ])
        ->select();
        
        $myfile = fopen("/www/wwwroot/weixin.haocibuni.cn/public/python/wordCloud/txt/".$openId.$timestamp.".txt", "w") or die("Unable to open file!");
        $txt = "";
        fwrite($myfile, $txt);
		fclose($myfile);
		
		$myfile = fopen("/www/wwwroot/weixin.haocibuni.cn/public/python/wordCloud/txt/".$openId.$timestamp.".txt", "a+") or die("Unable to open file!");
        foreach ($selectKeyword as $item) {
        	// 将文本录入txt
			$txt = $item['content'];
			fwrite($myfile, $txt);
			
            if ($item['keyword_value'] >= 2/3) {
                array_push($up, $item);
            }
            else if ($item['keyword_value'] < 1/3) {
                array_push($down, $item);
            }
            else{
            	array_push($neutral, $item);
            }
            $ave += $item['keyword_value'];
        }
        $ave = $ave /count($selectKeyword);
        
        fclose($myfile);
    	$cmd2 = "cd /www/wwwroot/weixin.haocibuni.cn/public/python/wordCloud; python3 wordCloud.py ".$openId.$timestamp;
    	exec($cmd2);
     
        $record=[
        	'user_id'=>$user_id,
        	'keyword'=>$keyword,
        	'date' =>$time,
        	'ave_value'=>round($ave*100,0),
        	];
        $insertrecord = Db::name('keyword_record')
        ->insert($record,true);
        
        $deletekeyword =  Db::name('keyword')
        ->where([
        	'user_id' => $user_id,
        ])
        ->delete();
        
        $message = [
            'up' => $up,
            'down' => $down,
            'neutral' => $neutral,
            'ave' => round($ave*100,0),
            'up_count' =>count($up),
            'down_count' =>count($down),
            'neutral_count' =>count($neutral),
        ];
        return GlobalVariable::promptData($message);
        
    }
    public function analyse()
    {
        $set_charset = 'export LANG=zh_CN.UTF-8;';
        $openId = input("get.openId");
        $time = time();
        $weibopage_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('weibopage_id');
    	$user_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('id');
    	$pg_depth = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('pg_depth');
    	if($pg_depth>80){
    		$pg_depth=4;
    	}
    	else if($pg_depth<=40){
    		$pg_depth=2;
    	}
    	else{
    		$pg_depth=3;
    	}
    	
        $cmd =  'cd /www/wwwroot/weixin.haocibuni.cn/public/python/weibopage_spider/sina/spiders;
                 python3 -m scrapy crawl weibo_spider -a weibopageid="' . $weibopage_id . '" -a userid=' . $user_id . ' -a pgdepth=' . $pg_depth . ' -a  date='.$time.' 2>&1 ';
        $do = exec($set_charset . $cmd, $ret, $out);
        
        $ave = 0;
        $up = [];
        $down = [];
        $neutral = [];
        $page_information = Db::name('page_information')
    	->where(['weibopage_id' => $weibopage_id])
    	->find();
        $selectPage = Db::name('page_comment')
        ->where([
            'weibopage_id' => $weibopage_id,
            'date' => $time,
            'user_id' => $user_id,
        ])
        ->select();
        foreach ($selectPage as $item) {
            if ($item['page_value'] >= 2/3) {
                array_push($up, $item);
            }
            else if ($item['page_value'] < 1/3) {
                array_push($down, $item);
            }
            else{
            	array_push($neutral, $item);
            }
            $ave += $item['page_value'];
        }
        $ave = $ave / count($selectPage);

        $record=[
        	'user_id'=>$user_id,
        	'date'=>$time,
        	'ave_value'=> round($ave*100,0),
        	];
        $insertrecord = Db::name('page_record')
        ->insert($record);
        
        $deletekeyword =  Db::name('page_comment')
        ->where([
        	'user_id' => $user_id,
        ])
        ->delete();
        
        $message = [
            'up' => $up,
            'down' => $down,
            'neutral' => $neutral,
            'ave' => round($ave*100,0),
            'up_count' =>count($up),
            'down_count' =>count($down),
            'neutral_count' =>count($neutral),
            'page_information'=>$page_information
        ];
        return GlobalVariable::promptData($message);
        
    }
     public function isbind()
    {
    	$openId = input("get.openId");
    	$weibo_page='';
        $bind_flag=false;
    	$weibo_page = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('weibopage_id');
        if($weibo_page!=null){
        	$bind_flag=true;
        }
        $message = [
            'weibo_page' =>$weibo_page,
            'bind_flag' => $bind_flag,
            
        ];
        return GlobalVariable::promptData($message);

    }
    public function binding()
    {
    	$openId = input("get.openId");
    	$weibopage_id = input("get.weibopage_id");
    	$isbind=false;
    	$ok = Db::name('user')
    	->where([ 'open_id'=> $openId])
    	->update(['weibopage_id' => $weibopage_id]);
    	if($ok!=0){
    		$isbind=true;
    	}
        $message = [
            'isbind' =>$isbind
            
        ];
        return GlobalVariable::promptData($message);

    }
    public function unbinding()
    {
    	$openId = input("get.openId");
    	$isbind=true;
    	$user_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('id');
        $update_pageid = Db::name('user')
        ->where(['open_id'=> $openId])
        ->update(['weibopage_id' => null]);
        $delete_page_record = Db::name('page_record')
        ->where(['user_id' => $user_id])
        ->delete();
        if($update_pageid!=0){
        	$isbind = false;
        }
        $message = [
            'isbind' =>$isbind
        ];
        return GlobalVariable::promptData($message);

    }
    public function kwrecord()
    {
    	$openId = input("get.openId");
    	$user_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('id');
    	
        $record = Db::name('keyword_record')
        ->where([
            'user_id' => $user_id,
        ])
        ->order("date DESC")
        ->select();
        $message = [
            'record' =>$record,
        ];
        return GlobalVariable::promptData($message);

    }
    public function pgrecord()
    {
    	$openId = input("get.openId");
    	$user_id = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('id');
        $record = Db::name('page_record')
        ->where([
            'user_id' => $user_id,
        ])
        ->order("date DESC")
        ->select();
        $message = [
            'record' =>$record,
        ];
        return GlobalVariable::promptData($message);

    }
    public function getSetup()
    {
    	$openId = input("get.openId");
    	$kw_depth = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('kw_depth');
        $pg_depth = Db::name('user')
    	->where(['open_id' => $openId])
    	->value('pg_depth');
        $message = [
        	'kwDepth' =>$kw_depth,
            'pgDepth' =>$pg_depth
        ];
        return GlobalVariable::promptData($message);

    }
    public function setSetup()
    {
    	$openId = input("get.openId");
    	$kw_depth =input("get.kwDepth");
        $pg_depth = input("get.pgDepth");
        $success=false;
    	$is_update = Db::name('user')
        ->where(['open_id'=> $openId])
        ->update(['kw_depth' => $kw_depth,
                  'pg_depth' =>$pg_depth]);
        if($is_update>=0){
        	$success=true;
        }
        $message = [
        	'success' =>$success
        ];
        return GlobalVariable::promptData($message);

    }
    
    
    
   
}
