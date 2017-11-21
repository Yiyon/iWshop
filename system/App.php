<?php

if (!defined('APP_PATH')) {
    exit(0);
}

/**
 * Wshop main Class
 */
class App {

    const ROUTER_HASH_LIMIT = 30;

    // Singleton instance
    protected static $_instance = NULL;

    // Controller instance
    public $Controller = NULL;

    /**
     * get App Class instance
     * @return object $_instance
     */
    public static function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new App();
        }
        return self::$_instance;
    }

    /**
     * 打包请求参数
     * @param type $QueryString
     * @return Object QueryObject
     */
    private function packQueryString($QueryString) {
        if (!empty($QueryString)) {
            $QueryObject = new stdClass();
            $QueryString = explode('&', $QueryString);
            foreach ($QueryString as $r) {
                $r = explode('=', $r);
                if (count($r) == 2) {
                    $key               = $r[0];
                    $QueryObject->$key = $r[1];
                }
            }
            return $QueryObject;
        } else {
            return NULL;
        }
    }

    /**
     * action转换
     * @param type $config
     * @param type $Controller
     * @param type $Action
     * @return type
     */
    private function getAction($config, $Controller, $Action) {
        if ($Action == "") {
            if (array_key_exists($Controller, $config->defaultAction)) {
                return $config->defaultAction[$Controller];
            } else {
                return 'index';
            }
        } else {
            // Action&querystring
            if (strstr($Action, "&")) {
                return substr($Action, 0, strpos($Action, "&"));
            }
            return $Action;
        }
    }

    /**
     * 处理请求
     * @global type $config
     * parse http request
     */
    public function parseRequest() {

        global $config;

        // 解析URI
        $URI = explode('/', $_SERVER["QUERY_STRING"]);

        // 得出相对根目录
        $config->shoproot = preg_replace('/\w+\.php/i', '', htmlentities($_SERVER['PHP_SELF']));
        $config->shoproot = str_replace('//', '/', $config->shoproot);

        // 解析路由参数
        $RouteParam = $this->getRouterParams($URI, $config);

        try {
            if (class_exists($RouteParam->controller)) {
                // 实例化控制器
                $this->Controller = new $RouteParam->controller($RouteParam->controller, $RouteParam->action, $RouteParam->queryString);
                if (method_exists($this->Controller, $RouteParam->action)) {
                    // 注册当前URI
                    $this->Controller->uri = $URI = preg_replace('/\/\?\/$/', '', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                    // 回调根目录
                    $this->Controller->root = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" . $_SERVER['HTTP_HOST'] . $config->shoproot;
                    // 调用对应方法
                    $this->Controller->{$RouteParam->action}($this->packQueryString($RouteParam->queryString));
                } else {
                    header('HTTP/1.1 404 Not Found');
                    throw new Exception("访问错误：方法不存在 {$RouteParam->controller}->{$RouteParam->action}() 不存在");
                }
            } else {
                header('HTTP/1.1 404 Not Found');
                throw new Exception("访问错误：控制器 {$RouteParam->controller} 不存在");
            }
        } catch (Exception $ex) {
            Util::log($ex->getMessage());
        }

    }

    /**
     * 获取路由处理参数
     * @global object $config
     * @param string $URI
     * @return array
     */
    private function getRouterParams(&$URI) {
        $hashKey    = hash('md4', $_SERVER["REQUEST_URI"]);
        $RouteParam = null;
        // 缓存文件
        $cacheFile = APP_PATH . "tmp/routerHash.cache.php";
        // 缓存文件检查
        if (is_file($cacheFile) && is_readable($cacheFile)) {
            $pathHash = include $cacheFile;
            if (sizeof($pathHash) > 30) {
                $pathHash = array();
            }
        } else {
            $pathHash = array();
        }

        // 查找缓存文件
        if (sizeof($pathHash) !== 0) {
            // 缓存不为空
            if (isset($pathHash[$hashKey]) && is_array($pathHash[$hashKey])) {
                return (object)$pathHash[$hashKey];
            } else {
                $RouteParam = $this->genRouterParams($URI);
            }
        } else {
            $RouteParam = $this->genRouterParams($URI);
        }

        $pathHash[$hashKey] = (array)$RouteParam;

        // 写回缓存
        if (is_array($pathHash)) {
            if (!is_file($cacheFile)) {
                touch($cacheFile);
                chmod($cacheFile, 0777);
            }
            $fp = fopen($cacheFile, 'w');
            fwrite($fp, '<?php return ' . var_export($pathHash, true) . ';?>');
        }

        //        if (sizeof($pathHash) > ROUTER_HASH_LIMIT) {
        //             只保留20个数据
        //             $pathHash = array_slice($pathHash, -1, ROUTER_HASH_LIMIT - 1);
        //        }

        return $RouteParam;
    }

    /**
     * 生成路由处理参数
     * @global object $config
     * @param string $URI
     * @return \stdClass
     */
    final private function genRouterParams(&$URI) {
        global $config;
        $RouteParam              = new stdClass();
        $RouteParam->queryString = "";
        if (isset($GLOBALS['controller'])) {
            $RouteParam->controller = $GLOBALS['controller'];
            $RouteParam->action     = $this->getAction($config, $RouteParam->controller, $GLOBALS['action']);
        } else {
            if ($URI[1] == "" || strpos($URI[1], '=')) {
                $RouteParam->controller = $config->default_controller;
                if (strpos($URI[1], '=')) {
                    $RouteParam->queryString = $URI[1];
                }
            } else {
                $RouteParam->controller  = $URI[1];
                $RouteParam->queryString = isset($URI[3]) ? $URI[3] : '';
            }
            $RouteParam->action = $this->getAction($config, $RouteParam->controller, isset($URI[2]) && preg_match("/\w+\_?\w?/is", $URI[2]) ? $URI[2] : "");
        }

        return $RouteParam;
    }

}
