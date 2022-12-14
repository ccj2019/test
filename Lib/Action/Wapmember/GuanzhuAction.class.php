<?php
// 本类由系统自动生成，仅供测试用途
class GuanzhuAction extends MCommonAction {
	
	 public function index(){
            //分页处理
            import("ORG.Util.Page");
            $count = M('pro_guanzhu') ->count('id');
            $p = new Page($count, 10);
            $page = $p->show();
            $Lsql = "{$p->firstRow},{$p->listRows}";
    	    $list= M('pro_guanzhu')->join("lzh_borrow_info  on lzh_borrow_info.id=lzh_pro_guanzhu.bid ") ->limit($Lsql)->select();
            $this->assign('list', $list);
            $this->assign('pagebar', $page);
            $this->assign('count', $count);
            $this->assign('glomodulename','tendout');
    	 	$this->display();
	 }

	 public function bespeak(){
         $pre = C('DB_PREFIX');
         //分页处理
         import("ORG.Util.Page");
         $where['b.bespeak_uid'] = Array('eq',$this->uid);
         $where['b.bespeak_point'] = Array('neq',1);
         $where['bi.bespeak_able'] = Array('eq',1);
         $count = $list = M('borrow_info bi')
             ->join("{$pre}bespeak b on bi.id = b.borrow_id")
             ->where($where)
             ->order('b.id DESC')
             ->count('b.borrow_id');
         $p = new Page($count, 10);
         $page = $p->show();
         $field = "b.borrow_id,bi.borrow_name,bi.borrow_img,bi.start_time,bi.bespeak_days,b.bespeak_money,b.bespeak_status,b.add_time";
         $Lsql = "{$p->firstRow},{$p->listRows}";
         $list = M('borrow_info bi')
             ->distinct(true)
             ->field($field)
             ->join("{$pre}bespeak b on bi.id = b.borrow_id")
             ->where($where)
             ->order('b.id DESC')
             ->limit($Lsql)
             ->select();
         $this->assign('list', $list);
         $this->assign('pagebar', $page);
         $this->assign('count', $count);
         $this->assign('glomodulename','tendout');
         $this->display();
     }
}
