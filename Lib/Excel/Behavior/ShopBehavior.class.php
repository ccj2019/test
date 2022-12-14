<?php
    /**
    * 用户同步
    */
   class ShopBehavior
   {
       public static function sysAdmin($uid)
       {
            $db = M()->db();
            $pre_shop = C('DB_PREFIX_SHOP');
            $uinfo = M('ausers')->find($uid);
            $data ['username'] = $uinfo['user_name'];
            $m = $db->table($pre_shop.'admin');            
            $isExist = $m->where($data)->count('id');            
            $data['password'] = md5($uinfo['user_pass']); 
            $data['regtime'] = time();
            $data['privilege'] = '0';  
            $data['role_id'] = 1;         
            
            $data['privilege'] = $db->table($pre_shop.'admin_role')->where('id='.$data['role_id'])->getField('privilege');
            $data['checkadmin'] = 'true';                                 
            $m = $db->table($pre_shop.'admin'); 
            if($isExist) $m->where(array('username'=>$data['username']))->save(array('password'=>$data['password']));
            else $m->add($data);            
            $admin = $db->table($pre_shop.'admin t1')->field('t1.*,t2.privilege as r_privilege,t2.sign')->where(array('username'=>$data['username']))->join($pre_shop.'admin_role t2 on t1.role_id = t2.id')->find();                        
            session('adminid',$admin['id']);
            session('shop_admin',$admin['username']);
            session('adminpwd',md5($admin['password']));
            session('lastlogintime',$admin['logintime']);
            session('lastloginip',$admin['loginip']);
            session('r_sign',$admin['sign']);
            session('logintime',time());
            if($admin['privilege']!="all")//提取当前用户权限
            {
              $admin_pri=$admin['role_id']!=0?$admin['r_privilege']:$admin['privilege'];
            }else{
              $admin_pri='all';
            }
            session('admin_pri',$admin_pri);            
       } 
       public static function sysUser($uid)
       {
            $db = M()->db();
            $pre_shop = C('DB_PREFIX_SHOP');
            $uinfo = M('members')->find($uid);              
            $data ['username'] = $uinfo['user_name'];
            $m = $db->table($pre_shop.'member');
            $isExist = $m->where($data)->count('id');                        
            $data ['password'] = md5($uinfo['user_pass']);
            $data ['regtime'] = $uinfo['reg_time'];
            $data ['email'] = $uinfo['user_email'];  
            $m = $db->table($pre_shop.'lc_member');
            if($isExist) $m->where(array('username'=>$data['username']))->save(array('password'=>$data['password']));
            else $m->add($data);    
//            echo $m->getlastsql();exit;
       }
      public static function getGoodsType($c_id=false,$num=false)
      {
        $db = M()->db();
        $pre_shop = C('DB_PREFIX_SHOP');
        $where="";
        $c_id and  $where.=" AND parentid = '{$c_id}'";        
        $rs = $db->table($pre_shop.'goodscategory')->where("`checkinfo`='true' {$where}")->order('orderid')->select();         
        return $rs;
      }
      public static function getGoodsList($cid,$limit = null){
        $db = M()->db();
        $pre_shop = C('DB_PREFIX_SHOP');
        $map = array();    
        $where = "t1.checkinfo='true' and t1.delstate = '' and t3.a_id like '%,3,%'";
        $cid and $where .= " and (t1.`classid` = {$cid} or t1.`parentstr` like '%{$cid}%')";
        $rs = $db->table($pre_shop.'goods t1')->field('distinct t1.id,t1.classid,t1.title,t1.picurl,t2.price')->join($pre_shop.'goodsattr t2 on t1.id=t2.goodsid')->join($pre_shop.'goodsattribute t3 on t1.id=t3.g_id')->where($where)->limit($limit)->select();
        return $rs;
      }
   }
