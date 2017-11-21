<?php

/**
 * @description Holp You Do Good But Not Evil
 * @copyright   Copyright 2014-2015 <ycchen@iwshop.cn>
 * @license     LGPL (http://www.gnu.org/licenses/lgpl.html)
 * @author      Chenyong Cai <ycchen@iwshop.cn>
 * @package     Wshop
 * @link        http://www.iwshop.cn
 */
class wUser extends Controller {

    /**
     * 权限检查
     * @param type $ControllerName
     * @param type $Action
     * @param type $QueryString
     */
    public function __construct($ControllerName, $Action, $QueryString) {
        parent::__construct($ControllerName, $Action, $QueryString);
        if (!$this->Auth->checkAuth()) {
            $this->redirect('?/Wdmin/logOut');
        } else {
            $this->Db->cache = false;
        }
    }

    /**
     * ajax获取用户列表
     */
    public function getUserList($Query) {
        $this->loadModel('User');
        $gid      = $this->pGet('gid');
        $phone    = $this->pGet('phone');
        $name     = urldecode($this->pGet('uname'));
        $page     = Util::digitDefault($this->pGet('page'), 0);
        $pagesize = Util::digitDefault($this->pGet('pagesize'), 30);

        $WHERE            = [];
        $WHERE['deleted'] = 0;

        $this->Dao->select('')
                  ->count()
                  ->from(TABLE_USER)
                  ->where($WHERE);

        !empty($gid) && $WHERE['client_level'] = $gid;

        !empty($phone) && $this->Dao->aw("client_phone LIKE '%$phone%'");

        !empty($name) && $this->Dao->aw("client_name LIKE '%$name%'");

        $count = $this->Dao->getOne();

        $this->Dao->select()
                  ->from(TABLE_USER)
                  ->where($WHERE);

        !empty($gid) && $WHERE['client_level'] = $gid;

        !empty($phone) && $this->Dao->aw("client_phone LIKE '%$phone%'");

        !empty($name) && $this->Dao->aw("client_name LIKE '%$name%'");

        $list = $this->Dao->orderby('client_id')
                          ->desc()
                          ->limit($pagesize * $page, $pagesize)
                          ->exec();

        $this->echoJson([
            'total' => intval($count),
            'list' => $list
        ]);
    }

    /**
     * 获取用户数量
     */
    public function getUserCount() {
        $count = $this->Dao->select('')
                           ->count()
                           ->from(TABLE_USER)
                           ->where('`deleted` = 0')
                           ->getOne();
        $this->echoMsg(0, $count);
    }

    /**
     * 获取用户分组
     */
    public function getUserLevel() {
        $this->loadModel('UserLevel');
        $list = $this->UserLevel->getList();
        $this->echoMsg(0, $list);
    }

    /**
     * 获取用户分组详情
     */
    public function getUserLevelInfo() {
        $id = $this->pGet('id');
        $this->loadModel('UserLevel');
        $info = $this->UserLevel->get($id);
        $this->echoMsg(0, $info);
    }

    /**
     * 编辑用户分组
     */
    public function alterUserLevelInfo() {
        $this->loadModel('UserLevel');
        $id = intval($this->post('id'));
        if ($id >= 0) {
            $ret = $this->UserLevel->addLevel($id, $this->post('level_name'), $this->post('level_credit'), $this->post('level_discount'), $this->post('level_credit_feed'), 1);
        } else {
            $ret = $this->UserLevel->addLevel(false, $this->post('level_name'), $this->post('level_credit'), $this->post('level_discount'), $this->post('level_credit_feed'), 1);
        }
        $this->echoMsg($ret ? 0 : -1);
    }

    /**
     * 删除用户分组
     */
    public function deleteLevel() {
        $this->loadModel('UserLevel');
        $id = intval($this->post('id'));
        try {
            $this->UserLevel->delete($id);
            $this->echoMsg(0);
        } catch (Exception $ex) {
            $this->echoMsg(-1, $ex->getMessage());
        }
    }

    /**
     * ajax删除用户
     */
    public function deleteUser() {
        $id = intval($this->post('id'));
        if ($id > 0) {
            $sql = "UPDATE `clients` SET `deleted` = 1 WHERE `client_id` = $id";
            if ($this->Db->query($sql)) {
                $this->echoSuccess();
            } else {
                $this->echoFail();
            }
        }
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo() {
        $id = intval($this->pGet('id'));
        if ($id > 0) {
            $ret = $this->Dao->select()
                             ->from(TABLE_USER)
                             ->where(['client_id' => $id])
                             ->getOneRow();
            $this->echoMsg(0, $ret);
        } else {
            $this->echoFail();
        }
    }


    /**
     * ajax编辑用户 | 添加用户
     */
    public function alterUser() {
        $clientId = $this->pPost('client_id');
        $data     = $this->post();
        if ($clientId == 0) {
            $field                        = array();
            $values                       = array();
            $data['client_joindate']      = date('Y-m-d');
            $data['client_wechat_openid'] = hash('md4', uniqid() . time());
            foreach ($data as $key => $value) {
                $field[]  = $key;
                $values[] = $value;
            }
            if ($this->Dao->insert(TABLE_USER, implode(',', $field))
                          ->values($values)
                          ->exec()
            ) {
                $this->echoSuccess();
            } else {
                $this->log('新增会员失败,SQL:' . $this->Dao->getSQL());
                $this->echoFail();
            }
        } else {
            // 更新用户信息
            if ($this->Dao->update(TABLE_USER)
                          ->set($data)
                          ->where(['client_id' => $clientId])
                          ->exec()
            ) {
                $this->echoSuccess();
            } else {
                $this->echoFail();
            }
        }
    }

}
