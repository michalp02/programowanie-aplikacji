<?php
// KONTROLER strony kalkulatora
require_once dirname(__FILE__).'/../config.php';
//załaduj Smarty
require_once _ROOT_PATH.'/lib/smarty/Smarty.class.php';

// Pobranie parametrów
function getParams(&$form) {
	$form['credit_value']  = $_POST['value']         ??= null;
	$form['credit_period'] = $_POST['period_value']  ??= null;
	$form['interest_rate'] = $_POST['interest_rate'] ??= null;
	$form['period_type']   = $_POST['period_type']   ??= null;
	$form['type']          = $_POST['type']          ??= null;
}

//walidacja parametrów z przygotowaniem zmiennych dla widoku
function validate(&$form,&$infos,&$msgs,&$hide_intro) {
	// Sprawdzenie, czy przesłane parametry są poprawne
	if(!isset($form['credit_value']) || !isset($form['credit_period']) || !isset($form['interest_rate']) || !isset($form['period_type']) || !isset($form['type'])) return false;
	$infos[] = 'Parametry zostały przekazane.';

	// Ukrycie wstępu strony, jeśli przesłane są parametry
	$hide_intro = true;
	
	// Sprawdzenie, czy przesłane dane nie są puste
	if ($form['credit_value'] == "") $msgs[] = 'Nie podano kwoty kredytu';
	if ($form['credit_period'] == "") $msgs[] = 'Nie podano okresu kredytowania';
	if ($form['interest_rate'] == "") $msgs[] = 'Nie podano oprocentowania';
    if(count($msgs) != 0) return false;

    // Sprawdzenie, czy przesłane dane są poprawnego typu w przypadku opcji wyboru
	if(!in_array($form['period_type'], ['months', 'years'])) $msgs[] = 'Błędny typ okresu kredytowania';
	if(!in_array($form['type'], ['constant', 'decreasing'])) $msgs[] = 'Błędny typ rat';
    if(count($msgs) != 0) return false;

	// Sprawdzenie, czy przesłane dane są liczbami
	if (!is_numeric($form['credit_value'])) $msgs[] = 'Kwota kredytu nie jest liczbą';
	if (!is_numeric($form['credit_period'])) $msgs[] = 'Okres kredytowania nie jest liczbą';
	if (!is_numeric($form['interest_rate'])) $msgs[] = 'Oprocentowanie nie jest liczbą';
    if(count($msgs) != 0) return false;
    
    // Sprawdzenie, czy liczby spełniają wymagania
	if($form['credit_value'] <= 0) $msgs[] = 'Kwota kredytu musi być liczbą dodatnią';
	if($form['credit_period'] <= 0) $msgs[] = 'Okres kredytowania musi być liczbą dodatnią';
	if($form['interest_rate'] <= 0) $msgs[] = 'Oprocentowanie musi być liczbą dodatnią';

    if(count($msgs) != 0) return false;

    return true;
}
	
// wykonaj obliczenia
function process(&$form,&$infos,&$msgs,&$result) {
    $infos [] = 'Parametry są poprawne. Wykonuję obliczenia.';

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

	$result = [
		'rate' => $rate,
		'intrest' => $intrest,
		'cost' => $cost,
	];
}

//inicjacja zmiennych
$form = null;
$infos = array();
$messages = array();
$result = null;
$hide_intro = false;
	
getParams($form);
if ( validate($form,$infos,$messages,$hide_intro) ){
	process($form,$infos,$messages,$result);
}



// 4. Przygotowanie danych dla szablonu

$smarty = new Smarty();

$smarty->assign('app_url',_APP_URL);
$smarty->assign('root_path',_ROOT_PATH);
$smarty->assign('page_title','Kalkulator kredytowy');
$smarty->assign('page_description','Kalkulator kredytowy z formatowniem przy użyciu biblioteki Smarty');
$smarty->assign('page_header','Kalkulator kredytowy');

$smarty->assign('hide_intro',$hide_intro);

//pozostałe zmienne niekoniecznie muszą istnieć, dlatego sprawdzamy aby nie otrzymać ostrzeżenia
$smarty->assign('form',$form);
$smarty->assign('result',$result);
$smarty->assign('messages',$messages);
$smarty->assign('infos',$infos);

// 5. Wywołanie szablonu
$smarty->display(_ROOT_PATH.'/app/calc.html');