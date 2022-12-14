<?php
// 本类由系统自动生成，仅供测试用途
class PaynewsAction extends HCommonAction {
    public function dogmzf(){
//order_id 订单号
//union_id 平台联合id
//total_fee 交易金额（单位：元）
//pay_time 交易时间
//payment 第三方支付方式 wechat（微信）
//trade_way 交易方式 pay（支付）、refund（退款）
        $data=$_REQUEST;

       $jsons['data']=$data;
//        $jsons['status'] = '0';
//        $jsons['msg'] = '订单已经充值过了！';
//        outJson($jsons);
//        exit;

        $mmap['unionid']=$data['union_id'];
        $minfo = M('members')->where($mmap)->find();
       // $jsons['sql']=M('members')->where($mmap)->getlastsql();
        if(!$minfo){
            $jsons['status'] = '0';
            $jsons['msg'] = '未查到关联用户！';
            outJson($jsons);
        }
        $map['trade_no']=$data['order_id'];
        $vo = M('member_payonline')->where($map)->find();
        if($vo){
            $jsons['status'] = '0';
            $jsons['msg'] = '订单已经充值过了！';
            outJson($jsons);
        }

        $newid = memberMoneyLog($minfo['id'],321,$data['total_fee'],'商城购买转入鱼币');
        if($newid){
            //分销返佣
            // distribution_maid($id);
            $save['money']=$data['total_fee'];
            $save['deal_user'] = '超级管理员';
            $save['deal_uid'] = 0;
            $save['deal_info'] = '支付宝在线充值';
            $save['status'] = 1;
            $save['trade_no']=$data['order_id'];
            $res=M('member_payonline')->add($save);
            //writeLog(M('member_payonline')->getLastSql());
            addInnerMsg($vo['uid'],'成功充值'.$vo['money'].'元','您刚刚购买商品成功充值了'.$vo['money'].'元！');
           // notice1(5, $vo['uid'], $data = array("MONEY"=>$zv));
            if($res){
                $jsons['status'] = '1';
                $jsons['msg'] = '订单已经充值成功！';
            }else{
                $jsons['status'] = '0';
                $jsons['msg'] = '订单已经充值失败！';
            }
        }else {
            $jsons['status'] = '0';
            $jsons['msg'] = '订单数据更新失败！';
        }
        outJson($jsons);
    }

}
