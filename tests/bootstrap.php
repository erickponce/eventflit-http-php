<?php

$dir = dirname(__FILE__);
$config_path = $dir.'/config.php';
if (file_exists($config_path) === true) {
    require_once $config_path;
} else {
    define('EVENTFLITAPP_AUTHKEY', getenv('EVENTFLITAPP_AUTHKEY'));
    define('EVENTFLITAPP_SECRET', getenv('EVENTFLITAPP_SECRET'));
    define('EVENTFLITAPP_APPID', getenv('EVENTFLITAPP_APPID'));

    define('EVENTFLITAPP_HOST', 'http://service.eventflit.com');
}
