<?php

// 全局设置

class ZhongzhiAction extends ACommonAction

{

    /**

    +----------------------------------------------------------

    * 默认操作

    +----------------------------------------------------------

    */
    public function index()

    {

		$map=array();

 		

		//分页处理

		import("ORG.Util.Page");

		$count = M('zhongzhi')->where($map)->count();

		$p = new Page($count, C('ADMIN_PAGE_SIZE'));

		$page = $p->show();

		$Lsql = "{$p->firstRow},{$p->listRows}";

		//分页处理


		
		$list = M('zhongzhi')->where($map)->limit($Lsql)->order("id DESC")->select();

        $this->assign("list", $list);

        $this->assign("pagebar", $page);


        $this->display();

    }


    public function add(){
		
		$this->display();
	}

	public function _doAddFilter($m){
        $m->images = preg_replace( "/<p[^>]*>/i", "", $m->images );
        $m->images = preg_replace( "/<\\/p[^<]*>/i", "", $m->images );
		$m->time=time();
		return $m;

	}

    public function edit()
    {
        $id = intval( $_GET['id'] );
        $vo =M( "zhongzhi" )->find( $id );
     
       // $vo["images"]=unserialize($vo["images"]);

        $this->assign( "vo", $vo );
        //var_dump($vo);exit();
      
        $this->display();
        
    }

    public function _doEditFilter($m)
    {   
        session('listaction',"index");
        if(isset($_POST['swfimglist'])&&!empty($_POST['swfimglist'])){
            $m->images = implode(",", $_POST["swfimglist"]);

        }
        return $m;
    }
    protected function _AfterDoEdit(){

        switch(strtolower(session('listaction'))){
            case "waitverify":
            break;

        }

    }


    public function swfUpload( )
    {
        if ( $_POST['picpath'] )
        {
            $imgpath = substr( $_POST['picpath'], 1 );
            if ( in_array( $imgpath, $_SESSION['imgfiles'] ) )
            {
                unlink( C( "WEB_ROOT" ).$imgpath );
                $thumb = get_thumb_pic( $imgpath );
                $res = unlink(C("WEB_ROOT").$thumb );
                if ( $res )
                {
                    $this->success("删除成功", "", $_POST['oid'] );
                }
                else
                {
                    $this->error( "删除失败", "", $_POST['oid'] );
                }
            }
            else
            {
                $this->error( "图片不存在", "", $_POST['oid'] );
            }
        }
        else
        {
            //$_REQUEST["PHPSESSID"]=
            $this->savePathNew = C( "ADMIN_UPLOAD_DIR" )."Ad/";
            $this->thumbMaxWidth = "100";
            $this->thumbMaxHeight = "100";
            $this->saveRule = date( "YmdHis", time()).rand(0,1000);
            $info = $this->CUpload();
            $data['product_thumb'] = $info[0]['savepath'].$info[0]['savename'];
            if ( !isset( $_SESSION['count_file'] ) )
            {
                $_SESSION['count_file'] = 1;
            }
            else
            {
                ++$_SESSION['count_file'];
            }
            $_SESSION['imgfiles'][$_SESSION['count_file']] = $data['product_thumb'];
            echo "{$_SESSION['count_file']}:".__ROOT__."/".$data['product_thumb'];
        }
    }

    public function gglist()
    {
        $id = intval( $_GET['id'] );
        $this->assign("zid",$id);
        $map=array();
        $map["zid"]=$id;
        //分页处理

        import("ORG.Util.Page");

        $count = M('zhongzhi_xx')->where($map)->count();

        $p = new Page($count, C('ADMIN_PAGE_SIZE'));

        $page = $p->show();

        $Lsql = "{$p->firstRow},{$p->listRows}";

        //分页处理


        
        $list = M('zhongzhi_xx')->where($map)->limit($Lsql)->order("id DESC")->select();

        $this->assign("list", $list);

        $this->assign("pagebar", $page);


        $this->display();

    }
    public function addgg(){
        $vo["zid"] = intval($_GET['id'] );
        $this->assign("vo",$vo);
        $this->display("ggfrom");
    }
    public function editgg(){
        $id = intval($_GET['id'] );
        $vo =M( "zhongzhi_xx" )->find( $id );
        $this->assign("vo",$vo);
        $this->display("ggfrom");
    }

    public function doeditgg(){
        $this->db=M("zhongzhi_xx");
        if(isset($_POST['id'])&&!empty($_POST['id'])){
            $res = $this->updateinfo($this->db);
        }else{
            $res = $this->addinfo($this->db);
        }
        //var_dump($this->db);exit;
        if($res){
          

            $this->assign('jumpUrl', __URL__."/gglist?id=".$_POST["zid"]);
            $this->success(L('修改成功'));

          
        }else{
            $this->error('操作失败');
        }

    }



}