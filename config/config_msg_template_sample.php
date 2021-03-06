<?php

// 微信模板消息配置
// 相关文档：https://mp.weixin.qq.com/advanced/tmplmsg?action=faq&token=991825086&lang=zh_CN

return array(
    // 下单成功
    'pay_success' => array(
        // 模板ID
        'tpl_id' => '',
        // 模板顶部文字key，默认first
        'first_key' => 'first',
        // 订单序列号key
        'serial_key' => '',
        // 商品名称key
        'product_name_key' => '',
        // 商品数量key
        'product_count_key' => '',
        // 订单金额key
        'order_amount_key' => '',
        // 模板底部文字key
        'remark_key' => 'remark'
    ),
    // 订单取消
    'cancel_order' => array(
        // 模板ID
        'tpl_id' => '',
        // 模板顶部文字key，默认first
        'first_key' => 'first',
        // 订单序列号key
        'serial_key' => '',
        // 商品名称key
        'product_name_key' => '',
        // 商品数量key
        'product_count_key' => '',
        // 订单金额key
        'order_amount_key' => '',
        // 模板底部文字key
        'remark_key' => 'remark'
    ),
    // 快递发货
    'exp_notify' => array(
        // 模板ID
        'tpl_id' => '',
        // 模板顶部文字key，默认first
        'first_key' => 'first',
        // 订单序列号key
        'serial_key' => '',
        // 快递公司名称key
        'expname' => '',
        // 快递单号key
        'expcode' => '',
        // 模板底部文字key
        'remark_key' => 'remark'
    ),
    // 快递发货提醒
    'exp_staff_notify' => array(
        // 模板ID
        'tpl_id' => '',
        // 模板顶部文字key，默认first
        'first_key' => 'first',
        // 订单序列号key
        'serial_key' => '',
        // 快递公司名称key
        'expname' => '',
        // 快递单号key
        'expcode' => '',
        // 模板底部文字key
        'remark_key' => 'remark'
    ),
    // 代理审核通过通知
    'company_reg_notify' => array(
        // 模板ID
        'tpl_id' => '',
        // 模板顶部文字key，默认first
        'first_key' => 'first',
        // 申请人名称
        'username' => 'keyword1',
        // 审核结果
        'result' => 'keyword2',
        // 模板底部文字key
        'remark_key' => 'remark'
    )

);

