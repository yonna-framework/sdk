<?php

namespace Aliyun\Core;

use Aliyun\Core\Regions\EndpointConfig;

//config http proxy
if(!defined('ENABLE_HTTP_PROXY')) define('ENABLE_HTTP_PROXY', FALSE);
if(!defined('HTTP_PROXY_IP')) define('HTTP_PROXY_IP', '127.0.0.1');
if(!defined('HTTP_PROXY_PORT')) define('HTTP_PROXY_PORT', '8888');


class Config
{
    private static $loaded = false;
    public static function load(){
        if(self::$loaded) {
            return;
        }
        EndpointConfig::load();
        self::$loaded = true;
    }
}