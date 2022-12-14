<?php
//include "./../../vendor/autoload.php";


require_once   ("D:\www\mw_svn/vendor/autoload.php"); //'./../../vendor/autoload.php';
//var_dump(dirname(__FILE__).'/'."../../vendor/autoload.php");
//require_once(dirname(__FILE__).'/'."../../vendor/autoload.php");

use PhpAmqpLib\Connection\AMQPStreamConnection;
//phpinfo();
//use think\Log;
//var_dump();
//exit;
/**
 * 消费者
 */
class Consumer
{
    // 连接
    protected $connection;
    // 管道
    protected $channel;
    // 配置内容
    protected $mqConfig;

    public function __construct()
    {
        $this->mqConfig = ['host'=>'123.56.184.150',
            'port'=>'5672',
            'user'=>'xunyuji',
            'password'=>'xunyuji',
            'vhost'=>'xunyuji',
            'exchange_name' => 'order',
            'queue_name' => 'order',
            'route_key' => 'order',
            'consumer_tag' => 'consumer',];
        // 创建连接
        $this->connection = new AMQPStreamConnection(
            $this->mqConfig['host'],
            $this->mqConfig['port'],
            $this->mqConfig['user'],
            $this->mqConfig['password'],
            $this->mqConfig['vhost']
        );

        //$cnn=new
        // 创建通道
        $this->channel = $this->connection->channel();
    }
    /**
     * 启动
     * nohup php index.php index/Message_Consume/start &
     */
    public function start()
    {
        // 设置消费者（Consumer）客户端同时只处理一条队列
        // 这样是告诉RabbitMQ，再同一时刻，不要发送超过1条消息给一个消费者（Consumer），
        // 直到它已经处理了上一条消息并且作出了响应。这样，RabbitMQ就会把消息分发给下一个空闲的消费者（Consumer）。
        // 消费者端要把自动确认autoAck设置为false，basic_qos才有效。
        // $this->channel->basic_qos(0, 1, false);
        // 同样是创建路由和队列，以及绑定路由队列，注意要跟producer（生产者）的一致
        // 这里其实可以不用设置，但是为了防止队列没有被创建所以做的容错处理
        $this->channel->queue_declare($this->mqConfig['queue_name'], false, true, false, false);
        $this->channel->exchange_declare($this->mqConfig['exchange_name'], 'direct', false, true, false);
        $this->channel->queue_bind($this->mqConfig['queue_name'], $this->mqConfig['exchange_name'], $this->mqConfig['route_key']);
        /**
         * queue: queue_name     被消费的队列名称
         * consumer_tag: consumer_tag  消费者客户端身份标识，用于区分多个客户端
         * no_local: false       这个功能属于AMQP的标准，但是RabbitMQ并没有做实现
         * no_ack: true          收到消息后，是否不需要回复确认即被认为被消费
         * exclusive: false      是否排他，即这个队列只能由一个消费者消费。适用于任务不允许进行并发处理的情况下
         * nowait: false         不返回执行结果，但是如果排他开启的话，则必须需要等待结果的，如果两个一起开就会报错
         * callback: $callback   回调逻辑处理函数
         */
        $this->channel->basic_consume($this->mqConfig['queue_name'], $this->mqConfig['consumer_tag'], false, false, false, false, array($this, 'process_message'));
        register_shutdown_function(array($this, 'shutdown'), $this->channel, $this->connection);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
            //exit;
        }
    }

    /**
     * 消息处理
     * @param $message
     */
    public function process_message($message)
    {
        if ($message->body !== 'quit') {
            $messageBody = json_decode($message->body);
            // 自定义的消息类型
//            if (!isset($messageBody->message_type)) {
//                //Log::write("error data:" . $message->body, 2);
//                echo $message->body;
//            } else {
//                $messageModel = new MessageModel();
                try {
//                    include './PaynewAction.class.php';
//                    $pay=new PaynewAction();
//
//                    //echo json_encode($message, JSON_UNESCAPED_UNICODE);
                     $body = json_decode($message->body, true);
                     $body= http_build_query($body, '', '&');
                      //$body=$message->body;
//                    $pay->dogmzf($body);
                    $url=  "https://" . $_SERVER['SERVER_NAME']."/index.php/Paynew/dogmzf?".$body;
                    $url=  "http://ccx.com/index.php/Paynew/dogmzf?".$body;
                    $aa=$this->curl_posts($url,$body);
                    $aa = json_decode($aa, true);
                    if($aa['status']=='1'){
                        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
                    }
                    var_dump($aa);
                    // 消息
                    //Log::write("MQ_message_data:" . json_encode($message, JSON_UNESCAPED_UNICODE));
//                    $messageModel->test($body);
                } catch (Exception $pe) {
//                    Log::write($pe->getMessage(), 2);
//                    Log::write(json_encode($message), 2);
                    echo $pe->getMessage();
                }
            }
       // }

        // 手动确认ack，确保消息已经处理

        // Send a message with the string "quit" to cancel the consumer.
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
        exit;
    }
    /**CURL模拟post提交
     * $url post提交的url地址
     * $jsonData 要提交的json 数据
     */
    public function curlPostData($url, $jsonData){

        $ch = curl_init($url) ;
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch) ;
        curl_close($ch) ;
        return $result;
    }

    public function getCurlApis($url,$data)
    {
       //var_dump($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);//设置调用地址
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));//添加头信息
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//返回值输出方式
        curl_setopt($curl, CURLOPT_POST, 1);//POST数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, ['appid'=>$appid,'nonce'=>$nonce,'timestamp'=>$timestamp,'signature'=>$signature]);//把post的变量加上
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);  //禁用后cURL将终止从服务端进行验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);  //不验证证书是否存在
         curl_setopt($curl, CURLOPT_HEADER, FALSE);    //禁止后使用CURL_TIMECOND_IFUNMODSINCE，默认值为CURL_TIMECOND_IFUNMODSINCE
        $output = curl_exec($curl);
        //$result = curl_exec ($ch);
        curl_close($curl);
        return $output;

    }

     /**
     * 发起HTTPS请求
     */
    public function curl_posts($url,$data,$header= array("Content-type: application/json"),$post=1)
    {   //var_dump($data);
        //初始化curl
        $ch = curl_init();
        //参数设置
        $res= curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);

        if($post)
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 关闭进程
     * @param $channel
     * @param $connection
     */
    public function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
    }
    public function  rlog($content){
        $file='./test.txt';
        file_put_contents($file, $content,FILE_APPEND);
    }

}

$aa=new Consumer();
$aa->start();
var_dump(1);