<?php
// 本类由系统自动生成，仅供测试用途
class GuaranteeAction extends HCommonAction {
 	public function index() {
 		// 担保公司 
 		$guartype = M('guartype')->select();
 		foreach ($guartype as $key => $value) {
 			$field= 'g.*,t.title as type_name';
			$guartype[$key]['list'] = M('Guarantee g')->field($field)->join("lzh_guartype t on t.id=g.type_id")->where("type_id = {$value['id']}")->order('id desc')->select();	
 		}		 		
		$this->assign('guartype',$guartype);	
		$glo = array('web_title'=>'合作机构');
    	$this->assign($glo);
		$this->display();
    }	

    public function detail(){
		$pre = C('DB_PREFIX');
		$id = intval($_GET['id']);		
		$Guarantee = M("Guarantee")->field(true)->find($id);		
		if(!is_array($Guarantee)) $this->error("数据有误");
		$this->assign("guarantee",$Guarantee);
        $_list = m("guarantee")->field("id,db_pics")->where($id)->find();
        $list = $_list;		 
		$this->assign('list2',$list2);
		$glo = array('web_title'=> $Guarantee['title'].' - 合作机构');
    	$this->assign($glo);
		$this->display();
    }
}