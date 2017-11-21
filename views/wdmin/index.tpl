<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="renderer" content="webkit">
    <title>{$settings.shopname} - 管理后台</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <link href="{$docroot}favicon.ico" rel="Shortcut Icon"/>
    <link href="static/css/bootstrap/bootstrap.css" type="text/css" rel="Stylesheet"/>
    <link href="static/css/frame/base.css?v={$cssversion}" type="text/css" rel="Stylesheet"/>
    <link href="static/css/wshop_admin_index.css?v={$cssversion}" type="text/css" rel="Stylesheet"/>
    <script type="text/javascript" src="{$docroot}static/script/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="http://cdns.ycchen.cn/scripts/bootstrap.min.js"></script>
    <script type="text/javascript" src="{$docroot}static/script/Wdmin/wdmin.js?v={$cssversion}"></script>
</head>
<body class="wdmin-main">
<!-- 管理控制台主页面 -->
<nav class="navbar navbar-default" id="navtop">
    <div class="container-lg">
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <div class="pull-left" style="line-height: 40px;color: #fff;padding-left: 10px;">{$settings.shopname} - 管理后台
                ({$today})
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li class="topRightNavItem"><a href="?/" target="_blank">商城首页</a>
                <li class="topRightNavItem"><a href="?/Wdmin/clearCacheAll/" target="_blank">清除缓存</a></li>
                <li class="topRightNavItem">
                    <a href="?/Wdmin/logOut/"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>退出</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div id="wdmin-wrap">
    <div id="leftNav">{include file="./navs.tpl"}</div>
    <div id="rightWrapper">
        <div id="main-mid">
            <div id="iframe_loading"><img src="static/images/icon/iconfont-loading-x64-green.png"/></div>
            <div id="__subnav__"></div>
            <iframe id="right_iframe" src="" width="100%" frameborder="0"></iframe>
        </div>
    </div>
</div>
</body>
</html>