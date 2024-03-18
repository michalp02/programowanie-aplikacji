<?php
require_once dirname(__FILE__).'/../config.php';
//ochrona widoku
include _ROOT_PATH.'/app/security/check.php';

$menu = array(
	'app/calc.php' => 'Kalkulator',
	'app/inna_chroniona.php' => 'Inna chroniona strona',
	'app/security/logout.php' => 'Wyloguj się'
);
include_once _ROOT_PATH.'/app/template/header.php';
?>
<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
	<meta charset="utf-8" />
	<title>Chroniona strona</title>
</head>
<body>

<div style="width:90%; margin: 2em auto;">
	<p>To jest inna chroniona strona aplikacji internetowej</p>
</div>	

</body>
</html>