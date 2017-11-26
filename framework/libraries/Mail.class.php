<?php
    class Mail {
    private $host;
    private $port = 25;
    private $user;
    private $pass;
    private $debug = false;
    private $sock;
    private $from;
    
    public function __construct($config) {
        $this->host = isset($config['host'])? $config['host'] : 'smtp.163.com';
        $this->port = isset($config['port'])? $config['port'] : 25;
        $this->user = isset($config['username'])? base64_encode($config['username']) : 'localhost';
        $this->pass = isset($config['password'])? base64_encode($config['password']) : 'localhost';
        $this->from = isset($config['from'])? $config['from'] : 'yingview@163.com';
        $this->debug = isset($config['debug'])? $config['debug'] : false;
    //socket连接
        $this->sock = fsockopen($this->host,$this->port);
        if(!$this->sock){
        exit('出错啦');
        }
    //读取smtp服务返回给我们的数据
        $response = fgets($this->sock);
        $this->debug($response);
            //如果响应中有220返回码，说明我们连接成功了
        if(strstr($response,'220') === false){
        exit('出错啦');
        }
    }
    //发送SMTP指令，不同指令的返回码可能不同
    public function execCommand($cmd,$return_code){
        fwrite($this->sock,$cmd);
    
        $response = fgets($this->sock);
    //输出调试信息
        $this->debug('cmd:'.$cmd .';response:'.$response);
        if(strstr($response,$return_code) === false){
        return false;
        }
        return true;
    }
    
    public function sendMail($to,$subject,$body){
        $from = $this->from;
    //detail是邮件的内容，一定要严格按照下面的格式，这是协议规定的
        $detail = 'From:'.$from."\r\n";
        $detail .= 'To:'.$to."\r\n";
        $detail .= 'Subject:'.$subject."\r\n";
        $detail .= 'Content-Type: Text/html;'."\r\n";
        $detail .= 'charset=gb2312'."\r\n\r\n";
        $detail .= $body;
        $this->execCommand("HELO ".$this->host."\r\n",250);
        $this->execCommand("AUTH LOGIN\r\n",334);
        $this->execCommand($this->user."\r\n",334);
        $this->execCommand($this->pass."\r\n",235);
        $this->execCommand("MAIL FROM:<".$from.">\r\n",250);
        $this->execCommand("RCPT TO:<".$to.">\r\n",250);
        $this->execCommand("DATA\r\n",354);
        $this->execCommand($detail."\r\n.\r\n",250);
        $this->execCommand("QUIT\r\n",221);
    }
    
    public function debug($message){
        if($this->debug){
        echo '<p>Debug:'.$message . PHP_EOL .'</p>';
        }
    }
    
    public function __destruct()
    {
        fclose($this->sock);
    }
    
    }
/**
* $to = '2379183056@qq.com';
* $body = 'hello world1212';
* $subjet = '我是标题111';
* $mailer = new Mail($GLOBALS['mailconfig']);
* $mailer -> sendMail($to,$subjet,$body);
 */

?>