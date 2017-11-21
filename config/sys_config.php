<?php

/**
 * Conifg Object
 */
$config = new stdClass();

$config->orderStatus = array(
    'unpay' => '未支付',
    'payed' => '已支付',
    'canceled' => '已取消',
    'received' => '已完成',
    'delivering' => '快递在途',
    'closed' => '已关闭',
    'refunded' => '已退款',
    'reqing' => '代付'
);

/**
 * 系统初始化自动加载模块
 */
$config->preload = array(
      'Smarty' // Smarty模板引擎
    , 'Db'     // 数据库连接
    , 'Util'
    , 'Dao'
    , 'Banners'
    , 'Load'
    , 'Auth'
);

/**
 * 控制器默认方法，最终默认为index
 */
$config->defaultAction = array(
    'ViewProduct' => 'view_list',
    'Uc' => 'home'
);

/**
 * 默认视图文件目录
 */
$config->tpl_dir = "views";

/**
 * Smarty配置
 */
$config->Smarty = [
    'cache_dir' => APP_PATH . 'tmp/tpl_cache/',
    'compile_dir' => APP_PATH . 'tmp/tpl_compile/',
    'view_dir' => APP_PATH . 'views/'
];

/**
 * 默认Controller
 */
$config->default_controller = "Index";

/**
 * 模块加载自动查找路径
 */
$config->classRoot = array(
    'controllers/', 'models/', 'system/', 'lib/', 'lib/Smarty/', 'lib/Smarty/plugins/', 'lib/Smarty/sysplugins/',
    'lib/barcodegen/', 'lib/phpqrcode/', 'lib/PHPExcel/Classes/PHPExcel/', 'controllers/Wdmin/', 'controllers/Wshop/',
    'models/Interface/', 'controllers/Interface/'
);

/**
 * config -> shoproot
 * 微信支付发起路径
 */
$config->wxpayroot = 'wxpay.php';

/**
 * config -> admin_salt
 * 管理后台加密盐
 */
$config->admin_salt = '1akjx99k';

/**
 * config -> admin_salt
 * 微店加密盐
 */
$config->wshop_salt = 'a_asd(x';