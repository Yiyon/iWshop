<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * 用户个人中心控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Uc extends Controller {

    const COOKIEXP = 3600;

    /**
     * Uc constructor.
     * @param $ControllerName
     * @param $Action
     * @param $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('User');
    }

    /**
     * 登陆页面
     */
    public function login() {
        $this->assign('title', "用户登陆");
        $this->assign('bagRand', intval(rand(1, 3)));
        $this->show("uc/login.tpl");
    }

    /**
     * 注册页面
     */
    public function reg() {
        $this->assign('title', "用户注册");
        //$this->assign('bagRand', intval(rand(1, 3)));
        //$this->show("uc/login.tpl");
        $this->show("uc/wechatplease.tpl");
    }

    /**
     * user Home
     * 用户中心首页
     */
    public function home() {

        //$this->sCookie('uid', 5);
        //$this->sCookie('uopenid', 'oR8PjwATS61DqY1kDnK76hMe_M6A');

        // get openid
        $Openid = $this->getOpenId();
        // 微信自动注册
        $this->User->wechatAutoReg($Openid);

        $Uid = $this->getUid();

        if (!empty($Openid)) {

            $company_id = 0;

            $this->Smarty->caching = false;

            $this->loadModel([
                'mOrder', 'UserLevel', 'mCompany'
            ]);

            // 回收过期订单
            $this->mOrder->orderReclycle($Uid);

            $userInfo = false;

            if (!$Uid) {
                // uid cookie 过期或者未注册
                if (!empty($Openid)) {
                    if (!$this->User->checkUserExt($Openid)) {
                        // 用户在微信里面 但是居然不存在这个用户
                        $this->redirect($this->root . '?/Uc/wechatPlease');
                    } else {
                        // 获取uid
                        $Uid = $this->User->getUidByOpenId($Openid);
                    }
                    $userInfo = $this->User->getUserInfoRaw($Uid);
                }
            } else {
                // 用户已注册
                $userInfo = $this->User->getUserInfoRaw($Uid);
                // 刷新微信头像
                if (time() - strtotime($userInfo['client_head_lastmod']) > 432000 && Controller::inWechat()) {
                    $AccessCode = WechatSdk::getAccessCode($this->uri, "snsapi_userinfo");
                    if ($AccessCode) {
                        // 获取到accesstoken和openid
                        $Result = WechatSdk::getAccessToken($AccessCode);
                        // 微信用户资料
                        $WechatUserInfo = WechatSdk::getUserInfo($Result->access_token, $Result->openid);
                        $this->Dao->update(TABLE_USER)
                                  ->set([
                                      'client_head' => preg_replace("/\\/0/", "", $WechatUserInfo->headimgurl),
                                      'client_head_lastmod' => 'NOW()'
                                  ])
                                  ->where([
                                      'client_wechat_openid' => $Result->openid
                                  ])
                                  ->exec();
                    }
                }
            }

            if (!$userInfo) {
                $this->redirect($this->root . '?/Uc/login');
            }

            // 代理开关
            $companyOn = $this->getSetting('company_on') == 0;
            // 如果是代理，获取代理数据
            if ($userInfo['is_com'] == 1) {
                $company_id = $this->mCompany->getCompanyIdByOpenId($Openid);
                $income     = $this->mCompany->getCompanyIncomeCount($company_id, false, false);
                $this->assign('income', $income);
            } else {
                // 是否正在审核中
                if ($this->mCompany->isReqesting($Uid)) {
                    $companyOn = false;
                }
            }

            // 刷新uid cookie
            $this->sCookie('uid', $Uid, Uc::COOKIEXP);

            // 统计数据
            $count = array();

            $count[] = $this->Db->getOne("SELECT COUNT(`order_id`) AS `count` FROM `orders` WHERE `status` = 'unpay' AND `wepay_openid` = '$Openid';");
            $count[] = $this->Db->getOne("SELECT COUNT(`order_id`) AS `count` FROM `orders` WHERE `status` = 'payed' AND `wepay_openid` = '$Openid';");
            $count[] = $this->Db->getOne("SELECT COUNT(`order_id`) AS `count` FROM `orders` WHERE `status` = 'delivering' AND `wepay_openid` = '$Openid';");
            $count[] = $this->Db->getOne("SELECT COUNT(`order_id`) AS `count` FROM `orders` WHERE `status` = 'received' AND is_commented = 0 AND `wepay_openid` = '$Openid';");
            $count[] = $this->Db->getOne("SELECT COUNT(`order_id`) AS `count` FROM `orders` WHERE `status` = 'canceled' AND `wepay_serial` <> '' AND `wepay_openid` = '$Openid';");

            $this->assign('comid', $company_id);
            $this->assign('level', $this->UserLevel->getLevByUid($Uid));
            $this->assign('count_envs', $this->Db->getOne("SELECT COUNT(`id`) AS `count` FROM `client_envelopes` WHERE `uid` = '$Uid' AND `count` > 0 AND `exp` > NOW();"));
            $this->assign('count_like', $this->Db->getOne("SELECT COUNT(`id`) AS `count` FROM `client_product_likes` WHERE `openid` = '$Openid';"));
            $this->assign('count', $count);
            $this->assign('bagRand', intval(rand(1, 3)));
            $this->assign('userinfo', $userInfo);
            $this->assign('companyOn', $companyOn);
            $this->assign('ucBanners', $this->Banners->getBanners(2));

            $this->show();
        }else{
            $this->assign('title', "用户登陆");
            $this->assign('bagRand', intval(rand(1, 3)));
            //$this->show("uc/login.tpl");
            $this->show("uc/wechatplease.tpl");
        }
    }

    /**
     * 请使用微信访问页面
     */
    public function wechatPlease() {
        $this->show();
    }

    /**
     * 我的红包列表
     */
    public function envslist() {
        $this->loadModel('Envs');
        $Openid = $this->getOpenId();
        // 微信注册
        $this->User->wechatAutoReg($Openid);
        $envs = $this->Envs->getUserEnvs($this->getUid());
        $this->assign('envs', $envs);
        $this->assign('title', '我的红包');
        $this->show();
    }

    /**
     * 代理个人中心
     */
    public function companySpread() {
        // 统计数据 
        $uid = $this->pCookie('uid');
        $this->loadModel('User');
        # $userInfo = $this->User->getUserInfo();
        if (!$this->isCompany($uid)) {
            header('Location:' . $this->root . '?/WechatWeb/proxy/');
        } else {
            $comRow   = $this->Dao->select()
                                  ->from('companys')
                                  ->where("uid=$uid")
                                  ->getOneRow();
            $comid      = $comRow['id'];
            $comR       = $comRow['return_percent']; //返佣率
            $comtype    = $comRow['utype'];          //代理等级
            //临时开发给大家先测试用，后期可写入到数据库中 by zmq2163
            $typenum	= array(
                "代理I","代理II","代理III"
            );
            //$this->log("----".$comid."----".$comtype);
            $this->assign("comR", $comR);
            $this->assign("comtype", $comtype);
            $this->assign("typenum", $typenum);

            $userInfo   = $this->User->getUserInfoRaw();
            $this->assign("userinfo", $userInfo);
            $spreadData = $this->Db->getOneRow("select sum(readi) as readi,sum(turned) as turned from company_spread_record WHERE com_id = '$comid';");
            // 转化率
            $spreadData['turnrate'] = sprintf('%.2f', $spreadData['readi'] > 0 ? ($spreadData['turned'] / $spreadData['readi']) : 0);
            // 总收益
            $spreadData['incometot'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid';");
            $spreadData['incometot'] = $spreadData['incometot'] > 0 ? $spreadData['incometot'] : 0;
            // 今日收益
            $spreadData['incometod'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid' AND to_days(date) = to_days(now());");
            $spreadData['incometod'] = $spreadData['incometod'] > 0 ? $spreadData['incometod'] : 0;
            // 昨日收益
            $spreadData['incometotyet'] = $this->Db->getOne("SELECT sum(amount) AS amount FROM `company_income_record` WHERE com_id = '$comid' AND to_days(date) = to_days(now()) - 1;");
            $spreadData['incometotyet'] = $spreadData['incometotyet'] > 0 ? $spreadData['incometotyet'] : 0;
            // 名下用户总数
            $spreadData['ucount'] = $this->Db->getOne("SELECT count(*) AS amount FROM `company_users` WHERE comid = '$comid';");
            $spreadData['ucount'] = $spreadData['ucount'] > 0 ? $spreadData['ucount'] : 0;
            // 名下用户列表
            $spreadData['ulist'] = $this->Dao->select()
                                             ->from('clients')
                                             ->where('client_comid=' . $comid)
                                             ->exec();
            foreach ($spreadData['ulist'] as &$l) {
                $r            = $this->Db->getOneRow("SELECT COUNT(*) as count, SUM(amount) as am FROM `company_income_record` WHERE com_id = $comid AND client_id = $l[client_id];");
                $l['od']      = $r['count'];
                $l['oamount'] = $r['am'] > 0 ? sprintf('%.2f', $r['am']) : '0.00';
            }
            $this->assign('stat_data', $spreadData);
            $this->assign('title', '我的推广');
            $this->show();
        }
    }

    /**
     * 代理资料修改
     */
    public function companyEdit() {
        $uid = $this->pCookie('uid');
        $this->loadModel('mCompany');
        if (!$this->isCompany($uid)) {
            header('Location:' . $this->root . '?/WechatWeb/proxy/');
        } else {
            $this->loadModel('mCompany');
            $cominfo = $this->Dao->select('*')
                                 ->from('companys')
                                 ->where("uid=$uid")
                                 ->getOneRow();
            $this->assign('cominfo', $cominfo);
            $this->show();
        }
    }

    /**
     * 订单列表
     * @param type $Query
     */
    public function orderlist($Query) {
        $this->loadModel('JsSdk', 'WechatSdk');
        $this->Smarty->caching = false;
        $signPackage           = $this->JsSdk->GetSignPackage();
        $openid                = $this->getOpenId();
        !isset($Query->status) && $Query->status = '';
        $this->assign('signPackage', $signPackage);
        $this->assign('status', $Query->status);
        $this->assign('title', '我的订单');
        $this->show();
    }

    /**
     * Ajax订单列表
     * @param type
     */
    public function ajaxOrderlist($Query) {

        $this->Db->cache       = false;
        $this->Smarty->caching = false;
        $openid                = $this->pCookie('uopenid');

        if (empty($openid)) {
            die(0);
        } else {
            !isset($Query->page) && $Query->page = 0;
            $limit = (5 * $Query->page) . ",5";

            if (!$this->isCached()) {
                global $config;
                $this->loadModel('Product');
                if ($Query->status == '' || !$Query->status) {
                    $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' ORDER BY `order_time` DESC LIMIT $limit;";
                } else {
                    if ($Query->status == 'canceled') {
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status' AND `wepay_serial` <> '' ORDER BY `order_time` DESC LIMIT $limit;";
                    } else if ($Query->status == 'received') {
                        // 待评价订单列表
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status' AND `is_commented` = 0 ORDER BY `order_time` DESC LIMIT $limit;";
                    } else {
                        // 其他普通列表
                        $SQL = "SELECT * FROM `orders` WHERE `wepay_openid` = '$openid' AND `status` = '$Query->status' ORDER BY `order_time` DESC LIMIT $limit;";
                    }
                }
                $orders = $this->Db->query($SQL, false);
                foreach ($orders AS &$_order) {
                    // 是否为代付
                    $_order['isreq']      = false;
                    $_order['statusX']    = $config->orderStatus[$_order['status']];
                    $_order['order_time'] = $this->Util->dateTimeFormat($_order['order_time']);
                    $_order['data']       = $this->Db->query("SELECT catimg,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price,`sd`.product_price_hash_id " . "FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $_order['order_id']);
                    // 整理商品数据
                    foreach ($_order['data'] as &$data) {
                        $d             = $this->Product->getProductInfoWithSpec($data['product_id'], $data['product_price_hash_id']);
                        $data['spec1'] = $d['det_name1'];
                        $data['spec2'] = $d['det_name2'];
                    }
                }
                $this->assign('orders', $orders);
            }
        }
        $this->show();
    }

    /**
     * 查看订单详情
     * @param type $orderid
     */
    public function viewOrder() {
        $this->show();
    }

    /**
     * 判断是否微代理
     */
    private function isCompany($openid) {
        return $this->Db->query("SELECT `uid` FROM `companys` WHERE `uid` = '$openid';");
    }

    /**
     * 我的收藏页面
     */
    public function uc_likes() {
        $this->assign('title', '我的收藏');
        $this->show();
    }

    /**
     * 获取收藏列表
     * @param type $Query
     */
    public function ajaxLikeList($Query) {
        $openid        = $this->getOpenId();
        $this->cacheId = $openid . $Query->page;
        if (!$this->isCached()) {
            !isset($Query->page) && $Query->page = 0;
            $limit = ($Query->page * 10) . ',10';
            $this->loadModel('User');
            $likeList = $this->User->getUserLikes($openid, $limit);
            if ($likeList !== false) {
                $this->assign('loaded', count($likeList));
                $this->assign('likeList', $likeList);
            } else {
                $this->assign('loaded', 0);
            }
        }
        $this->show();
    }

    /**
     * ajax获取用户分组
     */
    public function getAllGroup() {
        $this->loadModel('SqlCached');
        // file cached
        $cacheKey  = 'ucajaxGetCategroys';
        $fileCache = new SqlCached();
        $ret       = $fileCache->get($cacheKey);
        if (-1 === $ret) {
            $this->loadModel('UserLevel');
            $lev  = $this->UserLevel->getList();
            $levs = array();
            foreach ($lev as $l) {
                $levs[] = array('dataId' => $l['id'], 'name' => $l['level_name']);
            }
            $cats = $this->toJson($levs);
            $fileCache->set($cacheKey, $cats);
            echo $cats;
        } else {
            echo $ret;
        }
    }

    /**
     * 查看物流情况
     */
    public function expressDetail($Query) {

        global $config;

        $openId = $this->getOpenId();

        $this->loadModel('mOrder');

        $this->cacheId = $openId . $Query->order_id;

        if (!$this->isCached()) {

            // 通知人员openid
            $openIds = explode(',', $this->getSetting('order_notify_openid'));
            // 配送人员openid
            $openIdExps = explode(',', $this->getSetting('order_express_openid'));
            // 允许查看订单
            $openIds = array_merge($openIdExps, $openIds);

            $Query->order_id = addslashes($Query->order_id);

            // 订单信息
            $orderData = $this->Db->getOneRow("SELECT * FROM `orders` WHERE `order_id` = $Query->order_id;");

            $openIds[] = $orderData['wepay_openid'];

            if (!in_array($openId, $openIds) || empty($openId)) {
                echo 0;
            } else {
                $this->loadModel('Product');
                $orderProductsList         = $this->Db->query("SELECT `catimg`,`pi`.product_name,`pi`.product_id,`sd`.product_count,`sd`.product_discount_price,`sd`.product_price_hash_id FROM `orders_detail` sd LEFT JOIN `products_info` pi on pi.product_id = sd.product_id WHERE `sd`.order_id = " . $Query->order_id);
                $expressCode               = include APP_PATH . 'config/express_code.php';
                $orderData['address']      = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE `order_id` = $Query->order_id;");
                $orderData['express_com1'] = $expressCode[$orderData['express_com']];
                $orderData['statusX']      = $config->orderStatus[$orderData['status']];
                foreach ($orderProductsList as &$pds) {
                    $d            = $this->Product->getProductInfoWithSpec($pds['product_id'], $pds['product_price_hash_id']);
                    $pds['spec1'] = $d['det_name1'];
                    $pds['spec2'] = $d['det_name2'];
                }
                $this->assign('orderdetail', $orderData);
                $this->assign('productlist', $orderProductsList);
                $this->assign('title', '订单详情');
            }
        }

        $this->show();
    }

    /**
     * 积分兑换页面
     */
    public function credit_exchange() {
        $this->loadModel('CreditExchange');
        $list = $this->CreditExchange->getList(false);
        $this->assign('list', $list);
        $this->assign('title', '积分兑换');
        $this->show();
    }

    /**
     * 积分兑换详情选择地址页面
     */
    public function credit_exchange_detail($Query) {
        global $config;
        $this->loadModel('WechatSdk');
        $this->loadModel('JsSdk');

        $this->log('---'.$Query->pid);
        $this->log('---'.$Query->credit);
        $pid    = $Query->pid;
        $credit = $Query->credit;

        if ($pid > 0) {
            $this->loadModel('Product');
            $product = $this->Product->getProductInfo($pid, true);
        }

        if (Controller::inWechat()) {
            // 请求收货地址参数数据
            include_once(APP_PATH . "lib/wepaySdk/SignTool.php");
            $OauthURL        = $this->root . '?/Uc/credit_exchange_detail/pid=' . $pid .'&credit='.$credit;;
            $FinalURL        = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" . $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
            $addrsignPackage = WechatSdk::getAddrShareSign($OauthURL, $FinalURL);
            $this->assign('addrsignPackage', $this->toJson($addrsignPackage));
        } else {
            $this->assign('addrsignPackage', '{}');
        }
        $signPackage = $this->JsSdk->GetSignPackage();
        $this->assign('signPackage', $signPackage);

        $this->assign('title', '积分兑换');
        $this->assign('credit', $credit);
        $this->assign('pid', $pid);
        $this->assign('product', $product);

        $this->show();
    }

    /**
     * 检查是否可以兑换某产品
     */
    public function credit_exchange_check() {
        $openid = $this->getOpenId();
        $pid    = $this->post('pid');
        $uid    = $this->getUid();
        if ($uid > 0 && $this->User->checkUserExt($openid)) {
            $this->loadModel('CreditExchange');
            $creditReq = $this->CreditExchange->getReq($pid);
            $credit    = $this->User->getCredit($uid);
            if ($credit > 0 && $credit >= $creditReq) {
                $this->echoMsg(0);
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 确定兑换某产品
     */
    public function credit_exchange_confirm() {
        $openid     = $this->getOpenId();
        $pid        = $this->post('pid');
        $addrData   = $this->pPost('addrData');
        if (empty($addrData)) {
            return $this->echoMsg(-1, '地址数据非法');
        }

        $uid    = $this->getUid();
        if ($uid > 0 && $this->User->checkUserExt($openid)) {
            $this->loadModel([
                'CreditExchange', 'User', 'mOrder'
            ]);
            $creditReq = $this->CreditExchange->getReq($pid);
            $credit    = $this->User->getCredit($uid);
            $newcredit = $credit - $creditReq;
            if ($newcredit >= 0) {

                $this->Db->transtart();

                try {

                    // 获取最近的收货地址
                    // TODO 前端用户选择收货地址
//                    $address = $this->Dao->select()
//                                         ->from(TABLE_ORDER_ADDRESS)
//                                         ->where("client_id = $uid")
//                                         ->getOneRow();
//
//                    $addressFormat = array(
//                        'proviceFirstStageName' => $address['province'],
//                        'addressCitySecondStageName' => $address['city'], 'addressCountiesThirdStageName' => '',
//                        'addressDetailInfo' => $address['address'], 'addressPostalCode' => $address['postal_code'],
//                        'telNumber' => $address['tel_number'], 'userName' => $address['user_name']
//                    );

                    $this->mOrder->create($openid, array("p{$pid}m0" => 1), $addrData, [
                        'status' => 'payed'
                    ]);

                    $this->User->setCredit($uid, $newcredit, '积分兑换商品-' . $creditReq);

                    $this->Db->transcommit();

                    $this->echoSuccess();

                } catch (Exception $e) {

                    $this->Db->transrollback();

                    $this->echoFail();

                }
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

}
