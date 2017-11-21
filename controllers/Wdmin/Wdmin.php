<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Wdmin extends Controller {

    const COOKIE_EXP = 28800;
    const LIST_LIMIT = 100;
    const loginKeyK = '4s5mpxa';

    /**
     * 构造函数
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('Session');
        header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0"); // HTTP/1.1
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Pragma: no-cache"); // Date in the past
    }

    /**
     * 管理后台首页
     */
    public function index() {

        if (!$this->Auth->checkAuth()) {
            return $this->redirect('?/Wdmin/logOut');
        }

        if ($this->pCookie('loginKey')) {
            #$this->recycle();
            if (is_numeric($this->pCookie('lev'))) {
                $authStr                      = urldecode($this->pCookie('auth'));
                $this->cacheId                = $authStr;
                $this->Smarty->cache_lifetime = 7200;
                if (!$this->isCached()) {
                    $authArr = array();
                    foreach (explode(',', $authStr) as $a) {
                        $authArr[$a] = 1;
                    }
                    $this->Smarty->assign('adid', $this->pCookie('adid'));
                    $this->Smarty->assign('adname', $this->pCookie('adname'));
                    $this->Smarty->assign('admin_level', $this->pCookie('lev'));
                    $this->Smarty->assign('Auth', $authArr);
                    $weekarray = array(
                        "日",
                        "一",
                        "二",
                        "三",
                        "四",
                        "五",
                        "六"
                    );
                    $this->Smarty->assign('today', date("n月j号 星期") . $weekarray[date('w')]);
                }
                $this->show();
            }
        } else {
            header('Location:' . $this->root . '?/Wdmin/login');
            exit(0);
        }
    }

    /**
     * 退出登录清空cookie
     */
    public function logOut() {
        foreach ($_COOKIE as $k => $v) {
            setcookie($k, NULL);
        }
        header('Location:?/Wdmin/login/');
    }

    /**
     * 登录处理
     */
    public function checkLogin() {
        $this->Session->start();
        $ip = $this->getIp();
        $this->loadModel('WdminAdmin');
        $admin_acc = addslashes($this->post('admin_acc'));
        $admin_pwd = addslashes($this->post('admin_pwd'));
        // 保存登录账户
        $this->sCookie('admin_acc', $admin_acc, self::COOKIE_EXP);
        // admin login
        $admininfo = $this->WdminAdmin->get($admin_acc);
        // 写入登陆记录
        @$this->Db->query("INSERT INTO `admin_login_records` (`account`, `ip`, `ldate`) VALUE ('$admin_acc', '$ip', NOW())");
        if ($admininfo) {
            // 校验成功
            if ($this->WdminAdmin->pwdCheck((string)$admininfo['admin_password'], (string)$admin_pwd)) {
                // 更新管理员登录状态
                $this->WdminAdmin->updateAdminState($admin_acc, $ip, $admininfo['id']);
                // 权限密钥
                $loginKey = $this->WdminAdmin->encryptToken($ip, $admininfo['id']);
                $this->Session->set('loginKey', $loginKey);
                // 下发管理员权限表
                $this->sCookieHttpOnly('auth', $admininfo['admin_auth'], self::COOKIE_EXP);
                $this->sCookieHttpOnly('loginKey', $loginKey, self::COOKIE_EXP);
                $this->sCookieHttpOnly('adid', $admininfo['id'], self::COOKIE_EXP);
                $this->sCookieHttpOnly('adname', $admininfo['admin_name'], self::COOKIE_EXP);
                $this->sCookieHttpOnly('lev', 0, self::COOKIE_EXP);
                // 删除cookie
                $this->sCookie('admin_acc', '', 1);
                // 成功
                $this->echoJson(array('status' => 1));
            } else {
                // 失败
                $this->echoJson(array('status' => 0));
            }
        } else {
            // 失败
            $this->echoJson(array('status' => 0));
        }
        $this->sCookie('admin_acc', null);
    }

    /**
     * 登录页面
     */
    public function login() {
        $this->Smarty->assign('rand', (int)rand(1, 8));
        $this->Smarty->assign('ip', $this->getIp());
        $this->initSettings(true);
        $this->show();
    }

    /**
     * 获取订单列表
     */
    public function ajaxLoadOrderlist($Query) {

        if (!$this->Auth->checkAuth()) {
            return $this->redirect('?/Wdmin/logOut');
        }

        $this->cacheId = hash('md4', serialize($Query));

        if (!$this->isCached()) {
            $this->loadModel('mOrder');
            global $config;
            $express = include APP_PATH . 'config/express_code.php';
            !isset($Query->page) && $Query->page = 0;
            // where
            if (isset($Query->status)) {
                if ($Query->status == 'all') {
                    $WHERE = '';
                } else {
                    if ($Query->status == 'canceled') {
                        // 退货而且已经支付才需要审核，否则直接关闭订单
                        $WHERE = " WHERE status = '$Query->status' AND wepay_serial <> '' ";
                    } else {
                        $WHERE = " WHERE status = '$Query->status' ";
                    }
                }
            } else {
                $Query->status = 'payed';
                $WHERE         = " WHERE status = '$Query->status' ";
            }

            if (isset($Query->cid) && is_numeric($Query->cid)) {
                $WHERE .= " WHERE client_id = $Query->cid ";
            }

            if (isset($Query->month) && !empty($Query->month) && $Query->status != 'canceled') {
                if ($Query->status == 'all') {
                    $WHERE .= " WHERE DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
                }
                if ($Query->status == 'delivering') {
                    $WHERE .= "AND DATE_FORMAT(send_time,'%Y-%c') = '$Query->month' ";
                } else {
                    $WHERE .= "AND DATE_FORMAT(order_time,'%Y-%c') = '$Query->month' ";
                }
            }

            $Limit = $Query->page * self::LIST_LIMIT . "," . self::LIST_LIMIT;
            $SQL   = sprintf("SELECT * FROM `orders`%sORDER BY `order_id` DESC LIMIT $Limit;", $WHERE);

            $orderList = $this->Db->query($SQL);

            if ($Query->status == 'canceled') {
                foreach ($orderList as &$od) {
                    if ($od['order_amount'] < 1) {
                        $od['refundable'] = $od['order_amount'];
                    } else {
                        $od['refundable'] = $this->mOrder->getUnRefunded($od['order_id']);
                    }
                }
            }

            /**
             * 加工
             */
            foreach ($orderList as $index => $order) {
                // company
                if ($order['company_com'] > 0) {
                    $orderList[$index]['company'] = $this->Db->getOneRow("SELECT `id`,`name` FROM `companys` WHERE `id` = $order[company_com];");
                }
                // address
                $address                          = $this->Db->query("SELECT * FROM `orders_address` WHERE order_id = $order[order_id];");
                $orderList[$index]['address']     = $address[0];
                $orderList[$index]['order_time']  = $this->Util->dateTimeFormat($orderList[$index]['order_time']);
                $orderList[$index]['statusX']     = $config->orderStatus[$orderList[$index]['status']];
                $orderList[$index]['expressName'] = $express[$orderList[$index]['express_com']];
                // product info
                $orderList[$index]['data'] = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price FROM `orders_detail` sd LEFT JOIN `vproductinfo` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $order['order_id']);
            }

            $this->Smarty->assign('olistcount', count($orderList));
            $this->Smarty->assign('orderlist', $orderList);
        }

        if (isset($Query->export)) {
            $this->show('wdminpage/orders/ajaxloadorderlist_export.tpl');
        } else {
            if (isset($Query->cid) && is_numeric($Query->cid)) {
                $this->show('wdminpage/orders/ajaxloadorderlist_' . $Query->status . '_customer.tpl');
            } else {
                $this->show('wdminpage/orders/ajaxloadorderlist_' . $Query->status . '.tpl');
            }
        }
    }

    /**
     * 快递查询api
     * @see http://www.kuaidiapi.cn/
     * @param type $Query
     */
    public function ajaxLoadOrderExpress($Query) {

        $this->cacheId = $Query->com . $Query->nu;
        if (!$this->isCached()) {
            $typeCom = $Query->com; //快递公司
            $typeNu  = $Query->nu;  //快递单号
            $url     = "http://www.kuaidiapi.cn/rest/?uid=23350&key=7614261fa71a4948ad73795e88d958af&order=$typeNu&id=$typeCom";
            $this->Smarty->assign('res', json_decode(Curl::get($url), true));
        }
        $this->show();
    }

    /**
     * 回收数据
     */
    public function recycle() {

        if (!$this->Auth->checkAuth()) {
            $this->redirect('?/Wdmin/logOut');
        }

        $dirArr = array(
            APP_PATH . 'uploads/product_hpic_tmp/',
            APP_PATH . 'uploads/banner_tmp/',
            APP_PATH . 'uploads/gmess_tmp/'
        );
        foreach ($dirArr as $dir) {
            $dirs = dir($dir);
            if ($dirs && is_readable($dirs)) {
                try {
                    while ($file = $dirs->read()) {
                        $file = $dir . $file;
                        if (is_file($file)) {
                            if (time() - filemtime($file) > 86400) {
                                unlink($file);
                            }
                        }
                    }
                } catch (Exception $ex) {
                    continue;
                }
            }
        }
    }

    /**
     * 清除Smarty所有缓存
     * Added By : Lei
     * @link : http://www.jiloc.com
     */
    public function clearCacheAll() {

        if (!$this->Auth->checkAuth()) {
            $this->redirect('?/Wdmin/logOut');
        }

        //$this->Smarty->clearAllCache();
        //mod by zmq
        $dirArr = array(
            APP_PATH . 'tmp/',
            APP_PATH . 'tmp/tpl_compile/'
        );

        $img_cache_dir = APP_PATH . 'tmp/img_cache/';
        for($i = 0; $i < 16; $i++)
        {
            $hash_dir = $img_cache_dir . dechex($i);
            $dirArr[] = $hash_dir . '/';
        }
        $sql_cache_dir = APP_PATH . 'tmp/sql_cache/';
        for($i = 0; $i < 16; $i++)
        {
            $hash_dir = $sql_cache_dir . dechex($i);
            $dirArr[] = $hash_dir . '/';
        }

        $count=0;
        foreach ($dirArr AS $dir)
        {
            $folder = @opendir($dir);
            if ($folder === false)
            {
                continue;
            }
            while ($file = readdir($folder))
            {
                if (is_file($dir . $file))
                {
                    if (@unlink($dir . $file))
                    {
                        $count++;
                    }
                }
            }
            closedir($folder);
        }


        die("<h1>All Cache Cleared</h1>");
    }

}
