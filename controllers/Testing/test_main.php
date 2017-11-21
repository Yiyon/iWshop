<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/10
 * Time: 23:35
 */

include './helpers/Curl.php';

/**
 * @param $url
 * @param bool $echo
 */
function doHttp($url, $echo = true){
    if($echo){
        var_dump(Curl::get($url));
    }
}