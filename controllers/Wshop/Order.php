<?php

/**
 * 订单控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Order extends Controller {

    /**
     * 构造函数
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
    }

    /**
     * 购物车
     */
    public function cart() {
        global $config;
        $this->loadModel('User');
        $this->loadModel('Envs');
        $this->loadModel('WechatSdk');
        $this->loadModel('JsSdk');
        $this->caching = false;
        //获取用户红包列表
        $envs = $this->Envs->getUserEnvs($this->getUid());
        $this->initSettings(true);
        if (Controller::inWechat()) {
            // 请求收货地址参数数据
            include_once(APP_PATH . "lib/wepaySdk/SignTool.php");
            $OauthURL        = $this->root . $config->wxpayroot . '?id=' . $_GET['id'];
            $FinalURL        = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" . $this->server('HTTP_HOST') . $this->server('REQUEST_URI');
            $addrsignPackage = WechatSdk::getAddrShareSign($OauthURL, $FinalURL);
            $this->assign('addrsignPackage', $this->toJson($addrsignPackage));
        } else {
            $this->assign('addrsignPackage', '{}');
        }
        $signPackage = $this->JsSdk->GetSignPackage();
        $this->assign('recis', explode(',', $this->settings['reci_cont']));
        $this->assign('envs', $envs);
        $this->assign('signPackage', $signPackage);
        $this->assign('title', '购物车');
        $this->assign('promId', $_GET['id']);
        $this->assign('promAva', $this->checkPromLimit($_GET['id']) ? 1 : 0);
        $this->assign('userInfo', (array)$this->User->getUserInfo());
        $this->show("wshop/order/cart.tpl");
    }

    /**
     * Ajax生成订单
     */
    public function ajaxCreateOrder() {
        $this->loadModel('mOrder');
        $openid   = $this->getOpenId();
        $cartData = $this->pPost('cartData');
        $addrData = $this->pPost('addrData');
        if (empty($cartData)) {
            return $this->echoMsg(-1, '订单数据非法');
        } else {
            $cartData = json_decode($cartData, true);
        }
        if (empty($addrData)) {
            return $this->echoMsg(-1, '地址数据非法');
        }
        try {
            $orderId = $this->mOrder->create($openid, $cartData, $addrData, [
                'remark' => $this->post('remark'),
                'exptime' => $this->post('exptime'),
                'balancePay' => $this->post('balancePay') == 1,
                'expfee' => $this->post('expfee'),
                'envsid' => intval($this->post('envsId')),
            ]);
            $this->echoMsg(0, intval($orderId));
        } catch (Exception $ex) {
            $this->log('order_create_error:' . $ex->getMessage());
            $this->echoMsg(-1, $ex->getMessage());
        }
    }

    /**
     * Ajax获取订单请求数据包
     */
    public function ajaxGetBizPackage() {
        $orderId = intval($this->post('orderId'));
        if ($orderId > 0) {
            global $config;
            $openid = $this->getOpenId();
            // 订单总额
            $totalFee = $this->countOrderSum($orderId) * 100;
            // 随机字符串
            $nonceStr = $this->Util->createNoncestr();
            // 时间戳
            $timeStamp = strval(time());

            $pack = array(
                'appid' => APPID,
                'body' => $config->shopName,
                'mch_id' => PARTNER,
                'nonce_str' => $nonceStr,
                'notify_url' => $config->order_wxpay_notify,
                'spbill_create_ip' => $this->getIp(),
                'openid' => $openid,
                // 外部订单号
                'out_trade_no' => $config->out_trade_no_prefix . $orderId,
                'timeStamp' => $timeStamp,
                'total_fee' => $totalFee,
                'trade_type' => 'JSAPI'
            );

            $pack['sign'] = $this->Util->paySign($pack);

            $xml = $this->Util->toXML($pack);
            $this->log('请求参数:'.$xml);
            $ret = Curl::post('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
            $this->log('返回结果:'.$ret);
            $postObj = json_decode(json_encode(simplexml_load_string($ret, 'SimpleXMLElement', LIBXML_NOCDATA)));

            if (empty($postObj->prepay_id) || $postObj->return_code == "FAIL") {
                // 支付发起错误 记录到logs
                $this->log('wepay_error:' . $postObj->return_msg . ' ' . $xml);
                exit;
            }

            $packJs = array(
                'appId' => APPID,
                'timeStamp' => $timeStamp,
                'nonceStr' => $nonceStr,
                'package' => "prepay_id=" . $postObj->prepay_id,
                'signType' => 'MD5'
            );

            $JsSign = $this->Util->paySign($packJs);

            unset($packJs['timeStamp']);

            $packJs['timestamp'] = $timeStamp;

            $packJs['paySign'] = $JsSign;
            $this->echoJson($packJs);
        } else {
            $this->echoJson(['package' => '']);
        }
    }

    /**
     * 计算订单总量
     * @return <float>
     */
    private function countOrderSum($orderid) {
        return $this->Dao->select('order_amount')
                         ->from(TABLE_ORDERS)
                         ->where("`order_id` = $orderid")
                         ->getOne();
    }

    /**
     * 订单详情
     * @param type $Query
     */
    public function expressDetail($Query) {
        header('Location: ?/Uc/expressDetail/order_id=' . $Query->order_id);
    }

    /**
     * 订单取消
     * @todo a lot
     */
    public function cancelOrder() {
        $orderId   = $_POST['orderId'];
        $cancelSql = "UPDATE " . TABLE_ORDERS . " SET `status` = 'canceled' WHERE `order_id` = $orderId;";
        $rst       = $this->Db->query($cancelSql);
        # echo $cancelSql;
        echo $rst > 0 ? "1" : "0";
    }

    /**
     * 订单确认收货确认收货
     * @return boolean
     */
    public function confirmExpress() {

        // 检查权限
        if (Controller::inWechat()) {
            $openid = $this->getOpenId();
            if (empty($openid)) {
                echo 0;
                return false;
            }
        } else {
            if (!$this->Auth->checkAuth()) {
                return false;
            }
        }

        if (!$this->pPost('orderId')) {
            echo 0;
            return false;
        }

        $this->loadModel([
            'mOrder',
            'WechatSdk',
            'Express',
            'User'
        ]);

        $orderId = intval($this->pPost('orderId'));

        $orderInfo = $this->mOrder->getOrderInfo($orderId, false);

        if ($orderId > 0 && $orderInfo && $orderInfo['status'] != 'received') {

            $isExpressStaff = false;

            if (Controller::inWechat()) {
                if ($openid == $orderInfo['express_openid']) {
                    // 配送人员确认
                    $isExpressStaff = true;
                } else {
                    // 买家确认
                    if ($openid != $orderInfo['wepay_openid']) {
                        echo 0;
                        return false;
                    }
                }
            } else {
                // 后台操作
            }

            $this->Db->transtart();

            try {
                // 更新订单状态
                if ($this->mOrder->updateOrderInfo([
                    'status' => 'received',
                    'receive_time' => 'NOW()'
                ], $orderId)
                ) {
                    // 配送操作记录
                    if ($isExpressStaff) {
                        $this->Express->setExpressRecord($orderId, $openid, $orderInfo['send_time']);
                    }
                    // 是否代理订单
                    if ($orderInfo['company_com'] > 0) {
                        // 商品信息
                        $orderData = $this->mOrder->GetOrderDetail($orderId);
                        // 推广结算
                        $companyCom = $orderData['company_com'];
                        if (!empty($companyCom) && $companyCom > 0) {
                            // 代理商结算
                            $clientId   = $orderData['client_id'];
                            $orderCount = $orderData['product_count'];
                            // todo model
                            foreach ($orderData['products'] as $productId => $count) {
                                $_rst = $this->Db->query("UPDATE `" . COMPANY_SPREAD_RECORD . "` SET `turned` = `turned` + 1 WHERE `com_id` = '$companyCom' AND `product_id` = $productId;");
                                if (!$_rst) {
                                    $this->Db->query("INSERT INTO `" . COMPANY_SPREAD_RECORD . "` (`product_id`,`com_id`,`turned`) VALUES ($productId,'$companyCom',1);");
                                }
                            }
                            $companyInfo = $this->Dao->select()
                                                     ->from('companys')
                                                     ->where("id=$companyCom")
                                                     ->getOneRow();
                            // 代理回报比例
                            $percent = floatval($companyInfo['return_percent']);
                            // 代理Openid
                            $com1openid = $companyInfo['openid'];
                            // 代理UID
                            $comUid = $companyInfo['uid'];
                            //订单利润
                            $profit = floatval($orderData['order_amount'] - $orderData['original_amount']);
                            //2015年11月30日09:41 beennn修改为订单的利润的百分比
                            $comAmount = $profit * $percent;
                            // 查询二级分销
                            // 上级代理ID
                            $com2id = $this->Dao->select('client_comid')
                                                ->from('clients')
                                                ->where("client_id=$comUid")
                                                ->getOne();
                            //com2Info二级代理信息
                            $com2Info = $this->Dao->select()
                                                  ->from('companys')
                                                  ->where("id=$com2id")
                                                  ->getOneRow();
                            //往上第二级代理Uid
                            $com2utype = $com2Info['utype'];
                            //有上层代理并且代理层级达到一定条件方可享受分佣：
                            //两层:utype>3;三层：utype>6；
                            if ($com2id !== false && $com2id !== $comUid && $com2utype > 3) {
                                //com2openid
                                $com2openid = $com2Info['openId'];
                                //2015-11-30 10:51:52 beennn 修改为上一级回报为订单利润的百分比
                                $com2Income = $profit * floatval($com2Info['return_percent']);
                                // 二级回报
                                $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($com2Income, NOW(), $clientId, $orderId, '$com2id',$orderCount);");
                                Messager::sendText(WechatSdk::getServiceAccessToken(), $com2openid, date('Y-m-d') . " 您名下代理的会员总额为" . $orderData['order_amount'] . "的订单已完成，您获得 $com2Income 元收益！");
                                // 查询往上第三层分销
                                // 上级代理ID
                                $com3id = $this->Dao->select('client_comid')
                                                    ->from('clients')
                                                    ->where("client_id=$com2id")
                                                    ->getOne();
                                //com3Info三级代理信息
                                $com3Info = $this->Dao->select()
                                                      ->from('companys')
                                                      ->where("id=$com3id")
                                                      ->getOneRow();
                                //往上第三级代理Uid
                                $com3Uid = $com3Info['uid'];
                                //往上第三级代理utype
                                $com3utype = $com3Info['utype'];
                                if ($com3id !== false && $com3Uid !== $com2id && $com3utype > 6) {
                                    //com3openid
                                    $com3openid = $com3Info['openId'];
                                    //2015-11-30 10:51:52 beennn 修改为上一级回报为订单利润的百分比
                                    $com3Income = $profit * floatval($com3Info['return_percent']);
                                    // 二级回报
                                    $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($com3Income, NOW(), $clientId, $orderId, '$com3id',$orderCount);");
                                    Messager::sendText(WechatSdk::getServiceAccessToken(), $com3openid, date('Y-m-d') . " 您旗下代理的代理会员旗下的会员总额为" . $orderData['order_amount'] . "的订单已完成，您获得 $com3Income 元收益！");
                                }
                            }
                            // 第一级回报
                            $this->Db->query("INSERT INTO `company_income_record` (`amount`,`date`,`client_id`,`order_id`,`com_id`,`pcount`) VALUE ($comAmount, NOW(), $clientId, $orderId, '$companyCom',$orderCount);");
                            Messager::sendText(WechatSdk::getServiceAccessToken(), $com1openid, date('Y-m-d') . " 您名下的会员总额为" . $orderData['order_amount'] . "的订单已完成，您获得 $comAmount 元收益！");
                        }
                    }
                    // 订单赠送积分
                    $credit_order_amount = $this->getSetting('credit_order_amount');
                    if ($credit_order_amount > 0) {
                        $this->User->addCredit($orderInfo['client_id'], $orderInfo['order_amount'] * $credit_order_amount, 0, 0);
                    }
                    $this->Db->transcommit();
                    echo 1;
                }
            } catch (Exception $ex) {
                $this->Db->transrollback();
                $this->log('订单确认收货失败:' . $ex->getMessage());
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * @HttpPost only
     * 获取快递跟踪情况
     * @return <html>
     */
    public function ajaxGetExpressDetails() {
        $typeCom = $_POST["com"]; //快递公司
        $typeNu  = $_POST["nu"];  //快递单号
        $url     = "http://api.ickd.cn/?id=105049&secret=c246f9fa42e4b2c1783ef50699aa2c4d&com=$typeCom&nu=$typeNu&type=html&encode=utf8";
        //优先使用curl模式发送数据
        $res = Curl::get($url);
        echo $res;
    }

    /**
     * ajax 订单退款处理
     */
    public function orderRefund() {
        $this->loadModel('mOrder');
        $orderId = intval($this->pPost('id'));
        // 退款金额
        $amount = floatval($this->pPost('amount'));
        // 退款结果
        $ret = $this->mOrder->orderRefund($orderId, $amount);
        // 可退款金额
        $rAmount = $this->mOrder->getUnRefunded($orderId);
        // 已退款金额
        $rAmounted = $this->mOrder->getRefunded($orderId);
        if ($ret !== false) {
            if (isset($ret->return_code) && (string)$ret->return_code === 'SUCCESS') {
                // 申请已提交 进一步处理订单
                if ($rAmount == $amount || $rAmount < 0.01) {
                    // 已经全部退款
                    $this->mOrder->updateOrderStatus($orderId, 'refunded', $rAmounted + $rAmount);
                } else {
                    // 部分退款
                    $this->mOrder->updateOrderStatus($orderId, 'canceled', $rAmounted + $amount);
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    }

    /**
     * 检查限购
     * @param type $key
     * @return boolean
     */
    private function checkPromLimit($key) {
        if ($key == '') {
            return false;
        } else {
            $matchs = array();
            preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
            // product id
            $pid      = intval($matchs[1]);
            $uid      = $this->getUid();
            $limitDay = $this->Dao->select('product_prom_limitdays')
                                  ->from(TABLE_PRODUCTS)
                                  ->where("product_id = $pid")
                                  ->getOne();
            $orderS   = $this->Db->query("select order_time as `date` from orders_detail `dt`
left join orders `od` on `od`.order_id = `dt`.order_id
where `dt`.product_id = $pid and `od`.client_id = $uid
and 
(`status` = 'payed' or `status` = 'delivering' or `status` = 'received')");
            foreach ($orderS as $od) {
                if ($od['date'] > $limitDay) {
                    return false;
                }
            }
            return true;
        }
    }

    /**
     * 代付
     * @param type $Q
     */
    public function reqPay($Q) {
        if (isset($Q->id) && $Q->id > 0) {
            $orderId = intval($Q->id);

            $this->cacheId = $orderId;

            if (!$this->isCached()) {

                $this->loadModel('User');
                $this->loadModel('mOrder');
                $this->loadModel('JsSdk');

                $orderInfo = $this->mOrder->getOrderInfo($orderId);

                $orderDetail = $this->mOrder->GetOrderDetailList($orderId);

                $userInfo = $this->User->getUserInfoRaw($orderInfo['client_id']);

                $reqEd = $this->mOrder->getOrderReqAmount($orderId);

                $reqCount = $this->mOrder->getOrderReqCount($orderId);

                // 参与朋友
                $reqList = $this->mOrder->getOrderReqList($orderId);

                $signPackage = $this->JsSdk->GetSignPackage();

                $this->assign('signPackage', $signPackage);
                $this->assign('userInfo', $userInfo);
                $this->assign('orderInfo', $orderInfo);
                $this->assign('orderDetail', $orderDetail);
                $this->assign('reqed', $reqEd);
                $this->assign('reqcount', $reqCount);
                $this->assign('reqlist', $reqList);
                $this->assign('isfinish', $reqEd == $orderInfo['order_amount']);
            }

            $this->show("./views/wshop/order/reqpay.tpl");
        }
    }

    /**
     * ajax检查购物车
     */
    public function checkCart() {
        if (empty($_POST['data'])) {
            $this->echoJson(array());
        } else {
            $this->loadModel('Product');
            $this->caching = false;
            $data          = json_decode($_POST['data'], true);
            $pdList        = array();
            $matchs        = array();
            foreach ($data as $key => $count) {
                preg_match("/p(\d+)m(\d+)/is", $key, $matchs);
                $pid = intval($matchs[1]);
                if (count($this->Product->checkExt($pid)) === 0) {
                    $pdList[] = $key;
                }
            }
            $this->echoJson($pdList);
        }
    }

    /**
     * 下单成功页面
     * 提示分享，返回首页，返回个人中心选项
     */
    public function order_success($Query) {
        $orderAddress = $this->Db->getOneRow("SELECT * FROM `orders_address` WHERE `order_id` = $Query->orderid;");
        $this->assign('orderAddress', $orderAddress);
        $this->assign('title', '下单成功');
        $this->show("./views/wshop/order/order_success.tpl");
    }

    /**
     * 订单评价
     * @param type $Query
     */
    public function commentOrder($Query) {
        $orderId = intval($Query->order_id);
        $openId  = $this->getOpenId();
        if ($orderId > 0 && !empty($openId)) {
            $this->Load->model('mOrder');
            if ($this->mOrder->checkOrderBelong($openId, $orderId)) {
                $orderData = $this->mOrder->GetOrderDetail($orderId);
                $this->assign('order', $orderData);
                $this->assign('title', '订单评价');
                $this->show("./views/wshop/order/commentorder.tpl");
            }
        }
    }

    /**
     * 订单评价
     */
    public function addComment() {
        $content = $this->pPost('commentText');
        $stars   = intval($this->pPost('stars'));
        $orderId = intval($this->pPost('orderId'));
        $openId  = $this->getOpenId();
        if ($orderId > 0 && !empty($openId)) {
            $this->loadModel('mOrder');
            if ($this->mOrder->checkOrderBelong($openId, $orderId)) {
                // 检查订单归属
                if ($this->mOrder->addComment($openId, $orderId, $content, $stars)) {
                    $this->echoMsg(0);
                } else {
                    $this->echoMsg(-1);
                }
            } else {
                $this->echoMsg(-1);
            }
        } else {
            $this->echoMsg(-1);
        }
    }

    /**
     * 获取店铺设置
     */
    public function ajaxGetSettings() {
        $jsonA = array();
        // 获取快递首重，续重参数
        $datas = $this->Dao->select()
                           ->from('wshop_settings')
                           ->where("`key` IN ('exp_weight1', 'exp_weight2', 'dispatch_day_zone', 'dispatch_day')")
                           ->exec();
        foreach ($datas as $da) {
            $jsonA[$da['key']] = $da['value'];
        }
        $this->echoJson($jsonA);
    }

    /**
     * 获取运费模板
     */
    public function ajaxGetExpTemplate() {
        $arr = $this->Dao->select()
                         ->from('wshop_settings_expfee')
                         ->exec();
        $this->echoJson($arr);
    }

}
