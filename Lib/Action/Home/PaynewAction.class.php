<?php
// 本类由系统自动生成，仅供测试用途
class PaynewAction extends HCommonAction {

    //微信异步返回
    public function wnotify(){
        vendor('Weixin.notify');
        $notify = new PayNotifyCallBack();
        $data = file_get_contents("php://input");
        $notify->NotifyProcess($data);
    }
    //支付宝异步返回
    public function alinotify(){
        //var_dump("adf");exit();
    	vendor('Alipay.notify');
    	$notify = new notify();
        $notify->do_order($_POST);
    }

    //支付宝异步返回
    public function ygalinotify(){
//        writeLog("-----------------============--------------------");
//        writeLog('a4');
        $_POST["fund_bill_list"] = str_replace('&quot;','"',$_POST["fund_bill_list"]);
        //writeLog($_POST);
        vendor('AppPay.pay');
        $wappay = new pay();
        $wappay->huuidiao($_POST);
    }

    public function zfbyg($ordernum,$trade_no){
//        writeLog('a5');
//        writeLog($ordernum);
        $order_type=substr($ordernum,0,3);
        if($order_type=='ZFB'){
            //支付宝充值
            $this->dochongzhi($ordernum,$trade_no);
        }else{
            //预购项目付款
            $this->doyugou($ordernum,$trade_no);
        }
    }
    public function doyugou($ordernum,$trade_no){
        $map['ordernum']=$ordernum;
        $map['status']='0';
        M()->startTrans();
        $res=M('ys_order')->where($map)->find();
        if(!$res){
            return false;
        }
        $ginfo=M('ys_good')->where('id='.$res['ysid'])->find();

        $user_name=M("members")->where('id='.$res['buid'])->getField('user_name');
        //给发起者添加余额
        $res2=memberMoneyLog($ginfo['uid'], 316, $res['money'] , "售出（{$ginfo['title']}）,". "{$res['fenshu']}份",$res['buid'],$user_name, false);

        //给个人增加代收金额
        $res1=memberMoneyLog($res['buid'], 317, $res['sfmoney'] , "购买（{$ginfo['title']}），支付宝付款,". "{$res['fenshu']}份",$ginfo['uid'],'项目方东盛澜', false);


        if(!empty($res['bonus_id'])){
            //使用红包部分
            $bmoney=M('member_bonus')->where('id='.$res['bonus_id'])->getField('money_bonus');

            $resb = memberMoneyLog($res['buid'], 318, $bmoney, "购买（{$ginfo['title']}）,优惠券",$res['yuid'], "项目方东盛澜", false);
            if($resb){
                $rs = M('member_bonus')->save(array('id' => $res['bonus_id'], 'status' => 2, 'use_time' => time()));
                if (!$rs) {
                    M()->rollback();
                    $jsons['msg'] = '红包使用出现问题！';
                    $jsons['status'] = '0';
                    //  outJson($jsons);
                }
            }
        }

        if(!$res2){
            M()->rollback();
            $jsons['msg'] = '购买错误！';
            $jsons['status'] = '0';
            //outJson($jsons);
        }else{
            //添加购买分数
            $zdata['fenshu']=$ginfo['fenshu']+$res['fenshu'];
            $zdata['money']=$ginfo['money']+$res['money'];
            $res3=m('ys_good')->where(array("id"=>$res['ysid']))->save($zdata);
            if(!$res3){
                M()->rollback();
                $jsons['msg'] = '购买出错了！';
                $jsons['status'] = '0';
                //outJson($jsons);
            }else{
                $zmap['borrow_id']=$res['ysid'];
                $zmap['uid']=$res['buid'];
                $zmap['type']='2';
                $yzp=M("member_zengpin")->where($zmap)->find();

                $datas['status']='1';
                M('ys_order')->where("id=".$res['id'])->save($datas);

                $ymap['ysid']=$res['ysid'];
                $ymap['buid']=$res['buid'];
                $ymap['status']='1';

                $ytmoney=M('ys_order')->where($ymap)->sum('money');
                $zpfs=floor($ytmoney/$ginfo["manzeng"]);
                $res4=$res5=true;
                $huozeng=0;
                if(!empty($yzp)){
                    if($zpfs!=$yzp['allnum']){
                        $xnum=$zpfs-($yzp['allnum']-$yzp['num']);
                        $res4=ygxzengpin($yzp["id"],$res['ysid'],$zpfs,$xnum);
                        $huozeng=$zpfs-$yzp['allnum'];
                    }
                }else{
                    if($zpfs>0){
                        $res4=yffzengpin($res['buid'],$res['id'],$zpfs,$ginfo['zpid'],$ginfo['id']);
                        $huozeng=$zpfs;
                    }
                }
                if($ginfo['contract']==1){
                    $condata['itemid']=$res['ysid'];
                    $condata['type']=2;
                    $condata['conid']=$res['id'];
                    $condata['add_time']=time();
                    $condata['uid']=$res['buid'];
                    $res5=M("contract")->add($condata);
                }

                if($res4&&$res5){
                    if($huozeng>0){
                        $datasd['zpnum']=$huozeng;
                    }
//                    $datas['status']='1';
                    $datasd['trade_no']=$trade_no;

                    $endtime=$ginfo['zhouqi']*86400+time();
                    $end_time= strtotime(date('Y-m-d',$endtime))+32000+rand(400,1000);
                    $datasd['add_time']=time();
                    $datasd['end_time']=$end_time;

                    M('ys_order')->where("id=".$res['id'])->save($datasd);
                    M()->commit();
                    $jsons['msg'] = "购买成功！";
                    $jsons['status'] = '1';
                }else{
                    M()->rollback();
                    $jsons['msg'] = "赠品更新失败！";
                    $jsons['status'] = '0';
                }
            }
        }
        //writeLog($jsons);
        if($jsons['status']==0){
            return false;
        }else{
            return true;
        }
    }
    public function dochongzhi($ordernum,$trade_no){
        $map['nid']=$ordernum;
        $map['status']=0;
        $vo = M('member_payonline')->where($map)->field('id,money,fee,uid,way')->find();
        $vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
        if(!$vo){
            return false;
        }
//        if($vo['status']==0){
            $newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],'支付宝在线充值');
            writeLog('AA1');
            if($newid){
                //分销返佣
                // distribution_maid($id);
                $zv=$vo['money']-$vo['fee'];
                $save['deal_user'] = '超级管理员';
                $save['deal_uid'] = 0;
                $save['deal_info'] = '支付宝在线充值';
                $save['status'] = 1;
                M('member_payonline')->where("id=".$vo['id'])->save($save);
                //writeLog(M('member_payonline')->getLastSql());
                addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
                notice1(5, $vo['uid'], $data = array("MONEY"=>$zv));
                return true;
            }else {
                return false;
            }
//        }else{
//            return false;
//        }
    }

    public function dogmzf(){
//order_id 订单号
//union_id 平台联合id
//total_fee 交易金额（单位：元）
//pay_time 交易时间
//payment 第三方支付方式 wechat（微信）
//trade_way 交易方式 pay（支付）、refund（退款）
        $data=$_REQUEST;
        writeLog('-----购物充值-----');
        writeLog($data);
        $mmap['unionid']=$data['union_id'];
        $minfo = M('members')->where($mmap)->find();
        if(!$minfo){
            $jsons['status'] = '0';
            $jsons['msg'] = '未查到关联用户！';
            outJson($jsons);
        }
        $map['trade_no']=$data['order_no'];
        $vo = M('member_payonline')->where($map)->find();
        if($vo){
            $jsons['status'] = '0';
            $jsons['msg'] = '订单已经充值过了！';
            outJson($jsons);
        }

        M()->startTrans();
        //分销返佣
        // distribution_maid($id);
        $save['money']=$data['total_fee'];
        $save['deal_user'] = '超级管理员';
        $save['deal_uid'] = 0;
        $save['deal_info'] = '商城购买转入鱼币';
        $save['status'] = 1;
        $save['trade_no']=$data['order_no'];
        $save['fee'] = 0;
        $save['nid'] = 'online';
        $save['way'] = 'shop';
        $save['uid'] = $minfo['id'];
        $save['add_time'] = time();

        $res=M('member_payonline')->add($save);
        writeLog(M('member_payonline')->getLastSql());
        addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚购买商品成功充值了'.$vo['money'].'元！');
        //memberMoneyLog($uid, $type, $amoney, $info = "", $target_uid = 0, $target_uname = "",$shiwu=true,$yubi=0,$benjin=0)
        $newid = memberMoneyLog($minfo['id'],321,$data['total_fee'],'商城购买转入鱼币',0,'',false);
        writeLog($newid.'--- --购物充值end-----'.$res);
        if($newid&&$res){
            M()->commit();
            // notice1(5, $vo['uid'], $data = array("MONEY"=>$zv));
            if($res){
                $jsons['status'] = '1';
                $jsons['msg'] = '订单已经充值成功！';
            }else{
                $jsons['status'] = '0';
                $jsons['msg'] = '订单已经充值失败！';
            }
        }else {
            M()->rollback();
            $jsons['status'] = '0';
            $jsons['msg'] = '订单数据更新失败！';
        }
        writeLog($jsons);
        writeLog('-----购物充值end-----');
        outJson($jsons);
    }



    public function hfbzfbfh(){
        writeLog($_REQUEST);
        $result = $_GET['result'];
        $pay_message = $_GET['pay_message'];
        $agent_id = $_GET['agent_id'];
        $jnet_bill_no = $_GET['jnet_bill_no'];
        $agent_bill_id = $_GET['agent_bill_id'];
        $pay_type = $_GET['pay_type'];
        $pay_amt = $_GET['pay_amt'];
        $remark = $_GET['remark'];
        $return_sign=$_GET['sign'];
        $remark = iconv("GB2312","UTF-8//IGNORE",urldecode($remark));//签名验证中的中文采用UTF-8编码;
        $signStr='';
        $signStr  = $signStr . 'result=' . $result;
        $signStr  = $signStr . '&agent_id=' . $agent_id;
        $signStr  = $signStr . '&jnet_bill_no=' . $jnet_bill_no;
        $signStr  = $signStr . '&agent_bill_id=' . $agent_bill_id;
        $signStr  = $signStr . '&pay_type=' . $pay_type;
        $signStr  = $signStr . '&pay_amt=' . $pay_amt;
        $signStr  = $signStr .  '&remark=' . $remark;
        $signStr = $signStr . '&key='.C('hfb')['key'];//商户签名密钥
        $sign='';
        $sign=md5($signStr);
        if($sign==$return_sign){   //比较签名密钥结果是否一致，一致则保证了数据的一致性
            echo 'ok';
            //商户自行处理自己的业务逻辑
            $map['nid']=$agent_bill_id;
            $map['status']=0;
            $vo = M('member_payonline')->where($map)->field('id,money,fee,uid,way')->find();
            $vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
            if(!$vo){
                return false;
            }
            $newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],'支付宝在线充值');
            writeLog('AA1');
            if($newid){
                //分销返佣
                // distribution_maid($id);
                $zv=$vo['money']-$vo['fee'];
                $save['deal_user'] = '超级管理员';
                $save['deal_uid'] = 0;
                $save['deal_info'] = '支付宝在线充值';
                $save['status'] = 1;
                M('member_payonline')->where("id=".$vo['id'])->save($save);
                addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
                notice1(5, $vo['uid'], $data = array("MONEY"=>$zv));
                return true;
            }else {
                return false;
            }
        }
        else{
            echo 'error';
            //商户自行处理，可通过查询接口更新订单状态，也可以通过商户后台自行补发通知，或者反馈运营人工补发

        }
    }
    public function hfbwxfh(){
        writeLog($_REQUEST);
        $result = $_GET['result'];
        $pay_message = $_GET['pay_message'];
        $agent_id = $_GET['agent_id'];
        $jnet_bill_no = $_GET['jnet_bill_no'];
        $agent_bill_id = $_GET['agent_bill_id'];
        $pay_type = $_GET['pay_type'];
        $pay_amt = $_GET['pay_amt'];
        $remark = $_GET['remark'];
        $return_sign=$_GET['sign'];
        $remark = iconv("GB2312","UTF-8//IGNORE",urldecode($remark));//签名验证中的中文采用UTF-8编码;
        $signStr='';
        $signStr  = $signStr . 'result=' . $result;
        $signStr  = $signStr . '&agent_id=' . $agent_id;
        $signStr  = $signStr . '&jnet_bill_no=' . $jnet_bill_no;
        $signStr  = $signStr . '&agent_bill_id=' . $agent_bill_id;
        $signStr  = $signStr . '&pay_type=' . $pay_type;
        $signStr  = $signStr . '&pay_amt=' . $pay_amt;
        $signStr  = $signStr .  '&remark=' . $remark;
        $signStr = $signStr . '&key='.C('dslhfb')['key'];//商户签名密钥
        $sign='';
        $sign=md5($signStr);
        if($sign==$return_sign){   //比较签名密钥结果是否一致，一致则保证了数据的一致性
            echo 'ok';
            //商户自行处理自己的业务逻辑
            $map['nid']=$agent_bill_id;
            $map['status']=0;
            $vo = M('member_payonline')->where($map)->field('id,money,fee,uid,way')->find();
            $vx = M('members')->field("user_name,user_phone")->find($vo['uid']);
            if(!$vo){
                return false;
            }
            $newid = memberMoneyLog($vo['uid'],27,$vo['money']-$vo['fee'],'微信在线充值');
            writeLog('AA2');
            if($newid){
                //分销返佣
                // distribution_maid($id);
                $zv=$vo['money']-$vo['fee'];
                $save['deal_user'] = '超级管理员';
                $save['deal_uid'] = 0;
                $save['deal_info'] = '微信在线充值';
                $save['status'] = 1;
                M('member_payonline')->where("id=".$vo['id'])->save($save);
                addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚成功充值了'.$vo['money'].'元！');
                notice1(5, $vo['uid'], $data = array("MONEY"=>$zv));
                return true;
            }else {
                return false;
            }
        }
        else{
            echo 'error';
            //商户自行处理，可通过查询接口更新订单状态，也可以通过商户后台自行补发通知，或者反馈运营人工补发

        }
    }

}
