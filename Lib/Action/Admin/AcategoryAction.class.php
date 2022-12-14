<?php
// 全局设置
class AcategoryAction extends ACommonAction
{
	var $typeleve=1;
	var $typeleve_default=1;
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
    {    	
    	$catid = isset($_GET['catid']) ? $_GET['catid'] : 0;
		$field= true;		
		$where['parent_id'] = $catid;		
		$this->_list(D('Acategory'),$field,$where,'sort_order'); 
		$this->assign('catid',$catid);
        $this->display();
    }
	
    public function _addFilter()
    {
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
    }

	public function _doAddFilter($m){
		$m->parent_id=intval($m->parent_id);
		$m->add_time=time();
		return $m;
	}

	public function _doEditFilter($m){
		$m->parent_id=intval($m->parent_id);
		return $m;
	}

	public function _editFilter($id){
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
	}

	public function addmultiple(){
		$typelist = get_type_leve_list('0','acategory');//分级栏目
		$this->assign('type_list',$typelist);
        $this->display();
	}
	
	public function doAddMul(){
		$mul_type=explode(",",$_POST['type_name']);
		$mul_nid=explode(",",$_POST['type_nid']);
		$Type=D("Acategory");
		foreach($mul_type as $key => $v){
			$data=array();
			$data['type_name'] = $v;
			$data['type_nid'] = $mul_nid[$key];
			$data['parent_id'] = intval($_POST['parent_id']);
			$data['type_set'] = intval($_POST['type_set']);
			$data['is_hiden'] = intval($_POST['is_hiden']);
			$data['type_url'] = text($_POST['type_url']);
			$newid = $Type->add($data);
		}
		
        if($newid){ 
		adminCreditsLog(2,"管理员[N]添加了栏目！");
		$this->success("栏目批量添加成功");}
		else {$this->error("添加失败");}
	}
	
    public function listType()
    {
		$typeid=intval($_REQUEST['typeid']);
		$sonlist = D('Acategory')->field(true)->where("parent_id={$typeid}")->select();
		$sonlist = $this->_listFilter($sonlist);
		$list="";
		foreach($sonlist as $key=>$v){
		$leve = $this->_typeLeve($v['id']);
		$haveson=$v['haveson'];
		$list.='<tr overstyle="on" id="list_'.$v['id'].'" class="leve_'.$leve.'" typeid="'.$v['id'].'" parentid="'.$v['parent_id'].'">
				<td><input type="checkbox" name="checkbox" id="checkbox2" onclick="checkon(this)" value="'.$v['id'].'"></td>
				<td>'.$v['id'].'</td>
				<td>'.($haveson?'<span class="typeson typeon" data="son">&nbsp;</span>':'<span class="typeson">&nbsp;</span>').$v['type_name'].'</td>
				<td>'.$v['type_nid'].'&nbsp;</td>
				<td>'.$v['sort_order'].'</td>
				<td>
					<a href="'.__URL__.'/edit?id='.$v['id'].'">编辑</a> 
					<a href="javascript:void(0);" onclick="del('.$v['id'].');">删除</a>  
				</td>
			  </tr>';
		}
		

		$data['inner'] = $list;
		$data['typeid'] = $typeid;
		$this->ajaxReturn($data,"");
    }
	public function _doDelFilter($id){
		$n = D('Acategory')->where("parent_id in ({$id})")->count();
		if($n==0) $n = D('Acategory')->where("id in ({$id}) AND is_sys=1")->count();
		if($n>0){
			$this->error("删除失败,所删除的栏目包含有子栏目,或者含有系统分类,不能删除");
			exit;
		}
	}
	
	public function _listFilter($list){
		$type_set = C('TYPE_SET');
		$row=array();
		foreach($list as $key=>$v){
			$v['haveson']  = $this->_typeSon($v['id']);
			$v['type_set'] = $type_set[$v['type_set']];
			$row[$key]=$v;
		}
		return $row;
	}
	//获取栏目的级别
	protected function _typeLeve($typeid){
		static $rt=0;//先声明要返回静态变量,不然在下面被赋值时是引用赋值
		$condition['id'] = $typeid;
		$v = D('Acategory')->field('parent_id')->where($condition)->find();
		if($v['parent_id']>0){
			$this->typeleve++;
			$this->_typeLeve($v['parent_id']);
		}else{
			$rt = $this->typeleve;
			$this->typeleve = $this->typeleve_default;
		}
		return $rt;
	}
	//获取栏目的上下级别
	protected function _typeSon($typeid){
		$condition['parent_id'] = $typeid;
		$v = D('Acategory')->field('id')->where($condition)->find();
		if($v['id']>0){
			return true;
		}else{
			return false;
		}
	}
	
}
?>