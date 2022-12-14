<?php
class AvatarAction extends MCommonAction
{
    public $input = array();
    public $config;
    private $root_path  = '';
    private $tmpdir     = 'Style/header/tempUpload'; //临时文件夹（相对于本文件的位置而言）。开头和结尾请不要加反斜杆
    private $avatardir  = 'Style/header/customavatars'; //存储头像的文件夹（相对于本文件的位置而言），开头和结尾请不要加反斜杆
    private $authkey    = 'safsdfsda5643dgsdfgrew'; //通讯密钥，必须填写，否则脚本无法运行！
    private $debug      = false; //开启debug记录？
    private $uploadsize = 1024; //上传图片文件的最大值，单位是KB
    private $uc_api     = ''; //运行该脚本的网址，末尾请不要加反斜杠（比如http://www.aaa.com/uc_avatar_upload）。详情请看说明
    private $web_root   = '';
    private $imgtype    = array(1 => '.gif', 2 => '.jpg', 3 => '.png'); //允许上传的类型，请勿修改此处设置，否则会引起安全隐患问题！
    //存储对象实例
    protected static $_objectInstance = array();
    /**
     * 构造函数。(ok)
     *
     */
    public function _initialize()
    {   
        parent::_initialize();
        $this->web_root  = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . '';
        $this->uc_api    = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http' . '://' . $_SERVER['HTTP_HOST'] . __ACTION__;
        $this->root_path = dirname(APP_PATH) . '/';
        $this->avatardir = $this->root_path . $this->avatardir;
    }
    public function index($a = '')
    {
        /**
         * 程序入口文件。该文件的文件名可以乱改。
         * 参考过以下程序，在此一并致谢！
         *     - Comsenz UCenter {@link http://www.comsenz.com}
         *
         * @author Horse Luke<horseluke@126.com>
         * @license the Apache License, Version 2.0 (the "License"). {@link http://www.apache.org/licenses/LICENSE-2.0}
         * @version $Id: upload.php 159 2010-08-16 07:58:42Z horseluke@126.com $
         */
        //脚本运行区
        //定义运行开始
        define('SYSTEM_PATH', dirname(__FILE__) . '/');
        //获取动作名称

        if ($a) {
            $action = $a;
        } else if (!isset($_GET['a']) || empty($_GET['a']) || !is_string($_GET['a'])) {
            $action = 'showupload';
        } else {
            $action = $_GET['a'] ? $_GET['a'] : $a;
        }

        //因为这个程序只有一个控制器，所以直接实例化了

        $controller = $this;
        //如果没有设置则自动生成运行该脚本的网址（不含脚本名称）
        if (empty($controller->uc_api)) {
            $controller->uc_api = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') .
            '://' .
            /* $_SERVER['HTTP_HOST'].  */
            (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')) .
            substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
        }

        //运行控制器指定的动作
        if (method_exists($controller, $action)) {
            /*
            if(method_exists($controller, $action.'Before')){
            $controller->$action.'Before';
            }
             */

            $result = $controller->$action();

            /*
            if(method_exists($controller, $action.'After')){
            $controller->$action.'After';
            }
             */
            if (is_array($result)) {
                echo json_encode($result);
            } else {
                echo $result;
            }
        } else {
            exit('NO ACTION FOUND!');
        }
        /**
         * php 5.0新增的自动加载模式
         * 假如php版本高于5.0，并且在大于等于5.1.2的情况下没有开启spl_autoload_register，则使用此__autoload方法执行之。
         * 本函数依赖于：
         *   - 常量SYSTEM_PATH：框架组件目录
         * @param string $classname 遵循本框架命名规则的class名称
         */
        function __autoload($classname)
        {
            $path = SYSTEM_PATH . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';
            require $path;
        }

    }

    public function __get($name)
    {
        $this->$name = null;
        return null;
    }

    /**
     * 初始化输入（ok）
     *
     * @param string $getagent 指定的agent
     */
    public function init_input($getagent = '')
    {
        $input = self::getgpc('input', 'R');
        if ($input) {
            $input = self::authcode($input, 'DECODE', $this->authkey);
            parse_str($input, $this->input);
            $this->input = self::addslashes($this->input, 1, true);
            $agent       = $getagent ? $getagent : $this->input['agent'];

            if (($getagent && $getagent != $this->input['agent']) || (!$getagent && md5($_SERVER['HTTP_USER_AGENT']) != $agent)) {
                exit('Access denied for agent changed');
            } elseif (time() - $this->input('time') > 3600) {
                exit('Authorization has expired');
            }
        }
        if (empty($this->input)) {
            exit('Invalid input');
        }
    }

    /**
     * 查找$this->input是否存在指定索引的变量？（ok）
     *
     * @param string $k 要查找的索引
     * @return mixed
     */
    public function input($k)
    {
        return isset($this->input[$k]) ? (is_array($this->input[$k]) ? $this->input[$k] : trim($this->input[$k])) : null;
    }
    /**
     * 获取显示上传flash的代码(ok)
     * 来源：Ucenter的uc_avatar函数
     * 依赖性：
     *     逻辑代码上为依赖本类和common类；实际操作中还须配合如下文件/组件：
     *         - Ucenter的头像上传flash文件（swf文件）
     */
    public function showupload()
    {
        $uid = abs((int) self::getgpc('uid', 'G'));
        if ($uid === null || $uid == 0) {
            return -1;
        }
        $returnhtml = self::getgpc('returnhtml', 'G');
        if ($returnhtml === null) {
            $returnhtml = 1;
        }

        $uc_input = urlencode(self::authcode('uid=' . $uid .
            '&agent=' . md5($_SERVER['HTTP_USER_AGENT']) .
            "&time=" . time(),
            'ENCODE', $this->authkey)
        );

        $uc_avatarflash = $this->web_root . '/Style/header/images/camera.swf?nt=1&inajax=1&input=' . $uc_input . '&agent=' . md5($_SERVER['HTTP_USER_AGENT']) . '&ucapi=' . urlencode($this->uc_api . '') . '&uploadSize=' . $this->uploadsize;
        if ($returnhtml == 1) {
            $result = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="' . $uc_avatarflash . '" />
			<param name="menu" value="false" />
			<embed src="' . $uc_avatarflash . '" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
            return $result;
        } else {
            return array(
                'width', '450',
                'height', '253',
                'scale', 'exactfit',
                'src', $uc_avatarflash,
                'id', 'mycamera',
                'name', 'mycamera',
                'quality', 'high',
                'bgcolor', '#ffffff',
                'wmode', 'transparent',
                'menu', 'false',
                'swLiveConnect', 'true',
                'allowScriptAccess', 'always',
            );
        }
    }

    /**
     * 头像上传第一步，上传原文件到临时文件夹（ok）
     *
     * @return string
     */
    public function uploadavatar()
    {
        header("Expires: 0");
        header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", false);
        header("Pragma: no-cache");
        //header("Content-type: application/xml; charset=utf-8");
        $this->init_input(self::getgpc('agent', 'G'));

        $uid = $this->input('uid');
        if (empty($uid)) {
            return -1;
        }
        if (empty($_FILES['Filedata'])) {
            return -3;
        }

        $imgext = strtolower('.' . self::fileext($_FILES['Filedata']['name']));
        if (!in_array($imgext, $this->imgtype)) {
            unlink($_FILES['Filedata']['tmp_name']);
            return -2;
        }

        if ($_FILES['Filedata']['size'] > ($this->uploadsize * 1024)) {
            unlink($_FILES['Filedata']['tmp_name']);
            return 'Inage is TOO BIG, PLEASE UPLOAD NO MORE THAN ' . $this->uploadsize . 'KB';
        }
        if (!in_array($imgext, array('.jpg', '.png', '.gif'))) {
            return -2;
        }

        list($width, $height, $type, $attr) = getimagesize($_FILES['Filedata']['tmp_name']);
        $this->deldir($this->root_path . $this->tmpdir);
        $filetype  = $this->imgtype[$type];
        $tmpavatar = realpath($this->root_path . $this->tmpdir) . '/upload' . $uid . $filetype;
        file_exists($tmpavatar) && unlink($tmpavatar);
        if (is_uploaded_file($_FILES['Filedata']['tmp_name']) && move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpavatar)) {
            list($width, $height, $type, $attr) = getimagesize($tmpavatar);
            if ($width < 10 || $height < 10 || $type == 4) {
                unlink($tmpavatar);
                return -2;
            }
        } else {
            unlink($_FILES['Filedata']['tmp_name']);
            return -4;
        }
        $avatarurl = $this->web_root . '/' . $this->tmpdir . '/upload' . $uid . $filetype;
        return $avatarurl;
    }
    public function deldir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }
    }
    /**
     * 头像上传第二步，上传到头像存储位置
     *
     * @return string
     */
    public function rectavatar()
    {
        header("Expires: 0");
        header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/xml; charset=utf-8");
        $this->init_input(self::getgpc('agent'));
        $uid = abs((int) $this->input('uid'));
        if (empty($uid) || 0 == $uid) {
            return '<root><message type="error" value="-1" /></root>';
        }

        $avatarpath    = $this->get_avatar_path($uid);
        $avatarrealdir = realpath($this->avatardir . DIRECTORY_SEPARATOR . $avatarpath);
        if (!is_dir($avatarrealdir)) {
            $this->make_avatar_path($uid, realpath($this->avatardir));
        }
        $avatartype = self::getgpc('avatartype', 'G') == 'real' ? 'real' : 'virtual';

        $avatarsize = array(1 => 'big', 2 => 'middle', 3 => 'small');

        $success = 1;

        foreach ($avatarsize as $key => $size) {
            $avatarrealpath = realpath($this->avatardir) . DIRECTORY_SEPARATOR . $this->get_avatar_filepath($uid, $size, $avatartype);
            $avatarcontent  = $this->_flashdata_decode(self::getgpc('avatar' . $key, 'P'));
            if (!$avatarcontent) {
                $success = 0;
                return '<root><message type="error" value="-2" /></root>';
                break;
            }
            $writebyte = file_put_contents($avatarrealpath, $avatarcontent, LOCK_EX);
            if ($writebyte <= 0) {
                $success = 0;
                return '<root><message type="error" value="-2" /></root>';
                break;
            }
            $avatarinfo = getimagesize($avatarrealpath);
            if (!$avatarinfo || $avatarinfo[2] == 4) {
                $this->clear_avatar_file($uid, $avatartype);
                $success = 0;
                break;
            }
        }

        //原uc bugfix  gif/png上传之后不能删除
        foreach ($this->imgtype as $key => $imgtype) {
            $tmpavatar = realpath($this->tmpdir . '/upload' . $uid . $imgtype);
            file_exists($tmpavatar) && unlink($tmpavatar);
        }

        if ($success) {
            return '<?xml version="1.0" ?><root><face success="1"/></root>';
        } else {
            return '<?xml version="1.0" ?><root><face success="0"/></root>';
        }
    }

    /**
     * flash data decode
     * 来源：Ucenter
     *
     * @param string $s
     * @return unknown
     */
    protected function _flashdata_decode($s)
    {
        $r = '';
        $l = strlen($s);
        for ($i = 0; $i < $l; $i = $i + 2) {
            $k1 = ord($s[$i]) - 48;
            $k1 -= $k1 > 9 ? 7 : 0;
            $k2 = ord($s[$i + 1]) - 48;
            $k2 -= $k2 > 9 ? 7 : 0;
            $r .= chr($k1 << 4 | $k2);
        }
        return $r;
    }

    /**
     * 获取指定uid的头像规范存放目录格式
     * 来源：Ucenter base类的get_home方法
     *
     * @param int $uid uid编号
     * @return string 头像规范存放目录格式
     */
    public function get_avatar_path($uid)
    {
        $uid  = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        return $dir1 . '/' . $dir2 . '/' . $dir3;
    }

    /**
     * 在指定目录内，依据uid创建指定的头像规范存放目录
     * 来源：Ucenter base类的set_home方法
     *
     * @param int $uid uid编号
     * @param string $dir 需要在哪个目录创建？
     */
    public function make_avatar_path($uid, $dir = '.')
    {
        $uid  = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        !is_dir($dir . '/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
        !is_dir($dir . '/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);
        !is_dir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3) && mkdir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3, 0777);
    }

    /**
     * 获取指定uid的头像文件规范路径
     * 来源：Ucenter base类的get_avatar方法
     *
     * @param int $uid
     * @param string $size 头像尺寸，可选为'big', 'middle', 'small'
     * @param string $type 类型，可选为real或者virtual
     * @return unknown
     */
    public function get_avatar_filepath($uid, $size = 'big', $type = '')
    {
        $size    = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
        $uid     = abs(intval($uid));
        $uid     = sprintf("%09d", $uid);
        $dir1    = substr($uid, 0, 3);
        $dir2    = substr($uid, 3, 2);
        $dir3    = substr($uid, 5, 2);
        $typeadd = $type == 'real' ? '_real' : '';
        return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . $typeadd . "_avatar_$size.jpg";
    }

    /**
     * 一次性清空指定uid用户已经存储的头像
     *
     * @param int $uid
     */
    public function clear_avatar_file($uid)
    {
        $avatarsize = array(1 => 'big', 2 => 'middle', 3 => 'small');
        $avatartype = array('real', 'virtual');
        foreach ($avatarsize as $size) {
            foreach ($avatartype as $type) {
                $avatarrealpath = realpath($this->avatardir) . DIRECTORY_SEPARATOR . $this->get_avatar_filepath($uid, $size, $type);
                file_exists($avatarrealpath) && unlink($avatarrealpath);
            }
        }
        return true;
    }

    /**
     * dz经典加解密函数
     * 来源：Discuz! 7.0
     * 依赖性：可独立提取使用
     *
     * @param string $string 要加密/解密的字符串
     * @param string $operation 操作类型，可选为'DECODE'（默认）或者'ENCODE'
     * @param string $key 密钥，必须传入，否则将中断php脚本运行。
     * @param int $expiry 有效期
     * @return string
     */
    public static function authcode($string, $operation = 'DECODE', $key, $expiry = 0)
    {

        $ckey_length = 4; // 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥

        //取消UC_KEY，改为必须传入$key才能运行
        if (empty($key)) {
            exit('PARAM $key IS EMPTY! ENCODE/DECODE IS NOT WORK!');
        } else {
            $key = md5($key);
        }

        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey   = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box    = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }

    /**
     * 获取$_GET/$_POST/$_COOKIE/$_REQUEST数组的指定索引变量(ok)
     * 来源：Ucenter
     * 依赖性：可独立提取使用
     *
     * @param string $k 指定索引
     * @param string $var 获取来源。默认为'R'（即$_REQUEST），可选值'G'/'P'/'C'（对应$_GET/$_POST/$_COOKIE）
     * @return mixed
     */
    public static function getgpc($k, $var = 'R')
    {
        switch ($var) {
            case 'G':$var = &$_GET;
                break;
            case 'P':$var = &$_POST;
                break;
            case 'C':$var = &$_COOKIE;
                break;
            case 'R':$var = &$_REQUEST;
                break;
        }
        return isset($var[$k]) ? $var[$k] : null;
    }

    /**
     * 转义处理，改动自daddslashes函数(ok)
     * 来源：Ucenter
     * 依赖性：需要修改才能独立使用
     *
     * @param string $string
     * @param int $force
     * @param bool $strip
     * @return mixed
     */
    public static function addslashes($string, $force = 0, $strip = false)
    {

        if (!ini_get('magic_quotes_gpc') || $force) {
            if (is_array($string)) {
                $temp = array();
                foreach ($string as $key => $val) {
                    $key        = addslashes($strip ? stripslashes($key) : $key);
                    $temp[$key] = self::addslashes($val, $force, $strip);
                }
                $string = $temp;
                unset($temp);
            } else {
                $string = addslashes($strip ? stripslashes($string) : $string);
            }
        }
        return $string;
    }

    /**
     * 返回文件的扩展名
     * 来源：Discuz!
     * 依赖性：可独立提取使用
     *
     * @param string $filename 文件名
     * @return string
     */
    public static function fileext($filename)
    {
        return trim(substr(strrchr($filename, '.'), 1, 10));
    }

    /**
     * 获取指定对象或者指定索引对象的实例。没有则新建一个并且存储起来。
     *
     * @param string $classname 类名
     * @param string $index 索引，默认等同于$classname
     */
    public static function getInstanceOf($classname, $index = null)
    {
        if (null === $index) {
            $index = $classname;
        }
        if (isset(self::$_objectInstance[$index])) {
            $instance = self::$_objectInstance[$index];
            if (!($instance instanceof $classname)) {
                throw new Exception("Key {$index} has been tied to other thing.");
            }
        } else {
            $instance                      = new $classname();
            self::$_objectInstance[$index] = $instance;
        }
        return $instance;
    }
}
