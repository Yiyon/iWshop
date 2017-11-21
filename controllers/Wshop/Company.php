<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * 代理中心
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class Company extends Controller {

    /**
     * Company constructor.
     * @param $ControllerName
     * @param $Action
     * @param $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        $this->loadModel('mCompany');
    }

    /**
     * 代理申请页面
     */
    public function companyRequest() {
        $uid = $this->getUid();
        if ($uid > 0) {
            $this->show('./views/wshop/company/companyrequest.tpl');
        }
    }

    /**
     * 代理申请页面
     */
    public function companyReg() {
        $this->assign('title', '代理申请');
        $this->assign('openid', $this->getOpenId());
        $this->show('./views/wshop/company/companyreg.tpl');
    }

    /**
     * 添加一个com推广记录
     */
    public function addComSpread() {
        // 这个代码写的烂
        $productId = intval($this->post('productId'));
        $comId     = $this->post('comId');
        $Uin       = $this->Db->query("SELECT COUNT(`rid`) AS `count` FROM " . COMPANY_SPREAD_RECORD . "WHERE `product_id` = $productId AND `com_id` = '$comId';");
        // 生成记录
        if ($Uin[0]['count'] == 0) {
            $SQL = "REPLACE INTO " . COMPANY_SPREAD_RECORD . " (`product_id`,`com_id`) VALUES ($productId,'$comId');";
            echo $this->Db->query($SQL);
        } else {
            // 已经有记录了
            echo 0;
        }
    }

    /**
     * 添加微代理
     */
    public function addCompany() {
        $SQL = sprintf("INSERT INTO `companys` " . "(uid,name,phone,email,person_id,openid,join_date,return_percent,utype) " . "VALUES ('%s','%s','%s','%s','%s','%s',NOW(),'0.10',2);", $this->pCookie('uid'), $this->pPost('name'), $this->pPost('phone'), $this->pPost('email'), $this->pPost('ids'), $this->pPost('openid'));
        $ret = $this->Db->query($SQL);
        echo $ret ? 1 : 0;
    }

    /**
     * 代理二维码页面
     */
    public function companyQrcode() {
        $this->loadModel('WechatSdk');
        $this->loadModel('mCompany');
        $openid = $this->getOpenId();
        $comId  = $this->mCompany->getCompanyIdByOpenId($openid);
        if ($comId > 0) {
            $stoken      = WechatSdk::getServiceAccessToken();
            $qrcodeImage = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $comId));
            echo $qrcodeImage;
        }
    }

    /**
     * 获取代理二维码
     * @param type $Query
     */
    public function ajaxGetCompanyQrcode($Query) {
        if (is_numeric($Query->id)) {
            $this->loadModel('WechatSdk');
            $this->Smarty->caching = false;
            $id                    = intval($Query->id);
            $stoken                = WechatSdk::getServiceAccessToken();
            $qrcodeImage           = WechatSdk::getCQrcodeImage(WechatSdk::getCQrcodeTicket($stoken, $id));
            $this->Smarty->assign('id', $id);
            $this->Smarty->assign('qrcode', $qrcodeImage);
            $this->show("wdminpage/company/ajax_qrcode.tpl");
        }
    }

    /**
     * 代理资料修改
     * @param type $length
     *
     */
    public function companyInfoEdit() {
        $ret = $this->Dao->update(TABLE_COMPANYS)
                         ->set(array(
                             'phone' => $this->pPost('phone'),
                             'name' => $this->pPost('name'),
                             'email' => $this->pPost('email'),
                             'person_id' => $this->pPost('ids'),
                             'bank_name' => $this->pPost('bname'),
                             'bank_account' => $this->pPost('bacc'),
                             'alipay' => $this->pPost('aliacc')
                         ))
                         ->where('uid', $this->pCookie('uid'))
                         ->exec();
        echo $ret ? 1 : 0;
    }

    /**
     * 生成代理密码
     * @param type $length
     * @return string
     */
    public function make_password($length = 8) {
        // 密码字符集，可任意添加你需要的字符
        $chars = array(
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9'
        );

        // 在 $chars 中随机取 $length 个数组元素键名
        $keys = array_rand($chars, $length);

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 将 $length 个数组元素连接成字符串
            $password .= $chars[$keys[$i]];
        }

        return $password;
    }

    /**
     * 代理结算
     */
    public function payCompanyBills() {
        if (intval($this->pPost('id')) > 0) {
            echo $this->mCompany->payCompanyBills($this->pPost('id'));
        } else {
            echo 0;
        }
    }

}
