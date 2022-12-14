<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends ACommonAction {
	var $justlogin = true;
	
    public function index(){
		require(C('APP_ROOT')."Common/acl.inc.php");
		require(C('APP_ROOT')."Common/menu.inc.php");
		header("Content-type:text/html;charset=utf-8");
		//dump($menu_left);
       	$this->assign('menu_left',$menu_left);
		$this->display();
    }
    public function home(){

    	$memberscount = M("members")->count();    	
    	//用户投资总额
		$investor_capital_count = M('borrow_investor')->sum('investor_capital');
		//用户赚取收益
		//$receive_interest_all_count = M('borrow_investor')->sum('receive_interest');		
		//用户奖励
		$moneylog_count = M('member_moneylog')->where(array('type'=>array('in','13,20,21,33,34,35')))->sum('collect_money');
		$receive_interest_all_count += $moneylog_count;
		//已归还用户本金
		$receive_capital_count = M('borrow_investor')->sum('receive_capital');
		//已归还用户利息
		
		$receive_interest_count = M('borrow_investor')->sum('receive_interest');
		$this->assign('investor_capital_count',$investor_capital_count);
		$this->assign('memberscount',$memberscount);
		$this->assign('receive_capital_count',$receive_capital_count);
		$this->assign('receive_interest_count',$receive_interest_count);
		$row['borrow_1'] = M('borrow_info')->where('borrow_status=0')->count('id');//初审
		$row['borrow_2'] = M('borrow_info')->where('borrow_status=4')->count('id');//复审
		$map=array();
		$map['d.status'] = array("neq",0);
		$map['d.repayment_time'] = 0;
		$map['d.substitute_money'] = 0;
		$map['d.deadline'] = array("between","100000,".time());
		$buildSql = M('investor_detail d')->field("d.id")->join("{$this->pre}borrow_info b ON b.id=d.borrow_id")->join("{$this->pre}members m ON m.id=b.borrow_uid")->where($map)->group('d.sort_order,d.borrow_id')->buildSql();
		//echo $buildSql;
		$newsql = M()->query("select count(*) as tc from {$buildSql} as t");
		$row['borrow_3'] = $newsql[0]['tc'];//逾期
		$row['limit_a'] = M('member_apply')->where('apply_status=0')->count('id');//额度
		$row['data_up'] = M('member_data_info')->where('status=0')->count('id');//上传资料
		$row['vip_a'] = M('vip_apply')->where('status=0')->count('id');//VIP审核
		$row['video_a'] = M('video_apply')->where('apply_status=0')->count('id');//视频认证		
		$row['face_a'] = M('face_apply')->where('apply_status=0')->count('id');//现场认证		
		$row['real_a'] = M('members_status')->where('id_status=3')->count('uid');//现场认证
		
		$week_1 = array(strtotime(date("Y-m-d",time())." 00:00:00"),strtotime("+1 day",strtotime(date("Y-m-d",time())." 23:59:59")));//一周内
		$row['jinridaihuan'] = M('borrow_info b')->join("{$this->pre}members m ON m.id=b.borrow_uid")->where("b.borrow_status=6 and FROM_UNIXTIME(b.deadline,'%Y-%m-%d') = curdate()")->count('b.id');
		//echo  M('borrow_info b')->getlastsql();
		//exit;
		$row['Withdrawlogwait'] = M('member_withdraw')->where('withdraw_status=0')->count('id');//待审核提现
		$row['real_Paylog'] = M('member_payonline')->where("tran_id<>'' and status=0 and way<>'off'")->count('id');//充值未成功待审核	
		$row['paylogoffline'] = M('member_payonline')->where("status=0 and way='off'")->count('id');//线下充值	
		$row['paysmf'] = M('member_payonline')->where("status=0 and way='smf'")->count('id');//线下充值	
		
		$this->assign("row",$row);
    	$this->display();	
    }
	public function verify(){
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}
	
    public function login()
    {
    	doTODO();
		require C("APP_ROOT")."Common/menu.inc.php";
		//echo (md5(strtolower("123456")));
		if( session("admin") > 0){
			if( session("admin_mobi")!=1){
				$this->assign('jumpUrl', "__URL__/mobi");
				//$this->success('登陆成功，还没有进行短信验证！');	
				$this->redirect("mobi");
			}
			$this->redirect('index');
			exit;
		}
		if($_POST){
			if($_SESSION['verify'] != md5($_POST['code'])){
				$this->error("验证码错误!");
			}
			$data['user_name'] = text($_POST['admin_name']);
			$data['user_pass'] = md5(strtolower($_POST['admin_pass']));
			$data['is_ban'] = array('neq','1');
			$data['user_word'] = text($_POST['user_word']);
			$admin = M('ausers')->field('id,user_name,u_group_id,real_name,is_kf,area_id,user_word')->where($data)->find();
			
			if(is_array($admin) && count($admin)>0 ){
				// setcookie('PHPSESSID', session_id(),  time() + 60 * 60);
				foreach($admin as $key=>$v){
					session("admin_{$key}",$v);
				}
				if(session("admin_area_id")==0) session("admin_area_id","-1");
				session('admin',$admin['id']);
				session('adminname',$admin['real_name']);
				$this->redirect("mobi");
				$this->assign('jumpUrl', "__URL__/mobi");
				//$this->redirect('mobi');
				//exit;
				//$this->success('登陆成功，现在转向短信验证页面');
				
				//$this->assign('jumpUrl', "__URL__/mobi");
				//$this->success('登陆成功，还没有进行短信验证！');	
				//$this->assign('jumpUrl', "__URL__/index");
				//$this->success('管理员验证成功，进入欢迎界面！');
			}else{
				adminCreditsLog(1,"管理员".$data["user_name"]."登录失败！");
				$this->error('用户名或密码或口令错误，登陆失败');
			}
		}else{
			$this->display();
		}
    }
	public function mobi()
    {
		
		$data['id'] = session("admin");
		$admin = M('ausers')->field('id,user_name,u_group_id,real_name,is_kf,area_id,user_word,phone')->where($data)->find();
		if($_POST){
			//echo $_POST["code"];
			if($_SESSION['admin_code'] != $_POST['code']){
				$this->error("验证码错误，请返回重新输入!");
				//$this->display();
				//exit;
			}else{
				session('admin_mobi',1);
                session('keypp',md5(substr(get_client_ip(),6)));
                doTODO();
//				adminCreditsLog(0,"管理员[N]登录成功！");
				$this->assign('jumpUrl', "__URL__/index");
				$this->success('管理员验证成功，进入欢迎界面！');	
				exit;
			}
		}
		//$data['user_name'] = session("adminname");
		
		//echo md5("123456");
		//print_r($admin);
		//exit;
		//echo $_SESSION['verify'];
		//echo $_SESSION['admin_code'];
		preg_match_all('/\d/S',md5(time()), $matches);
		//print_r($matches);
		$arr = join('',$matches[0]);
		//print_r($arr);
		$code = substr($arr , 0 , 4);		
		$this->assign('code',$code);
		session('admin_code',$code);
		//echo $admin['phone'];
		//exit;
		//exit;
		if($_SESSION['admin_code']!=""){
			$content= $datag['web_name'] . "后台登录验证码为:".$code;
			//sendsms($admin['phone'], $content);
			Notice1(1,$admin['id'],array('phone'=>$admin['phone'],"code"=>$code));
		}
		// echo $content;
		
		$this->assign('admin', $admin);
		$this->display();
    }
	public function send()
    {
		$data['id'] = session("admin");
		$admin = M('ausers')->field('id,user_name,u_group_id,real_name,is_kf,area_id,user_word,phone')->where($data)->find();
		if($_SESSION['admin_code']!=""){
			$content= $datag['web_name'] . "后台登录验证码为:".$_SESSION['admin_code'];
			//sendsms($admin['phone'], $content);
			Notice1(1,$admin['id'],array('phone'=>$admin['phone'],"code"=>$_SESSION['admin_code']));
		}else{
			preg_match_all('/\d/S',md5(time()), $matches);
			//print_r($matches);
			$arr = join('',$matches[0]);
			//print_r($arr);
			$code = substr($arr , 0 , 4);
			//echo $code;
			session('admin_code',$code);
			$content= $datag['web_name'] . "后台登录验证码为:".$code;
			//sendsms($admin['phone'], $content);
			Notice1(1,$admin['id'],array('phone'=>$admin['phone'],"code"=>$code));
		}
		exit;
    }
    public function logout()
    {
		require C("APP_ROOT")."Common/menu.inc.php";
		session(null);
		$this->assign('jumpUrl', '/'.ADMIN_PATH);
		$this->success('注销成功，现在转向首页');
    }
	
}
