#iWshop<微信开源商城>

iWshop是一个开源的微信商城。为了保证轻量级，使用了作者自主开发的mvc框架。 

LightMvc现已分离 <a href="https://git.oschina.net/koodo/LightMvc">https://git.oschina.net/koodo/LightMvc</a> 

>iWshop 交流群：470442221

>iWshop 交流社区：<a href="http://asw.iwshop.cn/" target="_blank">http://asw.iwshop.cn/</a> 

>iWshop 类文档：<a href="http://docs.ycchen.cc/iwshop/package-Wshop.html">http://docs.ycchen.cc/iwshop/package-Wshop.html</a> 

>iWshop 安装教程：<a href="http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md">http://git.oschina.net/koodo/iWshop/blob/dev/html/docs/install.md</a>

>安装教程图文教程及注意事项(JerryJee提供的教程)：<a target="_blank" href="http://www.jiloc.com/2661.html">http://www.jiloc.com/2661.html</a>

>微信公众平台三方接入技术问题总结,提问前必看(JerryJee提供的教程)：<a target="_blank" href="http://www.jiloc.com/2640.html">http://www.jiloc.com/2640.html</a>

>添加对wordpres的文章整合，可实现微信关键词对wordpress的内容匹配：<a target="_blank" href="http://www.jiloc.com/38430.html">http://www.jiloc.com/38430.html</a>


##iwshop 配置说明

####一、根目录修改说明


如果是服务器根目录，必须修改为 “/”


如果是服务器子目录，必须修改为”/subroot/” 等有左右斜杠的格式


修改文件：

/config/config.php ($config->shoproot = “目录”)

####二、手动部署说明

- 服务器配置过程略。

- 创建/install/install.lock

- 导入/install/iWshop.sql

- 后台地址（以域名www.iwshop.cn为例）为：http://www.iwshop.cn/?/Wdmin/login/

- 微信消息接口地址：http://www.iwshop.cn/wechat/ (切莫忘了最后的 / )

####三、目录权限说明

执行一下install里的shell文件

    ./install/dirmod.sh

请确保您的php配置中magic_quotes_gpc为Off，否则一些功能将失效

####四、配置文件config.php说明

初始配置文件为/config/config_sample.php

请编辑config_sample.php文件并且重命名为config.php

####五、运行环境要求
 
**MySQL 5.5.3+ (utf8mb4编码用于保存带有emoji表情的微信用户昵称)**

    PHP5.4+

    PHP扩展：php_mysql php_curl php_pdo_mysql php_gd2


####六、演示项目

<img src="http://down.ycchen.cn/iwshop_release/images/qrcode3.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />
<img src="http://down.ycchen.cn/iwshop_release/images/qrcode2.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" /> 
<img src="http://down.ycchen.cn/iwshop_release/images/qrcode1.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" /> 
<img src="http://down.ycchen.cn/iwshop_release/images/qrcode4.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />
<img src="http://down.ycchen.cn/iwshop_release/images/qrcode5.jpg" height="140" width="140" style="border:1px solid #eee;margin-right:10px;" />

## 相关下载

><a href="http://download-iwshop.oss-cn-shenzhen.aliyuncs.com/xampp-win32-5.6.12-0-VC11-installer.zip" target="_blank">xampp-win32-5.6.12-0-VC11(Xampp集成安装包Windows)</a> 