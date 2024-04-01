<?php
require_once 'Config.class.php';

$conf = new Config();

$conf->root_path = dirname(__FILE__);
$conf->server_name = 'aplikacje.pasierb.ski:443';
$conf->server_url = 'https://'.$conf->server_name;
$conf->app_root = '/4';
$conf->app_url = $conf->server_url.$conf->app_root;