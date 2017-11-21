<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html;charset=utf-8"/>
    <title>代理申请 - {$settings.shopname}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no"/>
    <link href="static/css/wshop/company_request.css?v={$cssversion}" type="text/css" rel="Stylesheet"/>
</head>
<body style="background: #fff;">

<section style="padding: 18px;">
    <!-- 代理协议 -->
    {include file="../../../html/agent_agreement.html"}
</section>

<div id="button-wrap">
    <a class="button green" id="confirm" href="?/Company/companyReg/">同意协议 马上申请</a>
</div>

{include file="../../global/copyright.tpl"}

<script data-main="static/script/Wshop/shop_companyreg.js?v={$cssversion}" src="static/script/require.min.js"></script>

</body>
</html>