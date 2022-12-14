<?php
// 全局设置
class CommonAction extends ACommonAction{   
    /**    +----------------------------------------------------------    * 默认操作    +----------------------------------------------------------    */
    
    public function member()    {	
        $utype = C('XMEMBER_TYPE');	
        $area=get_Area_list();	
        $uid=intval($_GET['id']);
        $vo=M('members m')->field("m.user_email,m.customer_name,m.user_phone,m.id,m.credits,m.is_ban,m.user_type,m.user_name")->where("m.id={$uid}")->find();	
        $vb = M('member_money')->where("uid=".$uid)->find();	
        if($vb){
            $vo=array_merge($vo,$vb);	
        }
        $vf = M('member_banks')->where("uid=".$uid)->order("is_default DESC")->find();
        if($vf){		
            $vo=array_merge($vo,$vf);	
        }
        $vs = M('member_info')->where("uid=".$uid)->find();
        if($vs){
            $vo=array_merge($vo,$vs);
        }
        //print_r($vo);		//echo "<br>";
        $vo['province'] = $area[$vo['province']];	
        $vo['city'] = $area[$vo['city']];	
        $vo['area'] = $area[$vo['area']];
        $vo['province_now'] = $area[$vo['province_now']];	
        $vo['city_now'] = $area[$vo['city_now']];	
        $vo['area_now'] = $area[$vo['area_now']];	
        $vo['is_ban'] = ($vo['is_ban']==0)?"未冻结":"<span style='color:red'>已冻结</span>";
        $vo['user_type'] = $utype[$vo['user_type']];	
        $vo['money_all'] = $vo['account_money'] + $vo['back_money'] + $vo['money_freeze'] + $vo['money_collect'];	
        //exit;		//print_r(getUserWC($uid));		//exit;		
        $this->assign("capitalinfo",getMemberBorrowScan($uid));	
        $this->assign("wc",getUserWC($uid));	
        $this->assign("vo",$vo);
        $this->assign("user",$vo['user_name']);   
        $this->display();  
    }
    
} 
?>