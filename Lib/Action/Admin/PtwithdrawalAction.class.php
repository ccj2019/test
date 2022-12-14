<?php

class PtwithdrawalAction extends ACommonAction
{
    public function index()
    {
        $map = array();

        if(!empty($_REQUEST['user_name'])){
            $map['m.user_name'] = $_REQUEST['user_name'];
        }

        if(!empty($_REQUEST['real_name'])){
            $map['i.real_name'] = $_REQUEST['real_name'];
        }

        if(!empty($_REQUEST['real_name'])){
            $map['i.real_name'] = $_REQUEST['real_name'];
        }

        if(!empty($_REQUEST['real_name'])){
            $bj = $_REQUEST['bj']?$_REQUEST['bj']:'eq';
            $map['w.money'] = array($bj,$_REQUEST['money']);
        }

        if(!empty($_REQUEST['add_time'])){
            $map['w.add_time'] = array('gt',$_REQUEST['add_time']);
        }

        if(!empty($_REQUEST['end_time'])){
            $map['w.end_time'] = array('lt',$_REQUEST['end_time']);
        }

//        if(!empty($_REQUEST['status'])){
            $map['w.status'] = $_REQUEST['status'];
//        }

        if(!empty($_REQUEST['audit'])){
            $map['w.audit'] = $_REQUEST['audit'];
        }
//var_dump($map);exit();
        $all_money =  M('pt_withdrawal w')->where($map)->order('id desc')->sum('money');

        import("ORG.Util.Page");

        $count = M('pt_withdrawal')->where($map)->order('id desc')->count();

        $p = new Page($count, C('ADMIN_PAGE_SIZE'));

        $page = $p->show();

        $field = 'w.status,w.id,w.uid,m.user_name,i.real_name,w.money,w.sxf,w.add_time,w.end_time,w.audit_time,w.content';

        $Lsql = "{$p->firstRow},{$p->listRows}";

        $list = M('pt_withdrawal w')
            ->where($map)
            ->field($field)
            ->join('lzh_members m ON w.uid=m.id')
            ->join('lzh_member_info i ON m.id =i.uid')
            ->order('w.id desc')
            ->limit($Lsql)
            ->select();

        $this->assign('list',$list);
        $this->assign("pagebar", $page);
        $this->assign('all_money',$all_money);
        $this->display();
    }

    public function edit()
    {
        $id = $_REQUEST['id'];

        $field = 'w.id,w.status,w.money,w.sxf,w.content,m.user_name,i.real_name,b.bank_name,b.bank_num';
        $list = M('pt_withdrawal w')->where(array('w.id'=>$id))->field($field)
            ->join('lzh_members m ON w.uid = m.id')
            ->join('lzh_member_banks b ON w.uid = b.uid')
            ->join('lzh_member_info i ON m.id =i.uid')
            ->find();
        //var_dump($list);
        $this->assign('vo',$list);
        $this->display();
    }

    public function eidtupdate()
    {
        $id = $_REQUEST['id'];
        $sxf = abs($_REQUEST['sxf']);
        $status = $_REQUEST['status'];
        $content = $_REQUEST['content'];

        $info = M('pt_withdrawal')->where(array('id'=>$id,'status'=>0))->find();

        if($sxf>$info['money']){
            $this->error('手续费错误，请改正后重试',U('Admin/Ptwithdrawal/edit',array('id'=>$id)));exit();
        }
//        $money = $info['money'] - $sxf;
//var_dump($id,$sxf,$status,$content);exit();
        $data  = M('pt_withdrawal')->where(array('id'=>$id,'status'=>0))->save(array(
            'sxf'=>$sxf,
            'status'=>$status,
            'content'=>$content
        ));

        if($status=='2'){
            $dec =  memberMoneyLog($uid, 303, $money, $info = "申请提现驳回", $target_uid = 0, $target_uname = "",$shiwu=true,$yubi=0);
        }

        if($data!==false){
            $this->success('修改成功',U('Admin/Ptwithdrawal/index'));
        }else{
            $this->error('修改失败',U('Admin/Ptwithdrawal/edit',array('id'=>$id)));
        }

    }
}