<?php 
class LotteryAction extends ACommonAction {
	public function index(){

		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		$count = M('lottery')->count();
		$p = new Page($count, 10);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		$list_lottery = M('lottery t1')->field('t1.*,t2.user_name')->join($pre.'members t2 on t1.uid=t2.id')->order('id desc')->limit($Lsql)->select();

		$this->assign('list_lottery',$list_lottery);
		$this->assign("pagebar", $page);		

		$this->assign("query", http_build_query($search));
		$this->assign('InvestmentRanking',$list);

		$this->display();
	}

	public function edit(){
		$lid = isset($_REQUEST['lid']) ? $_REQUEST['lid'] : 0;
		if(isset($_POST['status'])){
			$rs = M('lottery')->save(array('status'=>$_REQUEST['status'],'id'=>$lid));
			if($rs){
				
				$this->assign('jumpUrl', "__URL__/index");
				$this->success('修改成功！');	
				// $this->redirect("mobi");
			}else{
				$this->success('修改失败！');
			}
		}
		$pre = C('DB_PREFIX');
		$lottery = M('lottery t1')->field('t1.*,t2.user_name')->join($pre.'members t2 on t1.uid=t2.id')->where('t1.id='.$lid)->find();
		$this->assign('lottery',$lottery);
		$this->display();
	}
	public function setting(){
		$m = M('lottery_config');
		if(isset($_POST['set'])){
			$_POST['set']['prize'] = serialize($_POST['set']['prize']);
			$_POST['set']['modify_time'] = time();
			$rs = $m->add($_POST['set'],array(),true);			
			if($rs) $this->success('修改成功！');
			else $this->error('修改失败！');
			die;
		}
		$nowLottery = $m->order('id desc')->find();
		$nowLottery['prize'] = unserialize($nowLottery['prize']);
		$this->assign('set',$nowLottery);
		$this->display();
	}


}
?>