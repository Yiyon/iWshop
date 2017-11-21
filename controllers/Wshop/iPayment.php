<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * 支付处理控制器
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class iPayment extends Controller {

    // 支付回调页面
    public function payment_notify() {
        //解决$GLOBAL限制导致无法获取xml数据
        $sourceStr = file_get_contents('php://input');
        // 读取数据
        $postObj = simplexml_load_string($sourceStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$postObj) {
            $this->log("支付回调处理失败，数据包解析失败");
        } else {
            global $config;
            // orderid
            $orderId = str_replace($config->out_trade_no_prefix, '', $postObj->out_trade_no);
            // 微信交易单号
            $transaction_id = $postObj->transaction_id;
            if (!empty($transaction_id)) {
                try {
                    $this->loadModel([
                        'mOrder',
                        'User'
                    ]);
                    // 获取订单信息
                    $orderInfo = $this->mOrder->getOrderInfo($orderId, false);
                    if ($orderInfo && $orderInfo['status'] != 'payed' && empty($orderInfo['wepay_serial'])) {
                        if ($this->Dao->update(TABLE_ORDERS)
                                      ->set([
                                          'wepay_serial' => $transaction_id,
                                          'status' => 'payed',
                                          'wepay_openid' => $postObj->openid
                                      ])
                                      ->where(["order_id" => $orderId])
                                      ->exec()
                        ) {
                            // 商户订单通知
                            @$this->mOrder->comNewOrderNotify($orderId);
                            // 用户订单通知 模板消息
                            @$this->mOrder->userNewOrderNotify($orderId, $postObj->openid);
                            // 导入订单数据到个人信息
                            @$this->User->importFromOrderAddress($orderId);
                            // 积分结算
                            @$this->mOrder->creditFinalEstimate($orderId);
                            // 减库存
                            @$this->mOrder->cutInstock($orderId);
                            // 返回success
                            echo "<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>";
                        } else {
                            $this->log("支付回调处理失败:" . $sourceStr );
                        }
                    }
                } catch (Exception $ex) {
                    $this->log($ex->getMessage());
                }
            }
        }
    }
}