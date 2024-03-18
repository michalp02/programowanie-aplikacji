<?php require_once dirname(__FILE__) .'/../config.php';

// Import szablonu nagłówka
$menu = array(
	'app/calc.php' => 'Kalkulator',
	'app/inna_chroniona.php' => 'Inna chroniona strona',
	'app/security/logout.php' => 'Wyloguj się'
);
include_once _ROOT_PATH.'/app/template/header.php';

?>

<form action="<?=_APP_URL;?>/app/calc.php" method="post">
	<label for="id_value">Kwota kredytu: </label>
	<input id="id_value" type="text" name="value" value="<?=@$credit_value;?>" /> PLN<br />
	<label for="id_period">Okres kredytowania: </label>
	<input id="id_period" type="text" name="period_value" value="<?=@$credit_period; ?>" />
	<select name="period_type">
		<option value="months">miesiące</option>
		<option value="years">lata</option>
	</select><br />
	<label for="id_intrest">Oprocentowanie nominalne (roczne): </label>
	<input id="id_intrest" type="text" name="interest_rate" value="<?=@$interest_rate; ?>" />%<br />
	<label for="id_type">Forma rat: </label>
	<select name="type">
		<option value="constant"<?=(@$type == 'constant') ? ' selected' : ' ';?>>Raty stałe</option>
		<option value="decreasing"<?=(@$type == 'decreasing') ? ' selected' : ' ';?>>Raty malejące</option>
	</select><br />
	<input type="submit" value="Oblicz raty" />
</form>	

<?php
//wyświeltenie listy błędów, jeśli istnieją
if (isset($messages)) {
	if (count ($messages) > 0) {
		echo '<ol class="error">';
		foreach ( $messages as $key => $msg ) {
			echo '<li>'.$msg.'</li>';
		}
		echo '</ol>';
	}
}
?>

<?php if (isset($rate)){ ?>
<div class="result">
<?php 

if ($type == 'constant') {
	echo 'Miesięczna rata kredytu wynosi: '.round($rate, 2).' PLN<br />';
} else {
	echo 'Miesięczne raty kredytu wynoszą: <ul>';
	foreach ($rate as $key => $r) {
		echo '<li>Rata '.($key+1).' wynosi: '.round($r, 2).' PLN</li>';
	}
	echo '</ul>';
}

echo 'Całkowity koszt kredytu wynosi: '.round($cost).' PLN<br />';
echo 'W tym odsetki: '.round($intrest).' PLN';

?>
</div>
<?php } ?>

</body>
</html>