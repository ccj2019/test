<?php


class ExcelAction extends Action
{

    /**
     * Excel 导出
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function export()
    {
        $exts='xls';

        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new PHPExcel(); // 实例一个excel核心类

//        if($exts=='xls'){
//            Vendor('PHPExcel.PHPExcel.Reader.Excel5');
//            $objWriter = new PHPExcel_Reader_Excel5($objPHPExcel); // 将excel类对象作为参数传入进去
//        }else if($exts=='xlsx'){
//            Vendor('PHPExcel.PHPExcel.Reader.Excel2007');
//            $objWriter = new PHPExcel_Reader_Excel2007($objPHPExcel); // 将excel类对象作为参数传入进去
//        }

        $sheets=$objPHPExcel->getActiveSheet()->setTitle('pt_order');//设置表格名称
        //设置sheet列头信息
        $objPHPExcel->setActiveSheetIndex()
            ->setCellValue('A1', 'id')//id
            ->setCellValue('B1', 'uid')//用户id
            ->setCellValue('C1', 'gid')//商品id
            ->setCellValue('D1', 'hbid')//红包id
            ->setCellValue('E1', 'status')//状态
            ->setCellValue('F1', 'num')//数量
            ->setCellValue('G1', 'real_name')//真实姓名
            ->setCellValue('H1', 'address')//地址
            ->setCellValue('I1', 'money')//金额
            ->setCellValue('J1', 'add_time')//添加时间
            ->setCellValue('K1', 'add_ip')//ip
            ->setCellValue('L1', 'email')//email
            ->setCellValue('M1', 'beizhu')//备注
            ->setCellValue('N1', 'cell_phone')//手机号码
            ->setCellValue('O1', 'yijian')//意见
            ->setCellValue('P1', 'liuyan')//留言
            ->setCellValue('Q1', 'ordernums')//单号
            ->setCellValue('R1', 'type')//购买类型
            ->setCellValue('S1', 'pay_way')//支付类型
            ->setCellValue('T1', 'wuliu')//物流
            ->setCellValue('U1', 'pay_time')//支付时间
            ->setCellValue('V1', 'fh_time')//返还时间
            ->setCellValue('W1', 'wlid')//物流id
            ->setCellValue('X1', 'tid')//团id
            ->setCellValue('Y1', 'image')//image
            ->setCellValue('Z1', 'title');//title
        //这里是操作数据库查询库中的字段的值(如果数量庞大可以利用框架的批量读取功能)
        $users=M("pt_order")->limit(10)->Select();
        $i=2;//第一行被表头占有了
        foreach($users as $v){
            //设置单元格的值
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$v['id']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$v['uid']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$v['gid']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$v['hbid']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$v['status']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$v['num']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$v['real_name']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$v['address']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$v['money']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$v['add_time']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$v['add_ip']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$v['email']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$v['beizhu']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$v['cell_phone']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$v['yijian']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$v['liuyan']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$v['ordernums']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$v['type']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$v['pay_way']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$v['wuliu']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$v['pay_time']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('V'.$i,$v['fh_time']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('W'.$i,$v['wlid']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('X'.$i,$v['tid']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,$v['image']);
            $sheets=$objPHPExcel->getActiveSheet()->setCellValue('Z'.$i,$v['title']);

            $i++;
        }

        //整体设置字体和字体大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName( 'Arial');//整体设置字体
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);//整体设置字体大小

//        $objPHPExcel->getActiveSheet()->setAutoSize(true); //单元格宽度自适应


//         $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true); //单元格宽度自适应
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20); //设置列宽度
//        $objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true); //设置单元格字体加粗

        // 输出Excel表格到浏览器下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="拼团订单.xls"'); //excel表格名称
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');

    }




    /**
     * Excel 导入
     * @param $info
     * @return bool
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function importExcel($info)
    {
        Vendor('PHPExcel.PHPExcel.IOFactory');

        if($info){
            $excel_path = $info[0]['savename'];//$info->getSaveName();  //获取上传文件名
            $file_name = './Public/excel/'.$excel_path;//ROOT_PATH . 'public' . DS . 'excel' . DS . $excel_path;   //上传文件的地址
            $obj_PHPExcel = \PHPExcel_IOFactory::load($file_name);  //加载文件内容

            $excel_array=$obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
//            array_shift($excel_array);  //删除第一个数组(标题);
            $arr  = reset($excel_array); //获取字段名
            unset($excel_array[0]); //删除字段名，剩下的都是存储到数据库的数据了！！
            $data = [];
            for($i = 0;$i < count($excel_array);$i++){
                foreach ($arr as $key => $value){
                    $resdata[$value] = $data[$i][$value] = $excel_array[$i+1][$key];//使数组的键值就是数据表的字段名
                }
                $res = M('pt_order')->save($resdata);
            }

            return $res;
        }
    }

    /**
     * 上传文件导入excel
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function uploade(){
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();

        $upload->saveRule = 'time';//图片命名规则
        $upload->maxSize  = 1024000 ;// 设置附件上传大小
        $upload->allowExts  = array('xls','xlsx');// 设置附件上传类型
        $info = $upload->uploadOne($_FILES['file'],'./Public/excel/');
        $res = $this->importExcel($info);

        var_dump($res);
    }


    /**
     * 导出.csv文件
     */
    public function exportCSVAction()
    {
//        $id = intval($this->_req->getParam('cid',0));
//        $reg = new RegistrationModel();
//        $studentsArr = $reg->getStudents($id);   // 获取课程及报名学员信息
        $studentsArr = M('pt_order')->select();

        $fileName = 'test.csv';//$studentsArr['info']['title'];  //这里定义表名。简单点的就直接  $fileName = time();

        header('Content-Type: application/vnd.ms-excel');   //header设置
        header("Content-Disposition: attachment;filename=".$fileName.".csv");
        header('Cache-Control: max-age=0');

        $fp = fopen('php://output','a');    //打开php文件句柄，php://output表示直接输出到PHP缓存,a表示将输出的内容追加到文件末尾

        $head = array('姓名','地址','单号','手机号');  //表头信息
        foreach($head as $k=>$v){
            $head[$k] = iconv("UTF-8","GBK//IGNORE",$v);    //将utf-8编码转为gbk。理由是： Excel 以 ANSI 格式打开，不会做编码识别。如果直接用 Excel 打开 UTF-8 编码的 CSV 文件会导致汉字部分出现乱码。
        }
        fputcsv($fp,$head);  //fputcsv() 函数将行格式$head化为 CSV 并写入一个打开的文件$fp。

        //   if (!empty($studentsArr['students'])) {
        $data = [];  //要导出的数据的顺序与表头一致；提前将最后的值准备好（比如：时间戳转为日期等）
        foreach ($studentsArr as $key => $val) {
            $data['name'] = $val['real_name'];
            $data['address'] = $val['address'];
            $data['ordernum'] = " ".$val['ordernums'];
            $data['cell_phone'] = " ".$val['cell_phone'];

            foreach($data as $i=> $item){  //$item为一维数组哦
                $data[$i] = iconv("UTF-8","GBK//IGNORE",$item);  //转为gbk的时候可能会遇到特殊字符‘-’之类的会报错，加 ignore表示这个特殊字符直接忽略不做转换。
            }
            fputcsv($fp,$data);
        }
        exit;  //记得加这个，不然会跳转到某个页面。
        //   }

    }



    public function kefu()
    {
        vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new PHPExcel(); // 实例一个excel核心类

        $sheets = $objPHPExcel->getActiveSheet()->setTitle('pt_order');//设置表格名称
        $objPHPExcel->setActiveSheetIndex()
            ->setCellValue('A1', '项目名称')//账号
            ->setCellValue('B1', '账号')//账号
            ->setCellValue('C1', '真实姓名')//真实姓名
            ->setCellValue('D1', '投标总额')//投标总额
            ->setCellValue('E1', '投标时间')//投标时间
            ->setCellValue('F1', '使用优惠券情况')//使用优惠券情况
            ->setCellValue('G1', '鱼币')//投标方式
            ->setCellValue('H1', '余额')//投标方式
        ;

        $id = $_REQUEST['id'];

        $name = M('kefu')->where(array('id'=>$id))->getField('name');

        $kefu = M('kefu_gx')->where(array('kfid'=>$id))->select();

        $map = array_column($kefu,'memid');

        $i = 2;

        foreach ($map as $item){

            $borrow = M('borrow_investor')->where(array('investor_uid' => $item))->select();

            foreach ($borrow as $k => $v){

                $borrow_name = M('borrow_info')->where(array('id'=>$v['borrow_id']))->getField('borrow_name');
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $borrow_name);

                $find = M('member_info')->where(array('uid' => $v['investor_uid']))->find();
                $accont = M('members')->where(array('id' => $v['investor_uid']))->find();

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $accont['user_name']);
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $find['real_name']);

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $v['investor_capital']);
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, date("Y-m-d H:i:s",$v['add_time']));

                $bonus_id = 0;
                if ($v['bonus_id']==0) {
                    $sheets = $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, "$bonus_id");
                } else {
                    $bonus_id = M('member_bonus')->where(array('id'=>$v['bonus_id']))->getField('money_bonus');
                    $sheets = $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, "$bonus_id");
                }

                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $v['yubi'].'鱼币');
                $sheets = $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $v['investor_capital'] - ($v['yubi'] + $bonus_id).'余额');

                $i++;
            }

        }



        //整体设置字体和字体大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');//整体设置字体
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);//整体设置字体大小

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20); //设置列宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20); //设置列宽度


        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.'客服'.$name.'.xls'); //excel表格名称
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $objWriter->save('php://output');
    }

}
