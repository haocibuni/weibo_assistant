<?php

/**
 *
 */

namespace app\common\controller;

use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Controller;
use think\Db;
use think\Exception;
use think\Session;


class Index extends Controller
{
    /**
     * 获得指定日期所在的周开始时间与结束时间
     * @param string $gdate
     * @param int $weekStart
     * @return array
     */
    public function getAWeekTimeSlot($gdate = '', $weekStart = 1)
    {
        if (!$gdate) {
            $gdate = date("Y-m-d");
        }
        $w = date("w", strtotime($gdate)); //取得一周的第几天,星期天开始0-6
        $dn = $w ? $w - $weekStart : 6; //要减去的天数
        $st = date("Y-m-d", strtotime("$gdate  - " . $dn . "  days "));
        $en = date("Y-m-d", strtotime("$st  + 6  days "));
        return array($st, $en); //返回开始和结束日期
    }

    /**
     * 获得当天开始时间戳与结束时间戳
     * @return array
     */
    public function getTodayTimeSlot()
    {
        $begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        return array($begin, $end); //返回开始和结束日期
    }


    public static function doExportExcel($data_list)
    {
        /*导入phpExcel核心类 */
        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel.php';
        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel/Writer/Excel5.php';     // 用于其他低版本xls
        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php'; // 用于 excel-2007 格式
        error_reporting(E_ALL);
        date_default_timezone_set('PRC');
        $objPHPExcel = new \PHPExcel();


        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("administrator")
            ->setLastModifiedBy("administrator")
            ->setTitle("数据EXCEL导出")
            ->setSubject("数据EXCEL导出")
            ->setDescription("excel")
            ->setKeywords("excel")
            ->setCategory("result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('仿宋');
        $names = ['A', 'B', 'C', 'D'];
        foreach ($names as $value) {
            // 设置垂直居中
            $objPHPExcel->getActiveSheet()->getStyle($value)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "用户昵称")
            ->setCellValue('B1', "手机号")
            ->setCellValue('C1', "视频答题正确数")
            ->setCellValue('D1', "考试答题正确数");
//            ->setCellValue('E1',"性别")
//            ->setCellValue('F1',"专业")
//            ->setCellValue('G1',"做题个数")
//            ->setCellValue('H1',"成绩");
        //设置宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(19);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
//        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);


        $num = 0;
        foreach ($data_list as $v) {
            if ($num < 2) {
                $num++;
            }
            $objPHPExcel->setActiveSheetIndex(0)
                //Excel的第A列，出数组的键值，下面以此类推
                ->setCellValue('A' . ($num += 1), $v['nick_name'])
                ->setCellValue('B' . ($num), $v['telphone'])
                ->setCellValue('C' . ($num), ' ' . $v['video_count'])
                ->setCellValue('D' . ($num), ' ' . $v['exam_count']);
//                ->setCellValue('E'.($num), $v['exam_count'])
//                ->setCellValue('F'.($num), $v['major'])
//                ->setCellValue('G'.($num), $v['doneCount'])
//                ->setCellValue('H'.($num), $v['grade']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('User');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . time() . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public static function upload($user_id)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');//500KB
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            // 移动到服务器的上传目录 并且设置不覆盖
            $info = $file->validate(['size' => 1024000 * 5, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads', true, false);
            if ($info) {
                // 成功上传后 获取上传信息
                $file_path = $info->getSaveName();
//                str_replace('\\', '/', $file_path);
                $strLen = strlen($file_path);
                for ($i = 0; $i < $strLen; $i++) {
                    if ($file_path[$i] == '\\') {
                        $file_path[$i] = '/';
                    }
                }
//                var_dump($info->getSize());
                $data = [
                    'user_id' => $user_id,
                    'file_size' => $info->getSize(),
                    'create_time' => time(),
                    'filename' => $info->getInfo()['name'],
                    'file_path' => "/uploads/" . $file_path,
//                    $info->getSaveName()
                    'file_md5' => $info->hash('md5'),
                    'file_sha1' => $info->hash('sha1'),
                    'suffix' => $info->getExtension()
                ];
                Db::name('user_asset')->insert($data);
                return $data;
            } else {
                // 上传失败获取错误信息
                return GlobalVariable::promptErrorByJSON("错误", 500, "上传文件过大");
            }
        }
        return GlobalVariable::promptErrorByJSON("错误", 404, "无文件");
    }

    public static function uploadSign($user_id)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');//500KB
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            // 移动到服务器的上传目录 并且设置不覆盖
            $info = $file->validate(['size' => 1024000 * 5, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploadsSign', true, false);
            if ($info) {
                // 成功上传后 获取上传信息
                $file_path = $info->getSaveName();
//                str_replace('\\', '/', $file_path);
                $strLen = strlen($file_path);
                for ($i = 0; $i < $strLen; $i++) {
                    if ($file_path[$i] == '\\') {
                        $file_path[$i] = '/';
                    }
                }
//                var_dump($info->getSize());
                $data = [
                    'user_id' => $user_id,
                    'file_size' => $info->getSize(),
                    'create_time' => time(),
                    'filename' => $info->getInfo()['name'],
                    'file_path' => "/uploadsSign/" . $file_path,
//                    $info->getSaveName()
                    'file_md5' => $info->hash('md5'),
                    'file_sha1' => $info->hash('sha1'),
                    'suffix' => $info->getExtension()
                ];
                Db::name('user_asset')->insert($data);
                return $data;
            } else {
                // 上传失败获取错误信息
                return GlobalVariable::promptErrorByJSON("错误", 500, "上传文件过大");
            }
        }
        return GlobalVariable::promptErrorByJSON("错误", 404, "无文件");
    }

    public static function uploadVideo()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('video');//500KB
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            // 移动到服务器的上传目录 并且设置不覆盖
            $info = $file->validate(['size' => 1024 * 1024 * 500, 'ext' => 'mp4,avi'])->move(ROOT_PATH . 'public' . DS . 'videos', true, false);
            if ($info) {
                // 成功上传后 获取上传信息
                $file_path = $info->getSaveName();
                str_replace("\\", "/", $file_path);
                $data = [
                    'user_id' => Session::get('id', 'xgzx_user'),
                    'file_size' => $info->getSize(),
                    'create_time' => time(),
                    'filename' => $info->getInfo()['name'],
                    'file_path' => "/videos/" . $info->getSaveName(),
                    'file_md5' => $info->hash('md5'),
                    'file_sha1' => $info->hash('sha1'),
                    'suffix' => $info->getExtension()
                ];
                Db::name('user_asset')->insert($data);
                return $data;
            } else {
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return GlobalVariable::promptErrorByJSON("错误", 404, "无文件");
    }

    public static function doUploadExcel($user_id)
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('excel');//500KB
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            // 移动到服务器的上传目录 并且设置不覆盖
            $info = $file->validate(['size' => 1024 * 1024, 'ext' => 'xls,xlsx'])->move(ROOT_PATH . 'public' . DS . 'excel', true, false);
            if ($info) {
                // 成功上传后 获取上传信息
                $file_path = $info->getSaveName();
//                str_replace("\\", "/", $file_path);
                $strLen = strlen($file_path);
                for ($i = 0; $i < $strLen; $i++) {
                    if ($file_path[$i] == '\\') {
                        $file_path[$i] = '/';
                    }
                }
                $data = [
                    'user_id' => $user_id,
                    'file_size' => $info->getSize(),
                    'create_time' => time(),
                    'filename' => $info->getInfo()['name'],
                    'file_path' => "/excel/" . $file_path,
//                    $info->getSaveName()
                    'file_md5' => $info->hash('md5'),
                    'file_sha1' => $info->hash('sha1'),
                    'suffix' => $info->getExtension()
                ];
                Db::name('user_asset')->insert($data);
                return $data;
            } else {
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
        return GlobalVariable::promptErrorByJSON("错误", 404, "无文件");
    }

    public static function import_excel($id)
    {
        require '/www/wwwroot/sign.ujnxgzx.com/vendor/autoload.php';
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        var_dump($_FILES);
//        var_dump($_FILES);
        var_dump($_FILES['excel']['tmp_name']);
//        var_dump('http://sign.ujnxgzx.com' . $file);
        try {
//            var_dump('http://sign.ujnxgzx.com' . $file);
            $spreadsheet = $reader->load($_FILES['excel']['tmp_name']);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die($e->getMessage());
        }
        $sheet = $spreadsheet->getActiveSheet();
        $res = array();
        $i = 0;
        foreach ($sheet->getRowIterator() as $row) {
            $tmp = array();
            foreach ($row->getCellIterator() as $cell) {
                $tmp[] = $cell->getFormattedValue();
            }
            $res[$row->getRowIndex() - 1] = $tmp;
        }
//        echo json_encode($res);
        for ($i = 1; $i < count($res); $i++) {
            $insert = Db::name('user_sign_list_member')->insert([
                'list_id' => $id,
                'signer_name' => $res[$i][0],
                'signer_phone' => $res[$i][1],
                'open_time' => strtotime($res[$i][2]),
                'end_time' => strtotime($res[$i][3])
            ]);
            if ($insert) {
                return GlobalVariable::promptErrorByJSON("成功", 200, "成功");
            } else {
                return GlobalVariable::promptErrorByJSON("错误", 500, "数据插入错误");
            }
        }
    }

    public static function doReadExcel($sign_id, $id, $inputFileName)
    {
        /*导入phpExcel核心类 */
//        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel.php';
//        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel/Writer/Excel5.php';     // 用于其他低版本xls
//        require_once VENDOR_PATH . 'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php'; // 用于 excel-2007 格式

        require '/www/wwwroot/sign.ujnxgzx.com/vendor/PHPExcel/Classes/PHPExcel.php';
        require '/www/wwwroot/sign.ujnxgzx.com/vendor/PHPExcel/Classes/PHPExcel/Writer/Excel5.php';
        require '/www/wwwroot/sign.ujnxgzx.com/vendor/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';
        error_reporting(E_ALL);
        date_default_timezone_set('PRC');
        date_default_timezone_set('PRC');
// 读取excel文件
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            return GlobalVariable::promptErrorByJSON("错误", 500, '加载文件发生错误："' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        // 确定要读取的sheet，什么是sheet，看excel的右下角，真的不懂去百度吧
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        // 获取一行的数据
        $data = [];
        for ($row = 1; $row <= $highestRow; $row++) {
            // Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            //这里得到的rowData都是一行的数据，得到数据后自行处理，我们这里只打出来看看效果1
            array_push($data, $rowData);
        }
        $find = Db::name('user_create_sign')->where('id', $sign_id)->find();
        $is_circle = $find['is_circled'];
        $d = 25569;
        $t = 24 * 60 * 60;
        if ($is_circle == 0) {      //若是不是循环签到
            for ($i = 1; $i < count($data); $i++) {
                if ($i > 1000) {
                    return GlobalVariable::promptErrorByJSON("错误", 500, "数据量过大");
                }
                $data[$i][0][2] = ($data[$i][0][2] - $d) * $t;
                $data[$i][0][3] = ($data[$i][0][3] - $d) * $t;
                $user = Db::name('user')->where([
                    'phone_number' => $data[$i][0][1]
                ])->find();
                if ($user == null) {
                    Db::name('user_sign_list')->where(['sign_id' => $sign_id])->update(['excel_url' => null, 'is_excel' => 0]);
                    if ($data[$i][0][1] == "18811111111") {
                        return GlobalVariable::promptErrorByJSON("错误", 500, "温馨提示，您未删除样例数据");
                    }
                    return GlobalVariable::promptErrorByJSON("错误", 500, "手机号为" . $data[$i][0][1] . "的用户未完善小程序信息，录入停止");
                }
                $userSign = Db::name('user_sign_list_member')->where([
                    'list_id' => $id,       //签到名单id
                    'signer_id' => $user['id'],       //签到者id
                    'sign_id' => $sign_id,
                ])->find();
                if ($userSign) {
                    Db::name('user_sign_list')->where(['sign_id' => $sign_id])->update(['excel_url' => null, 'is_excel' => 0]);
                    return GlobalVariable::promptErrorByJSON("错误", 500, "手机号为" . $data[$i][0][1] . "的用户录入重复，录入停止");
                }
                $insert = Db::name('user_sign_list_member')->insert([
                    'list_id' => $id,       //签到名单id
                    'signer_id' => $user['id'],       //签到者id
                    'sign_id' => $sign_id,
                    'open_time' => $data[$i][0][2],         //签到开始时间
                    'end_time' => $data[$i][0][3]           //签到截止时间
                ]);
                if (!$insert) {
                    return GlobalVariable::promptErrorByJSON("错误", 500, "数据插入错误");
                }
            }

            $listMember = Db::name('user_sign_list_member')->where('list_id', $id)->select();

            for ($i = 0; $i < count($listMember); $i++) {
                $user = Db::name('user')->where('id', $listMember[$i]['signer_id'])->find();
                $listMember[$i]['name'] = $user['nick_name'];
                $listMember[$i]['phone'] = $user['phone_number'];
            }

            return GlobalVariable::promptData($listMember);

        } elseif ($is_circle == 1) {        //若是循环签到
            $now = time();
            if ($now < $find['begin_time']) {
                $gdate = date("Y-m-d H:i:s", $find['begin_time']);
            } else {
                $gdate = date("Y-m-d H:i:s", $now);
            }
            $w = date("w", strtotime($gdate)); //取得一周的第几天,星期天开始0-6
            $dn = $w ? $w - 1 : 6; //要减去的天数
            $st = date("Y-m-d", strtotime("$gdate  - " . $dn . "  days "));
            $en = date("Y-m-d", strtotime("$st  + 6  days "));
            for ($i = 1; $i < count($data); $i++) {
                if ($i > 1000) {
                    return GlobalVariable::promptErrorByJSON("错误", 500, "数据量过大");
                }
                if ($data[$i][0][2] == "周一") {              //我敢保证这绝对是我写的最蠢的逻辑
                    $day = 1;
                } elseif ($data[$i][0][2] == "周二") {
                    $day = 2;
                } elseif ($data[$i][0][2] == "周三") {
                    $day = 3;
                } elseif ($data[$i][0][2] == "周四") {
                    $day = 4;
                } elseif ($data[$i][0][2] == "周五") {
                    $day = 5;
                } elseif ($data[$i][0][2] == "周六") {
                    $day = 6;
                } elseif ($data[$i][0][2] == "周日") {
                    $day = 7;
                } elseif ($data[$i][0][2] == "周天") {
                    $day = 7;
                } else {
                    return GlobalVariable::promptErrorByJSON("错误", 500, "请按照规范正确填写周内时间");
                }

                $time = strtotime($st) + 86400 * ($day - 1);    //一周开始的时间戳加上一天的时间戳乘周几

//                var_dump($data[$i][0][3]);

                $data[$i][0][3] = $data[$i][0][3] * $t;
                $data[$i][0][4] = $data[$i][0][4] * $t;

//                var_dump($data[$i][0][3]);
//
//                /**
//                 * 计算开始时间的时间戳
//                 */
//                $stringBegin = explode(":", $data[$i][0][3]);
//                $timeBegin = $stringBegin[0] * 3200 + $stringBegin[1] * 60 + $stringBegin[2];
//                /**
//                 * 开始时间戳计算结束
//                 */
//
//                /**
//                 * 计算结束时间的时间戳
//                 */
//                $stringEnd = explode(":", $data[$i][0][4]);
//                $timeEnd = $stringEnd[0] * 3200 + $stringEnd[1] * 60 + $stringEnd[2];
//                /**
//                 * 结束时间戳计算结束
//                 */

                $signBegin = $time + $data[$i][0][3];
                $signEnd = $time + $data[$i][0][4];
                $user = Db::name('user')->where([
                    'phone_number' => $data[$i][0][1]
                ])->find();
                if ($user == null) {
                    Db::name('user_sign_list')->where(['sign_id' => $sign_id])->update(['excel_url' => null, 'is_excel' => 0]);
                    if ($data[$i][0][1] == "18811111111") {
                        return GlobalVariable::promptErrorByJSON("错误", 500, "温馨提示，您未删除样例数据");
                    }
                    return GlobalVariable::promptErrorByJSON("错误", 500, "手机号为" . $data[$i][0][1] . "的用户未完善小程序信息，录入停止");
                }
                $insert = Db::name('user_sign_list_member')->insert([
                    'list_id' => $id,       //签到名单id
                    'signer_id' => $user['id'],       //签到者id
                    'sign_id' => $sign_id,
                    'open_time' => $signBegin,         //签到开始时间
                    'end_time' => $signEnd           //签到截止时间
                ]);
                if (!$insert) {
                    return GlobalVariable::promptErrorByJSON("错误", 500, "数据插入错误");
                }
            }

            $listMember = Db::name('user_sign_list_member')->where('list_id', $id)->select();
            for ($i = 0; $i < count($listMember); $i++) {
                $user = Db::name('user')->where('id', $listMember[$i]['signer_id'])->find();
                $listMember[$i]['name'] = $user['nick_name'];
                $listMember[$i]['phone'] = $user['phone_number'];
            }

            return GlobalVariable::promptData($listMember);
//            return GlobalVariable::promptErrorByJSON("错误", 404, "尚未开发!");
        }
    }

    /**
     * 创建指定长度的随机字符串
     *
     * @param int $length
     * @return int|mixed
     */
    private function _generateInvitationCode($length = 6)
    {
        $chars = '123456789abcdefghijklmnpqrstuvwxyz';
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }
}