<?php
// 本类由系统自动生成，仅供测试用途
class PromotionAction extends MCommonAction {
    public function index(){
    	$invitation = M('members')->field("id,incode")->where("id={$this->uid}")->find();
    	$url = "http://".$_SERVER['HTTP_HOST']."/member/common/register.html?invite=".$invitation['incode'];
    	$this->assign("url",$url);
    	$this->assign("invitation",$invitation);
		$field = " m.user_name,m.reg_time";
		$friendlog = M("members m")->field($field)->where(" m.recommend_id ={$this->uid} or m.recommend_bid ={$this->uid} or m.recommend_cid ={$this->uid}")->group("m.id")->select();
		$this->assign("friendlog",$friendlog);
		$this->display();
    }
    public function promotionlog(){
        $pre = C('DB_PREFIX');
        $field = " l.tid,l.money,l.add_time,l.ordernums,m.user_name,m.reg_time";
        $friendlog = M("member_maidlog l")->field($field)->join($pre.'members m on m.id = l.tid')->where("l.uid ={$this->uid}")->select();
        $this->assign("friendlog",$friendlog);
        $this->display();
    }

}