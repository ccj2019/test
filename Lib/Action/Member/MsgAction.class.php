<?php

// 本类由系统自动生成，仅供测试用途

class MsgAction extends MCommonAction {



    public function index(){
		$map['uid'] = $this->uid;

		//分页处理

		if(isset($_GET['status']) && !empty($_GET['status'])&& ($_GET['status']==1 )){
			$map['status']=$_GET['status'];
			
		}else if(isset($_GET['status']) && !empty($_GET['status'])&& ($_GET['status']==2 )){
				$map['status']=0;
		}
		import("ORG.Util.Page");

		$count = M('inner_msg')->where($map)->count('id');

		$p = new Page($count, 15);

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理
		$this->assign("status",$_GET['status']);
		$list = M('inner_msg')->where($map)->order('id DESC')->limit($Lsql)->select();	
		$read=M("inner_msg")->where("uid={$this->uid}")->count('id');

		$this->assign("list",$list);

		$this->assign("pagebar",$page);

		$this->assign("read",$read);

		$this->assign("unread",$count-$read);

		$this->assign("count",$count);

		$this->display();

    }



    public function sysmsg(){

		$map['uid'] = $this->uid;

		//分页处理

		import("ORG.Util.Page");

		$count = M('inner_msg')->where($map)->count('id');

		$p = new Page($count, 15);

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$list = M('inner_msg')->where($map)->order('id DESC')->limit($Lsql)->select();	

		$read=M("inner_msg")->where("uid={$this->uid} AND status=1")->count('id');

		$this->assign("list",$list);

		$this->assign("pagebar",$page);

		$this->assign("read",$read);

		$this->assign("unread",$count-$read);

		$this->assign("count",$count);

		$this->display();

    }
    public function projectmsg(){

		$map['uid'] = $this->uid;

		//分页处理

		import("ORG.Util.Page");

		$count = M('dynamic')->where($map)->count('id');

		$p = new Page($count, 15);

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理

		$list = M('dynamic')->where($map)->order('id DESC')->limit($Lsql)->select();

		$this->assign("list",$list);

		$this->assign("pagebar",$page);

		$this->assign("count",$count);

		$this->display();

    }

	

	public function viewmsg(){

		$id = intval($_GET['id']);

		$vo = M("inner_msg")->field('msg')->where("id={$id} AND uid={$this->uid}")->find();

		if(!is_array($vo)){

			$this->assign("msg","数据有误");

			$data['content'] = $this->fetch();
				$this->display();	
			//exit(json_encode($data));

		}

		M("inner_msg")->where("id={$id} AND uid={$this->uid}")->setField("status",1);

		$this->assign("mid",$id);

		$this->assign("msg",$vo['msg']);

		//$data['content'] = $this->fetch();

		$this->display();

	}

	

	public function delmsg(){

		$id = text($_GET['idarr']);

		$wsql = "uid={$this->uid}";

		$up = M("inner_msg")->where("{$wsql} AND id in({$id})")->delete();
		if($up){

			$data['status'] = 1;

			$data['data'] = $id;
			$this->success("删除成功");
			exit();
		}else{

			$data['status'] = 0;

			$this->success("删除失败");
			exit();

		}

	}
	public function promsg(){

		$id = text($_GET['idarr']);

		$wsql = "uid={$this->uid}";

		$up = M("dynamic")->where("{$wsql} AND id in({$id})")->delete();
		if($up){

			$data['status'] = 1;

			$data['data'] = $id;
			$this->success("删除成功");
			exit();
		}else{

			$data['status'] = 0;

			$this->success("删除失败");
			exit();

		}

	}
	


}