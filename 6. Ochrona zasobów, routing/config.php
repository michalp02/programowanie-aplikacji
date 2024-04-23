<?php
//konfiguracja
$conf->server_name = 'aplikacje.pasierb.ski:443';
$conf->server_url = 'https://'.$conf->server_name;
$conf->app_root = '/6';
$conf->action_root = $conf->app_root.'/ctrl.php?action=';

//wartości wygenerowane, lub na podstawie powyższych
$conf->action_url = $conf->server_url.$conf->action_root;
$conf->app_url = $conf->server_url.$conf->app_root;
$conf->root_path = dirname(__FILE__);