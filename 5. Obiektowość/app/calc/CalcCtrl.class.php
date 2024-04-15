<?php

require_once $conf->root_path.'/lib/smarty/Smarty.class.php';
require_once $conf->root_path.'/lib/Messages.class.php';
require_once $conf->root_path.'/app/calc/CalcForm.class.php';
require_once $conf->root_path.'/app/calc/CalcResult.class.php';

/** 
 * Kontroler kalkulatora kredytowego
 * @author Michał Pasierbski
 */
class CalcCtrl {

	private $msgs;   //wiadomości dla widoku
	private $form;   //dane formularza (do obliczeń i dla widoku)
	private $result; //inne dane dla widoku
	private $hide_intro; //zmienna informująca o tym czy schować intro

	/** 
	 * Konstruktor - inicjalizacja właściwości
	 */
	public function __construct() {
		//stworzenie potrzebnych obiektów
		$this->msgs = new Messages();
		$this->form = new CalcForm();
		$this->result = new CalcResult();
		$this->hide_intro = false;
	}
	
	/** 
	 * Pobranie parametrów
	 */
	public function getParams() {
		$this->form->credit_value  = $_POST['value']         ??= null;
		$this->form->credit_period = $_POST['period_value']  ??= null;
		$this->form->interest_rate = $_POST['interest_rate'] ??= null;
		$this->form->period_type   = $_POST['period_type']   ??= null;
		$this->form->type          = $_POST['type']          ??= null;
	}
	
	/**
	 * Walidacja parametrów
	 * @return boolean true jeśli brak błędów, false w przeciwnym wypadku
	 */
	public function validate() {
		// Sprawdzenie, czy przesłane parametry są poprawne
		if(!isset($this->form->credit_value) || !isset($this->form->credit_period) || !isset($this->form->interest_rate) || !isset($this->form->period_type) || !isset($this->form->type)) return false;
	
		// Ukrycie wstępu strony, jeśli przesłane są parametry
		$this->hide_intro = true;
		
		// Sprawdzenie, czy przesłane dane nie są puste
		if ($this->form->credit_value == "") $this->msgs->addError('Nie podano kwoty kredytu');
		if ($this->form->credit_period == "") $this->msgs->addError('Nie podano okresu kredytowania');
		if ($this->form->interest_rate == "") $this->msgs->addError('Nie podano oprocentowania');
		if($this->msgs->isError()) return false;
	
		// Sprawdzenie, czy przesłane dane są poprawnego typu w przypadku opcji wyboru
		if(!in_array($this->form->period_type, ['months', 'years'])) $this->msgs->addError('Błędny typ okresu kredytowania');
		if(!in_array($this->form->type, ['constant', 'decreasing'])) $this->msgs->addError('Błędny typ rat');
		if($this->msgs->isError()) return false;
	
		// Sprawdzenie, czy przesłane dane są liczbami
		if (!is_numeric($this->form->credit_value)) $this->msgs->addError('Kwota kredytu nie jest liczbą');
		if (!is_numeric($this->form->credit_period)) $this->msgs->addError('Okres kredytowania nie jest liczbą');
		if (!is_numeric($this->form->interest_rate)) $this->msgs->addError('Oprocentowanie nie jest liczbą');
		if($this->msgs->isError()) return false;
		
		// Sprawdzenie, czy liczby spełniają wymagania
		if($this->form->credit_value <= 0) $this->msgs->addError('Kwota kredytu musi być liczbą dodatnią');
		if($this->form->credit_period <= 0) $this->msgs->addError('Okres kredytowania musi być liczbą dodatnią');
		if($this->form->interest_rate <= 0) $this->msgs->addError('Oprocentowanie musi być liczbą dodatnią');
	
		// Jeśli wystąpiły błędy, zwróć false, w przeciwnym wypadku true
		return !$this->msgs->isError();

	}
	
	/** 
	 * Pobranie wartości, walidacja, obliczenie i wyświetlenie
	 */
	public function process() {

		$this->getParams();

		if ($this->validate()) {
			$this->msgs->addInfo('Parametry są poprawne. Wykonuję obliczenia.');

			// konwersja przesłanych danych na odpowiedni typ
			$this->form->credit_value = floatval($this->form->credit_value);
			$this->form->credit_period = intval($this->form->credit_period);
			$this->form->interest_rate = floatval($this->form->interest_rate);
			
			// obliczenie oprocentowania miesięcznego
			$this->form->monthly_interest_rate = $this->form->interest_rate / (12 * 100);
			
			// obliczenie liczby rat, jeśli okres kredytowania jest podany w latach (domyślnie w miesiącach)
			if ($this->form->period_type == 'years') $this->form->credit_period *= 12;
			
			// obliczenie wartości raty lub rat w zależności od wybranej opcji
			if ($this->form->type == 'constant') {
				$this->form->rate = ($this->form->credit_value * $this->form->monthly_interest_rate) / (1 - pow((1 + $this->form->monthly_interest_rate), -$this->form->credit_period));
			} else {
				$this->form->rate = [];
				$this->form->credit_value_monthly = $this->form->credit_value / $this->form->credit_period;
				for ($i = 0; $i < $this->form->credit_period; $i++) {
					$this->form->rate[$i] = $this->form->credit_value_monthly + ($this->form->credit_value_monthly * ($this->form->credit_period - $i - 1) * $this->form->monthly_interest_rate);
				}
			}

			// obliczenie odsetek
			$this->form->intrest = 0;
			if ($this->form->type == 'constant') {
				$this->form->intrest = $this->form->rate * $this->form->credit_period - $this->form->credit_value;
			} else {
				foreach ($this->form->rate as $r) {
					$this->form->intrest += $r;
				}
				$this->form->intrest -= $this->form->credit_value;
			}

			// obliczenie kosztu kredytu
			$this->form->cost = $this->form->credit_value + $this->form->intrest;

			// Korekcja ilości miesięcy
			if($this->form->period_type == 'years') $this->form->credit_period /= 12;
			$this->result = [
				'rate' => $this->form->rate,
				'intrest' => $this->form->intrest,
				'cost' => $this->form->cost,
				'type' => $this->form->type
			];

			$this->msgs->addInfo('Wykonano obliczenia.');

		}

		$this->generateView();
	}


	/**
	 * Wygenerowanie widoku
	 */
	public function generateView() {
		global $conf;
		
		$smarty = new Smarty();
		$smarty->assign('conf', $conf);
		
		$smarty->assign('page_title', 'Kalkulator kredytowy');
		$smarty->assign('page_description', 'Kalkulator kredytowy z formatowniem przy użyciu biblioteki Smarty');
		$smarty->assign('page_header', 'Kalkulator kredytowy');
				
		$smarty->assign('hide_intro', $this->hide_intro);
		
		$smarty->assign('msgs', $this->msgs);
		$smarty->assign('form', $this->form);
		$smarty->assign('res', $this->result);
		
		$smarty->display($conf->root_path.'/app/calc/CalcView.html');
	}
}
