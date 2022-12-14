<?php 

class ZhuanpanAction extends ACommonAction {

	public function index(){



		$pre = C('DB_PREFIX');

		import("ORG.Util.Page");

		$count = M('zhuanpan')->count();

		$p = new Page($count, 10);

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		$list_win = M('zhuanpan t1')->field('t1.*')->order('id desc')->limit($Lsql)->select();



		$this->assign('list_win',$list_win);

		$this->assign("pagebar", $page);		





		$this->display();

	}



	public function add(){

		if(isset($_POST['prize']) && $_POST['jp_type'] != 0){
			$data['title'] = $_POST['title'];
			$data['prize'] = $_POST['prize'];
			$data['jp_type'] = $_POST['jp_type'];
			$data['rate'] = $_POST['rate'];
			$data['add_time'] = time();
			$rs = M('zhuanpan')->add($data);
			if($rs){
				$this->assign('jumpUrl', "__URL__/index");
				$this->success('增加成功！');	
			}else{
				$this->success('增加失败！');
			}
		}
		$this->display();

	}
	public function edit(){
		$lid = isset($_REQUEST['lid']) ? $_REQUEST['lid'] : 0;
		if(isset($_POST['prize']) && $_POST['jp_type'] != 0){
			$data['title'] = $_POST['title'];
			$data['prize'] = $_POST['prize'];
			$data['jp_type'] = $_POST['jp_type'];
			$data['rate'] = $_POST['rate'];
			$data['add_time'] = time();
			$rs = M('zhuanpan')->where('id='.$lid)->save($data);
			if($rs){
				// $this->assign('jumpUrl', "__URL__/index");
				$this->success('修改成功！', "__URL__/index");	
			}else{
				$this->success('增加失败！');
			}
		}

		$pre = C('DB_PREFIX');
		$zhuanpan = M('zhuanpan t1')->field('t1.*')->where('t1.id='.$lid)->find();
		$this->assign('zhuanpan',$zhuanpan);
		$this->display();
	}

	public function winlog(){
		//奖品列表
		$pre = C('DB_PREFIX');

		import("ORG.Util.Page");

		$count = M('member_win')->count();

		$p = new Page($count, 10);

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		
		$list_win = M('member_win t1')->field('t1.*,t2.user_name')->join($pre.'members t2 on t1.uid=t2.id')->order('id desc')->limit($Lsql)->select();
		$this->assign('list_win',$list_win);

		$this->assign("pagebar", $page);

		$this->display();

	}
	public function dodel(){
		$lid = isset($_REQUEST['lid']) ? $_REQUEST['lid'] : 0;
		$delnum = M('zhuanpan')->where("id in ({$lid})")->delete(); 
		if($delnum){


			$this->success("奖项删除成功", "__URL__/index");

		}else{

			$this->success("奖项删除失败");

		}
	}





}

?>