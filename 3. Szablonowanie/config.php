<?php
define('_SERVER_NAME', $_SERVER['SERVER_NAME']);
define('_SERVER_URL', 'https://'._SERVER_NAME);
define('_APP_ROOT', '/3');
define('_APP_URL', _SERVER_URL._APP_ROOT);
define("_ROOT_PATH", dirname(__FILE__));
?>