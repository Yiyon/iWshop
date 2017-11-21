<script type="text/javascript">
    if (!+[1,]) {
        document.execCommand("Stop");
        location.href = '/html/noIe/';
    }
</script>
<!DOCTYPE html>
<html>
<head>
    <title>微店后台管理登录</title>
    <meta charset="utf-8"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
    <meta name="format-detection" content="telephone=no">
    <link href="{$docroot}favicon.ico" rel="Shortcut Icon"/>
    <link href="static/css/wshop_admin_login.css?v={$cssversion}" type="text/css" rel="Stylesheet"/>
</head>
<body class="loginBody" style="background-image:url('static/images/admin_background/{$rand}.jpg');">
<div id="login" class="clearfix">
    <div class="login-form" id="login-frame">
        <div id="loading" style="display:none;"></div>
        {if $settings.admin_setting_icon neq ''}
            <img src="{$docroot}uploads/banner/{$settings.admin_setting_icon}" height="100px" width="100px"/>
        {elseif $settings.admin_setting_icon eq ''}
            <img src="static/images/login/profle_1.png" height="100px" width="100px"/>
        {/if}
        <p> &nbsp; </p>

        <div class='login-item'>
            <div class="inputField focus">
                <input type="text" tabindex="1" value="{$smarty.cookies.admin_acc}" name="username"
                       id="pd-form-username" placeholder="请输入用户名" autocomplete="off"/>
            </div>
        </div>
        <div class='login-item'>
            <div class="inputField">
                <input type="password" tabindex="2" name="password" id="pd-form-password" placeholder="请输入密码"
                       autocomplete="off"/>
            </div>
        </div>
        <div class='login-item'>
            <a class="login-gbtn" href="javascript:;">登录</a>
        </div>
        <div id="copyrights">&COPY; 2014-2015 iWshop All rights reserved.</div>
    </div>
</div>
<script type="text/javascript" src="static/script/jquery-2.1.1.min.js?v={$cssversion}"></script>
<script type="text/javascript" src="static/script/spin.min.js?v={$cssversion}"></script>
<script type="text/javascript" src="static/script/Wdmin/login.js?v={$cssversion}"></script>
</body>
</html>