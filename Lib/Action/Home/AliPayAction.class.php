<?php
 class AliPayAction extends HCommonAction{
    function __construct(){
        vendor('Alipay.Corefunction');
        vendor('Alipay.Md5function');
        vendor('Alipay.Notify');
        vendor('Alipay.Submit'); 
        parent::__construct();
        $this->ord = M('order_xx');
        $this->car=M('car');
        $this->add=M('member_address');
        $this->members=M('members');
    }
    public function doalipay(){
        $alipay_config=C('alipay_config');  
        /**************************请求参数**************************/
        //支付类型 //必填，不能修改
        $payment_type = "1";
        //服务器异步通知页面路径
        $notify_url = C('alipay.notify_url');
        //页面跳转同步通知页面路径 
        $return_url = C('alipay.return_url');
        //卖家支付宝帐户必填 
        $seller_email = C('alipay.seller_email');
        //商户订单号通过支付页面的表单进行传递，注意要唯一！
        $out_trade_no = $_POST['id'];
        $orders=$this->ord->where("ordernums='".$out_trade_no."' ")->find();
        //订单名称 //必填 通过支付页面的表单进行传递
        $subject = "123"; 
        //付款金额  //必填 通过支付页面的表单进行传递 
        $total_fee = $orders['jine']; 
        //订单描述 通过支付页面的表单进行传递  
        $body = ""; 
        //商品展示地址 通过支付页面的表单进行传递 
        $show_url = $_POST['ordshow_url'];  
        //付款金额
        $price =$orders['jine']; 
        //必填
       // var_dump( $_POST['WIDtotal_fee']);exit();
        //商品数量
        $quantity = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = "";
        // var_dump($orders["kdmoney"]);exit();
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "BUYER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述

        //订单描述
        $body = $_POST['WIDbody'];

        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，如：http://www.xxx.com/myorder.html
        $dizhi=$this->add->find($orders["addressid"]);
        //收货人姓名
        $receive_name = $orders["real_name"];
        //如：张三
        //收货人地址
        $receive_address = $orders["address"];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        //收货人邮编
        $receive_zip = "";
        //如：123456
        //收货人电话号码
        $receive_phone = $orders["cell_phone"];
        //如：0571-88158090
        //收货人手机号码
        $receive_mobile =  $orders["cell_phone"];
        //如：13312341234
        // var_dump($_POST);
        // var_dump($orders); exit();
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
                "service" => "create_partner_trade_by_buyer",
                "partner" => trim($alipay_config['partner']),
                "payment_type"  => $payment_type,
                "notify_url"    => $notify_url,
                "return_url"    => $return_url,
                "seller_email"  => $seller_email,
                "out_trade_no"  => $out_trade_no,
                "subject"   => $subject,
                "price" => $price,
                "quantity"  => $quantity,
                "logistics_fee" => $logistics_fee,
                "logistics_type"    => $logistics_type,
                "logistics_payment" => $logistics_payment,
                "body"  => $body,
                "show_url"  => $show_url,
                "receive_name"  => $receive_name,
                "receive_address"   => $receive_address,
                "receive_zip"   => $receive_zip,
                "receive_phone" => $receive_phone,
                "receive_mobile"    => $receive_mobile,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        header('Content-Type:text/html;charset=utf-8');
        echo $html_text;
    }
        /******************************
        服务器异步通知页面方法
        其实这里就是将notify_url.php文件中的代码复制过来进行处理
        
        *******************************/
    function notifyurl(){
                /*
                同理去掉以下两句代码；
                */ 
                //require_once("alipay.config.php");
                //require_once("lib/alipay_notify.class.php");
                
                //这里还是通过C函数来读取配置项，赋值给$alipay_config
        $alipay_config=C('alipay_config');
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
        //验证成功
               //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
           $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
           $trade_no       = $_POST['trade_no'];          //支付宝交易号
           $trade_status   = $_POST['trade_status'];      //交易状态
           $total_fee      = $_POST['total_fee'];         //交易金额
           $notify_id      = $_POST['notify_id'];         //通知校验ID。
           $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
           $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
           $parameter    = array(
             "out_trade_no" => $out_trade_no, //商户订单编号；
             "trade_no"     => $trade_no,     //支付宝交易号；
             "total_fee"    => $total_fee,    //交易金额；
             "trade_status" => $trade_status, //交易状态
             "notify_id"    => $notify_id,    //通知校验ID。
             "notify_time"  => $notify_time,  //通知的发送时间。
             "buyer_email"  => $buyer_email,  //买家支付宝帐号；
           );
        if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
            //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //如果有做过处理，不执行商户的业务程序
            echo "success";     //请不要修改或删除
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
                //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                    $data['createoff']=time();
                    $data['start']=2;
                    $data['buyer']=$buyer_email;
                    $this->ord->where('num='.$out_trade_no)->save($data);
                    //更新购物车状态
                    $ords=$this->ord->where('num='.$out_trade_no)->find();
                    $car1=$this->car->where(" id in(".$ords["carid"].")")->select();
                    foreach ($car1 as $k => $v) {
                        $date["start"]="3";
                        $this->car->where(" id =".$v["id"])->save($date);

                        $chanpin=$this->pro->find($v["productid"]);

                        //添加积分记录
                        $pos = M("points"); 
                        // $point["gettype"]="1";
                        $point["hdfs"]="2";
                        $point["title"]=$chanpin["name"];
                        $point["time"]=time();
                        $point["userid"]=$ords["memid"];
                        $point["objid"]=$v["productid"];
                        //根据订单详细加分
                        $point["count"]=$chanpin["points"]*$v["count"];
                        $pos->add($point);

                        //添加用户积分
                        $this->member->where(" id= ".$ords["memid"])->setInc('points',$point["count"]);

                   }
                   //更新个人信息
                   $mem=$this->member->find($ords["memid"]);
                   $xf=$mem["xiaofei"];
                   $xfs=$mem["xiaofei"]+$ords["money"];
                   $jf="";
                   if($xfs>=3000&&$xfs<8000&&$xf<3000){
                        $jfmap["count"]=1000;
                        $jfmap["title"]="消费总金额达到3000";
                        $jf="1";
                   }
                   if($xfs>=8000&&$xfs<20000&&$xf<8000){
                        $jfmap["count"]=3000;
                        $jfmap["title"]="消费总金额达到8000";
                        $jf="1";
                   }
                   if($xfs>=20000&&$xfs<50000&&$xf<20000){
                        $jfmap["count"]=10000;
                        $jfmap["title"]="消费总金额达到20000";
                        $jf="1";
                   }
                   if($xfs>=50000&&$xfs<100000&&$xf<50000){
                        $jfmap["count"]=50000;
                        $jfmap["title"]="消费总金额达到50000";
                        $jf="1";
                   }
                   if($xfs>=100000&&$xf<100000){
                        $jfmap["count"]=200000;
                        $jfmap["title"]="消费总金额达到100000";
                        $jf="1";
                   }
                   if($jf!=""){
                        //添加积分记录
                        $pos = M("points"); 
                        // $point["gettype"]="1";
                        $jfmap["hdfs"]="2";
                        $jfmap["time"]=time();
                        $jfmap["userid"]=$ords["memid"];
                        $pos->add($jfmap);
                   }
                   $mdate["xiaofei"]=$mem["xiaofei"]+$ords["money"];
                   $this->member->where(" id= ".$mem["id"])->save($mdate);
                   //想用户发送确认信息
                   $title = '金都酒窖订单确认';
                   $msg = '尊敬的用户，您好，您订购的订单，已经付款成功！我们会尽快发货！';
                   $this->send_mail($mem['name'],$mem['name'],$title,$msg);

            echo "success".$_POST['trade_status'];     //请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
            //该判断表示卖家已经发了货，但买家还没有做确认收货的操作
                    $data['fhtime']=time();
                    $data['start']=3;
                    $this->ord->where('num='.$out_trade_no)->save($data);
                    //更新购物车状态
                    $ords=$this->ord->where('num='.$out_trade_no)->find();
                    $car1=$this->car->where(" id in(".$ords["carid"].")")->select();
                    foreach ($car1 as $k => $v) {
                        $date["start"]="4";
                        $this->car->where(" id =".$v["id"])->save($date);
                   }
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            echo "success".$_POST['trade_status'];     //请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        else if($_POST['trade_status'] == 'TRADE_FINISHED') {
            //该判断表示买家已经确认收货，这笔交易完成
                    $data['wctime']=time();
                    $data['start']=4;
                    $this->ord->where('num='.$out_trade_no)->save($data);
                    //更新购物车状态
                    $ords=$this->ord->where('num='.$out_trade_no)->find();
                    $car1=$this->car->where(" id in(".$ords["carid"].")")->select();
                    foreach ($car1 as $k => $v) {
                        $date["start"]="5";
                        $this->car->where(" id =".$v["id"])->save($date);
                   }
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                
            echo "success".$_POST['trade_status'];;     //请不要修改或删除
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        else {
            //其他状态判断
            echo "success".$_POST['trade_status'];
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
        //验证失败
        echo "fail";

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }   
        // if($verify_result) {
        //        //验证成功
        //        //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        //    $out_trade_no   = $_POST['out_trade_no'];      //商户订单号
        //    $trade_no       = $_POST['trade_no'];          //支付宝交易号
        //    $trade_status   = $_POST['trade_status'];      //交易状态
        //    $total_fee      = $_POST['total_fee'];         //交易金额
        //    $notify_id      = $_POST['notify_id'];         //通知校验ID。
        //    $notify_time    = $_POST['notify_time'];       //通知的发送时间。格式为yyyy-MM-dd HH:mm:ss。
        //    $buyer_email    = $_POST['buyer_email'];       //买家支付宝帐号；
        //      $parameter    = array(
        //      "out_trade_no" => $out_trade_no, //商户订单编号；
        //      "trade_no"     => $trade_no,     //支付宝交易号；
        //      "total_fee"    => $total_fee,    //交易金额；
        //      "trade_status" => $trade_status, //交易状态
        //      "notify_id"    => $notify_id,    //通知校验ID。
        //      "notify_time"  => $notify_time,  //通知的发送时间。
        //      "buyer_email"  => $buyer_email,  //买家支付宝帐号；
        //    );
        //    if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
        //             //进行订单处理，并传送从支付宝返回的参数
        //             $data['createoff']=time();
        //             $data['start']=2;
        //             $this->ord->where('num='.$out_trade_no)->save($data);
        //             //更新购物车状态
        //             $ords=$this->ord->where('num='.$out_trade_no)->find();
        //             $car1=$this->car->where(" id in(".$ords["carid"].")")->select();
        //             foreach ($car1 as $k => $v) {
        //                 $date["start"]="3";
        //                 $this->car->where(" id =".$v["id"])->save($date);
        //            }

        //    }else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {                           
        //         if(!checkorderstatus($out_trade_no)){
                  
        //         }
        //     }
        //     echo "success";        //请不要修改或删除
        //  }else {
        //         //验证失败
        //         echo "fail";
        // }    
    }
    /*
        页面跳转处理方法；
        这里其实就是将return_url.php这个文件中的代码复制过来，进行处理； 
        */
    function returnurl(){
                //头部的处理跟上面两个方法一样，这里不罗嗦了！
        $alipay_config=C('alipay_config');
        $alipayNotify = new AlipayNotify($alipay_config);//计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {
            //验证成功
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        $out_trade_no   = $_GET['out_trade_no'];      //商户订单号
        $trade_no       = $_GET['trade_no'];          //支付宝交易号
        $trade_status   = $_GET['trade_status'];      //交易状态
        $total_fee      = $_GET['total_fee'];         //交易金额
        $notify_id      = $_GET['notify_id'];         //通知校验ID。
        $notify_time    = $_GET['notify_time'];       //通知的发送时间。
        $buyer_email    = $_GET['buyer_email'];       //买家支付宝帐号；
            
        $parameter = array(
            "out_trade_no"     => $out_trade_no,      //商户订单编号；
            "trade_no"     => $trade_no,          //支付宝交易号；
            "total_fee"      => $total_fee,         //交易金额；
            "trade_status"     => $trade_status,      //交易状态
            "notify_id"      => $notify_id,         //通知校验ID。
            "notify_time"    => $notify_time,       //通知的发送时间。
            "buyer_email"    => $buyer_email,       //买家支付宝帐号
        );
        //请在这里加上商户的业务逻辑程序代码
        //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
            //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
             $this->redirect(C('alipay.successpage'));//跳转到配置项中配置的支付成功页面；
        }
        else {
          echo "trade_status=".$_GET['trade_status'];
          $this->redirect(C('alipay.errorpage'));//跳转到配置项中配置的支付失败页面；
        }
            
        echo "验证成功<br />";
        echo "trade_no=".$trade_no;

        //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    }
    else {
        //验证失败
        //如要调试，请看alipay_notify.php页面的verifyReturn函数
        echo "验证失败";
    }
        
 // if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
 //        if(!checkorderstatus($out_trade_no)){
 //             orderhandle($parameter);  //进行订单处理，并传送从支付宝返回的参数；

 //                    $data['createoff']=time();
 //                    $data['start']=2;
 //                    $this->ord->where('num='.$out_trade_no)->save($data);
 //                    //更新购物车状态
 //                    $ords=$this->ord->where('num='.$out_trade_no)->find();
 //                    $car1=$this->car->where(" id in(".$ords["carid"].")")->select();
 //                    foreach ($car1 as $k => $v) {
 //                        $date["start"]="3";
 //                        $this->car->where(" id =".$v["id"])->save($date);
 //                   }
 //        }
 //        $this->redirect(C('alipay.successpage'));//跳转到配置项中配置的支付成功页面；
 //    }else {
 //        echo "trade_status=".$_GET['trade_status'];
 //        $this->redirect(C('alipay.errorpage'));//跳转到配置项中配置的支付失败页面；
 //    }
 // }else {
 //    //验证失败
 //    //如要调试，请看alipay_notify.php页面的verifyReturn函数
 //    echo "支付失败！";
 //    }
 // }
    }
}