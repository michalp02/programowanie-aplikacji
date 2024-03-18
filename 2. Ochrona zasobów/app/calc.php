<?php
// KONTROLER strony kalkulatora
require_once dirname(__FILE__).'/../config.php';

//ochrona widoku
include _ROOT_PATH.'/app/security/check.php';

$messages = [];


// Pobranie parametrów
function getParams(&$credit_value, &$credit_period, &$interest_rate, &$period_type, &$type) {
    global $messages;
    if($_POST && !(isset($_POST['value']) && isset($_POST['period_value']) && isset($_POST['period_type']) && isset($_POST['interest_rate']) && isset($_POST['type']))) {
        $messages[] = 'Błędne wywołanie aplikacji - niekompletne dane';
    }
	$credit_value = $_POST['value'] ??= null;
	$credit_period = $_POST['period_value'] ??= null;
	$interest_rate = $_POST['interest_rate'] ??= null;
	$period_type = $_POST['period_type'] ??= null;
	$type = $_POST['type'] ??= null;
}

// Walidacja otrzymanych danych
function validate(&$credit_value, &$credit_period, &$interest_rate, &$period_type, &$type) {
	if(is_null($credit_value) || is_null($credit_period) || is_null($interest_rate) || is_null($period_type) || is_null($type)) return false;
    global $messages;
	global $role;
	// Sprawdzenie, czy przesłane dane nie są puste
	if ($credit_value == "") $messages[] = 'Nie podano kwoty kredytu';
	if ($credit_period == "") $messages[] = 'Nie podano okresu kredytowania';
	if ($interest_rate == "") $messages[] = 'Nie podano oprocentowania';
    if(count($messages) != 0) return false;

    // Sprawdzenie, czy przesłane dane są poprawnego typu w przypadku opcji wyboru
	if(!in_array($period_type, ['months', 'years'])) $messages[] = 'Błędny typ okresu kredytowania';
	if(!in_array($type, ['constant', 'decreasing'])) $messages[] = 'Błędny typ rat';
    if(count($messages) != 0) return false;

	// Sprawdzenie, czy przesłane dane są liczbami
	if (!is_numeric($credit_value)) $messages[] = 'Kwota kredytu nie jest liczbą';
	if (!is_numeric($credit_period)) $messages[] = 'Okres kredytowania nie jest liczbą';
	if (!is_numeric($interest_rate)) $messages[] = 'Oprocentowanie nie jest liczbą';
    if(count($messages) != 0) return false;
    
    // Sprawdzenie, czy liczby spełniają wymagania
	if($credit_value <= 0) $messages[] = 'Kwota kredytu musi być liczbą dodatnią';
	if($credit_period <= 0) $messages[] = 'Okres kredytowania musi być liczbą dodatnią';
	if($interest_rate <= 0) $messages[] = 'Oprocentowanie musi być liczbą dodatnią';

	// Jeśli kwota kredytu jest większa niż 100.000 zł, to sprawdź, czy użytkownik jest adminem
	if($credit_value > 100000 && $role != 'admin') $messages[] = 'Kwota kredytu przekracza 100.000 zł - wymagane uprawnienia administratora';

    if(count($messages) != 0) return false;

    return true;
}

function process() {
    global $credit_value;
    global $credit_value;
    global $credit_period;
    global $interest_rate;
    global $period_type;
    global $type;
    global $intrest;
    global $cost;
    global $rate;

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



// Kontroler
getParams($credit_value, $credit_period, $interest_rate, $period_type, $type);
if (validate($credit_value, $credit_period, $interest_rate, $period_type, $type))
	process();


include 'calc_view.php';