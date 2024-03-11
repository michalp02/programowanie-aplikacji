<?php
// KONTROLER strony kalkulatora
require_once dirname(__FILE__).'/../config.php';

$messages = [];

// Walidacja otrzymanych danych
if(!$_POST) $messages[] = 'Błąd przesyłania danych - niedozwolone wywołanie';
else {
	// sprawdzenie, czy przesłane dane nie są puste
	if (!(isset($_POST['value']) && isset($_POST['period_value']) && isset($_POST['period_type']) && isset($_POST['interest_rate']) && isset($_POST['type']))) {
		$messages[] = 'Błędne wywołanie aplikacji - niekompletne dane';
	}
}

if(empty($messages)) {
	// sprawdzenie, czy przesłane dane nie są puste i poprawnego typu w przypadku opcji wyboru
	if ($_POST['value'] == "") $messages[] = 'Nie podano kwoty kredytu';
	if ($_POST['period_value'] == "") $messages[] = 'Nie podano okresu kredytowania';
	if ($_POST['interest_rate'] == "") $messages[] = 'Nie podano oprocentowania';

	if(!in_array($_POST['period_type'], ['months', 'years'])) $messages[] = 'Błędny typ okresu kredytowania';
	if(!in_array($_POST['type'], ['constant', 'decreasing'])) $messages[] = 'Błędny typ rat';
	
}

if(empty($messages)) {
	// sprawdzenie, czy przesłane dane są liczbami
	if (!is_numeric($_POST['value'])) $messages[] = 'Kwota kredytu nie jest liczbą';
	else if($_POST['value'] <= 0) $messages[] = 'Kwota kredytu musi być liczbą dodatnią';
	if (!is_numeric($_POST['period_value'])) $messages[] = 'Okres kredytowania nie jest liczbą';
	else if($_POST['period_value'] <= 0) $messages[] = 'Okres kredytowania musi być liczbą dodatnią';
	if (!is_numeric($_POST['interest_rate'])) $messages[] = 'Oprocentowanie nie jest liczbą';
	else if($_POST['interest_rate'] <= 0) $messages[] = 'Oprocentowanie musi być liczbą dodatnią';
}

if(empty($messages)) {
	// konwersja przesłanych danych na odpowiedni typ
	$credit_value = floatval($_POST['value']);
	$credit_period = intval($_POST['period_value']);
	$interest_rate = floatval($_POST['interest_rate']);
	$period_type = $_POST['period_type'];
	$type = $_POST['type'];

	// obliczenie oprocentowania miesięcznego
	$monthly_interest_rate = $interest_rate / (12 * 100);
	
	// obliczenie liczby rat, jeśli okres kredytowania jest podany w latach (domyślnie w miesiącach)
	if ($period_type == 'years') $credit_period *= 12;
	
	// obliczenie wartości raty lub rat w zależności od wybranej opcji
	if ($type == 'constant') {
		$rate = ($credit_value * $monthly_interest_rate) / (1 - pow((1 + $monthly_interest_rate), -$credit_period));
	} else {
		$rate = [];
		$credit_value_monthly = $credit_value / $credit_period;
		for ($i = 0; $i < $credit_period; $i++) {
			$rate[$i] = $credit_value_monthly + ($credit_value_monthly * ($credit_period - $i - 1) * $monthly_interest_rate);
		}
	}

	// obliczenie odsetek
	$intrest = 0;
	if ($type == 'constant') {
		$intrest = $rate * $credit_period - $credit_value;
	} else {
		foreach ($rate as $r) {
			$intrest += $r;
		}
		$intrest -= $credit_value;
	}

	// obliczenie kosztu kredytu
	$cost = $credit_value + $intrest;
}


include 'calc_view.php';