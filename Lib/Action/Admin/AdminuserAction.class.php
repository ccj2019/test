<?php
// 管理员管理
class AdminuserAction extends ACommonAction
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {
        import("ORG.Util.Page");
		
		$AdminU = M('ausers');
		$page_size = ($page_szie==0)?C('ADMIN_PAGE_SIZE'):$page_szie;
		
		if(session("admin_id")!=150){
			$count  = $AdminU->where("id<>150")->count(); // 查询满足要求的总记录数
		}else{
			$count = $AdminU->count();
		}
		$Page = new Page($count,$page_size); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		$fields = "id,user_name,u_group_id,real_name,is_ban,area_name,is_kf,qq,phone,user_word";
		$order = "id DESC,u_group_id DESC";
		//print_r($_SESSION);
		//exit;
		if(session("admin_id")!=150){
			$list = $AdminU->field($fields)->where("id<>150")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		}else{
			$list = $AdminU->field($fields)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();	
		}
		$AdminUserList = $list;
		
		$GroupArr = get_group_data();
		foreach($AdminUserList as $key => $v){
			$AdminUserList[$key]['groupname'] = $GroupArr[$v['u_group_id']]['groupname'];
		}

		$this->assign('position', '管理员管理');
		$this->assign('pagebar', $show);
		$this->assign('admin_list', $AdminUserList);
		$this->assign('arealist', M("area")->field("id,name")->where("is_open=1")->select());
		$this->assign('group_list', $GroupArr);
        $this->display();
    }

    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function addAdmin()
    {
	
		$data = $_POST;

		if(!isset($_POST['uid'])){//新增
			foreach($data as $key => $v){
				if($key == "user_pass") $data[$key] = md5(strtolower($data['user_pass']));
				else $data[$key] = EnHtml($v);
			}
			$data['area_name'] = M("area")->getFieldById($data['area_id'],'name');
			$newid = M('ausers')->add($data);
			if(!$newid>0){
				$this->error('添加失败，请确认填入数据正确');
				exit;
			}
			adminCreditsLog(2,"管理员[N]添加了管理员！");
			$this->assign('jumpUrl', U('/admin/Adminuser/index'));
			$this->success('管理员添加成功');
		}else{
			$data['id'] = intval($_POST['uid']);
			$data['user_pass'] = trim($data['user_pass']);
			if( empty($data['user_pass']) ) unset($data['user_pass']);
			foreach($data as $key => $v){
				if($key == "user_pass") $data[$key] = md5(strtolower($data['user_pass']));
				else $data[$key] = EnHtml($v);
			}
			
			$data['area_name'] = M("area")->getFieldById($data['area_id'],'name');
			$newid = M('ausers')->save($data);
			if(!$newid>0){
				$this->error('修改失败，数据没有改动或者改动未成功');
				exit;
			}
			adminCreditsLog(4,"管理员[N]修改了管理员账户！");
			$this->assign('jumpUrl', U('/admin/Adminuser/index'));
			$this->success('管理员修改成功');
		}
		
    }



    public function doDelete()
    {
		$id=$_REQUEST['idarr'];
		$delnum = M('ausers')->where("id in ({$id})")->delete(); 

		if($delnum){
			adminCreditsLog(3,"管理员[N]删除了管理员账户！");
			$this->success("管理员删除成功",'',$id);
		}else{
			$this->success("管理员删除失败");
		}
		
    }
	
	public function header(){
		$kfuid = intval($_GET['id']);
		$this->assign("kfuid",$kfuid);
		$this->display();
	}
	
	public function memberheaderuplad(){
		$uid = intval($_GET['uid']) + 10000000;
		if($uid<=10000000) exit;
		else redirect(__ROOT__."/Style/header/upload.php?uid={$uid}");
		exit;
	}

	//管理员登录日志
	public function loginlog(){
		$pre = C('DB_PREFIX');
		import("ORG.Util.Page");
		
		$count  = M('admin_log al')->count(); // 查询满足要求的总记录数   
		$Page = new Page($count,20); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		  
		$list = M('admin_log al')->join("{$pre}ausers a on al.aid=a.id")->field("al.*,a.user_name")->order('al.add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$logtype = array(
			'0' => '登录',
			'1' => '登录失败',
			'2' => '添加',
			'3' => '删除',
			'4' => '修改',
		);
		$this->assign('logtype',$logtype);
		$this->assign('pagebar',$show);
		$this->assign('list',$list);
		$this->display();
	}
	 public function loglist()
    {
        import("ORG.Util.Page");
		
		$do_log = M('do_log');
		$page_size = ($page_szie==0)?C('ADMIN_PAGE_SIZE'):$page_szie;
		
		// if(session("admin_id")!=150){
		// 	$count  = $AdminU->where("id<>150")->count(); // 查询满足要求的总记录数
		// }else{
			$count = $do_log->count();
		// }
		$Page = new Page($count,$page_size); // 实例化分页类传入总记录数和每页显示的记录数   
		$show = $Page->show(); // 分页显示输出
		   
		//$fields = "id,user_name,u_group_id,real_name,is_ban,area_name,is_kf,qq,phone,user_word";
		$order = "time DESC";
		//print_r($_SESSION);
		//exit;
		// if(session("admin_id")!=150){
		// 	$list = $AdminU->field($fields)->where("id<>150")->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
		// }else{
		$list = $do_log->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();	
		// }
		//$AdminUserList = $list;
		
		// $GroupArr = get_group_data();




		$ausers=M("ausers");
		foreach($list as $key => $v){
			$list[$key]['name'] = $ausers->where("id=".$v["uid"])->getField("user_name");
			if($v["title"]=='修改用户信息'){
				
				$list[$key]['message'] = json_decode($v["message"],true);

			}
		}
		$this->assign('position', '日志管理');
		$this->assign('pagebar', $show);
		$this->assign('list', $list);
		$this->assign('arealist', M("area")->field("id,name")->where("is_open=1")->select());
		$this->assign('group_list', $GroupArr);
        $this->display();
    }

}
?>