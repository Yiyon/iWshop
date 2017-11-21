<?php

/**
 * Desc
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
$_TIME = time();

class Wechat {

    public $serverRoot;

    /**
     * request user openid
     * @var <string>
     */
    public $openID = null;

    /**
     * wechat origin id
     * @var <string>
     */
    private $serverID = null;

    /**
     * current time
     * @var <UNIX TIMESTAMP>
     */
    private $time;

    /**
     * mysql database
     * @var <PDO>
     */
    public $Db;

    /**
     * Dao
     * @var Dao
     */
    public $Dao;
    
    public $oss;

    /**
     * Wechat Class Construction method
     * @access public
     */
    public function __construct() {
        global $config;
        if (isset($_GET["echostr"]) && isset($_GET["signature"]) && isset($_GET["timestamp"]) && isset($_GET["nonce"])) {
            echo $_GET["echostr"];
            exit();
        }
        $this->Db         = new Db();
        $this->Dao        = new Dao();
        $this->time       = time();
        $this->serverRoot = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot;
        $this->oss = $config->oss;
    }

    public function valid() {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
            die($echoStr);
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];

        $token  = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 回复图文消息
     * @param type $data
     */
    public function responseImageText($data = array()) {
        $tpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>%s</ArticleCount>
        <Articles>%s</Articles>
        </xml>";

        $items = "";
        /*
         * Added By Lei
         * http://www.jiloc.com
         * jerry.jee@live.com
         * 没有设定关键词的默认回复，名为 default
         * 进行了wordpress的整合
         */
        foreach ($data as $item) {
            if($item['description']){
                $item['desc'] = substr( strip_tags( $item['description'] ),0,100);
                $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $item['description'] , $matches);
                $item['picurl'] = $matches [1] [0];
            }
            $items .= "<item>";
            // cont
            $items .= "<Title><![CDATA[" . $item['title'] . "]]></Title>";
            $items .= "<Description><![CDATA[" . $item['desc'] . "]]></Description>";
            if ($item['url']) {
                $items .= "<Url><![CDATA[" . $item['url'] . "]]></Url>";
            }
            if ($item['picurl']) {
                $items .= "<PicUrl><![CDATA[" . $item['picurl'] . "]]></PicUrl>";
            }
            // cont
            $items .= "</item>";
        }

        echo sprintf($tpl, $this->openID, $this->serverID, $this->time, count($data), $items);
        exit(0);
    }

    /**
     * 向客户端发送文本
     * @param string $contentStr
     */
    public function responseText($contentStr) {
        $textTpl = "<xml> 
                    <ToUserName><![CDATA[%s]]></ToUserName> 
                    <FromUserName><![CDATA[%s]]></FromUserName> 
                    <CreateTime>%s</CreateTime> 
                    <MsgType><![CDATA[%s]]></MsgType> 
                    <Content><![CDATA[%s]]></Content> 
                    <FuncFlag>0</FuncFlag> 
                    </xml>";
        echo sprintf($textTpl, $this->openID, $this->serverID, $this->time, "text", $contentStr);
        exit(0);
    }

    /**
     * 事件处理入口
     * @param type $postObj
     */
    public function EventRequest($postObj) {
        include dirname(__FILE__) . '/../wechat/EventHandler.php';
        $EventHandler             = new EventHandler();
        $EventHandler->wc         = $this;
        $EventHandler->openID     = $this->openID;
        $EventHandler->serverRoot = $this->serverRoot;
        $EventHandler->Db         = $this->Db;
        $EventHandler->Dao        = $this->Dao;
        $EventHandler->run($postObj);
    }

    /**
     * 语音处理入口
     * @param type $postObj
     */
    public function VoiceRequest($postObj) {
        $this->responseText($postObj->Recognition);
    }

    /**
     * 普通文本处理入口
     * @param type $Content
     */
    public function TextRequest($Content) {
        include dirname(__FILE__) . '/../wechat/TextHandler.php';
        $TextHandler             = new TextHandler();
        $TextHandler->wc         = $this;
        $TextHandler->openID     = $this->openID;
        $TextHandler->serverRoot = $this->serverRoot;
        $TextHandler->Db         = $this->Db;
        $TextHandler->Dao        = $this->Dao;
        $TextHandler->run($Content);
        // 多客服接口转发 @see http://dkf.qq.com/
        echo "<xml><ToUserName><![CDATA[$this->openID]]></ToUserName><FromUserName><![CDATA[$this->serverID]]></FromUserName><CreateTime>$this->time</CreateTime><MsgType><![CDATA[transfer_customer_service]]></MsgType></xml>";
    }

    /**
     * 主入口
     */
    public function handle() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        // 记录openid
        $this->openID = $postObj->FromUserName;
        // 记录服务号ID
        $this->serverID = $postObj->ToUserName;
        // 判断入口
        if (!empty($postStr) && !empty($this->openID)) {
            switch ($postObj->MsgType) {
                case "text" :
                    $this->TextRequest($postObj->Content);
                    break;
                case "event" :
                    $this->EventRequest($postObj);
                    break;
                case 'voice' :
                    // todo 记录语音消息
                    break;
            }
        } else {
            // 无效访问
        }
    }

    /**
     * getIPaddress
     * @return type
     */
    public function getIp() {
        $cIP  = getenv('REMOTE_ADDR');
        $cIP1 = getenv('HTTP_X_FORWARDED_FOR');
        $cIP2 = getenv('HTTP_CLIENT_IP');
        $cIP1 ? $cIP = $cIP1 : null;
        $cIP2 ? $cIP = $cIP2 : null;
        return $cIP;
    }

    /**
     * 回复图文内容
     * @param int $msgid
     */
    public function echoGmess($msgid) {
        $data = $this->Db->query("SELECT * FROM `gmess_page` WHERE `id` = $msgid;");
        $data = $data[0];
        $this->responseImageText(array(array(
                                           'title' => $data['title'],
                                           'url' => "$this->serverRoot?/Gmess/view/id=$msgid",
                                           'picurl' => $this->oss? $data['catimg']:$this->serverRoot . "uploads/gmess/" . $data['catimg'],
                                           'desc' => $data['desc']
                                       )));
    }

    /**
     * 自动红包
     * @param type $envid
     */
    public function autoEnvs() {
        $envid = $this->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = 'auto_envs';");
        if ($envid > 0) {
            $exp = date('Y-m-d H:i:s', strtotime('+30 day'));
            $uid = $this->Db->getOne("SELECT `client_id` FROM `clients` WHERE `client_wechat_openid` = '$this->openID';");
            $ext = $this->Db->getOne("SELECT openid FROM `client_autoenvs` WHERE `openid` = '$this->openID';");
            if (!$ext) {
                $uid = $uid > 0 ? $uid : 'NULL';
                $this->Db->query("INSERT INTO `client_envelopes` (openid,uid,envid,count,exp) VALUES('$this->openID',$uid,$envid,1,'$exp');");
                $this->Db->query("INSERT INTO `client_autoenvs` (openid,envid) VALUES('$this->openID',$envid);");
                Messager::sendText(WechatSdk::getServiceAccessToken(), $this->openID, "恭喜你获得红包一个，<a href='" . $this->serverRoot . "?/Uc/envslist/'>点击查看</a>");
            }
        }
    }

    /**
     * 获取系统设置
     * @param type $key
     * @return type
     */
    public function getSetting($key) {
        return $this->Db->getOne("SELECT `value` FROM `wshop_settings` WHERE `key` = '$key' LIMIT 1;");
    }

}

class WechatHandler {

    public $wc;
    public $openID;
    public $serverRoot;
    public $Dao;

    /**
     * 写入日志文件
     * @param mixed $message
     * @param int $type 默认错误级别
     */
    public function log($message, $type = self::LOG_ERRORS) {
        global $config;
        if (isset($config->logdir)) {
            $logdir = $config->logdir;
        } else {
            $logdir = APP_PATH . 'logs/';
        }
        if (!is_writable($logdir)) {
            chmod($logdir, 0777);
        }
        if (!is_dir($logdir)) {
            mkdir($logdir, 0777);
        }
        @error_log(date('Y-m-d H:i:s') . ': ' . $message . PHP_EOL, 3, $logdir . $type . '_log_' . date('Y-m-d') . '.txt');
    }

}
