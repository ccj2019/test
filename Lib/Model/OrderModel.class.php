<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 管理用户模型
class OrderModel extends ACommonModel {
	
	protected $_validate	=	array(

		);

    public function checkNid() {
        if(!empty($_POST['id'])) $map['id']   = array('neq',$_POST['id']);
        $map['parent_id']    = intval($_POST['parent_id']);
        $map['type_nid']    = $_POST['type_nid'];
        if($this->where($map)->find()) {
            return false;
        }
        return true;
    }
}
?>