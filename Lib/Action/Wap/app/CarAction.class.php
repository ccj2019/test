<?php
class CarAction extends HCommonAction {
    public function index(){
    	$nowUrl = U("car/index").'?1=1';
    	$map = array(
            'parent_id'=>534,
            'is_hiden'=>0
            );
    	$carCate = M('article_category')->where($map)->order("sort_order desc")->select(); 
    	$tpl_var['carCate'] = $carCate;     	
    	$typeIdArr = array();
    	foreach ($carCate as $key => $value) {
    		$typeIdArr[] =  $value['id'];
    	}
    	
    	if (!empty($_GET['selling_price'])) {
    		$selling_price = $_GET['selling_price'];
    		switch ($selling_price) {
    			case '1':
    				$searchMap['selling_price'] = array('between','0,10');
    				break;
    			case '2':
    				$searchMap['selling_price'] = array('between','10,20');
    				break;
    				case '3':
    				$searchMap['selling_price'] = array('between','20,30');
    				break;
    				case '4':
    				$searchMap['selling_price'] = array('between','30,40');
    				break;
    				case '5':
    				$searchMap['selling_price'] = array('between','40,50');
    				break;
    				case '6':
    				$searchMap['selling_price'] = array('gt','60');
    				break;
    			default:
    				# code...
    				break;
    		}            
            $nowUrl .= '&selling_price=' . $_GET['selling_price'];
            $this->assign("selling_price", $_GET['selling_price']);
        } else {
            $this->assign("selling_price", 0);
        }
        if (intval($_GET['type_id'])) {
            $searchMap['type_id'] = intval($_GET['type_id']);
            $nowUrl .= '&type_id=' . intval($_GET['type_id']);
            $this->assign("type_id", intval($_GET['type_id']));
        } else {
	    	$type_id = implode($typeIdArr, ',');    	
        	$searchMap['type_id'] = array('in',$type_id) ;
            $this->assign("type_id", 0);
        }
        //分页处理
		import("ORG.Util.Page");
		$count = M('article')->where($searchMap)->count('id');
        // dump($searchMap);die;
		$p = new Page($count,20);
		$page = $p->show();
		$Lsql = "{$p->firstRow},{$p->listRows}";
		//分页处理
		$pre = c("DB_PREFIX");		
		$list = M('article')->where($searchMap)->limit($Lsql)->order('id DESC')->select();		        
		$tpl_var['list'] = $list;
		$tpl_var['page'] = $page;	
		$tpl_var['nowUrl'] = $nowUrl;	
        $this->assign($tpl_var);
		$this->display();
    }
}
