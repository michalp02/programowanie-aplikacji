<?php
// W skrypcie definicji kontrolera nie trzeba dołączać już niczego.
// Kontroler wskazuje tylko za pomocą 'use' te klasy z których jawnie korzysta
// (gdy korzysta niejawnie to nie musi - np używa obiektu zwracanego przez funkcję)

// Zarejestrowany autoloader klas załaduje odpowiedni plik automatycznie w momencie, gdy skrypt będzie go chciał użyć.
// Jeśli nie wskaże się klasy za pomocą 'use', to PHP będzie zakładać, iż klasa znajduje się w bieżącej
// przestrzeni nazw - tutaj jest to przestrzeń 'app\controllers'.

// Przypominam, że tu również są dostępne globalne funkcje pomocnicze - o to nam właściwie chodziło

namespace app\controllers;

//zamieniamy zatem 'require' na 'use' wskazując jedynie przestrzeń nazw, w której znajduje się klasa
use app\forms\CalcForm;
use app\transfer\CalcResult;

/** Kontroler kalkulatora
 * @author Przemysław Kudłacik
 *
 */
class CalcCtrl {

	private $form;   //dane formularza (do obliczeń i dla widoku)
	private $result; //inne dane dla widoku

	/** 
	 * Konstruktor - inicjalizacja właściwości
	 */
	public function __construct(){
		//stworzenie potrzebnych obiektów
		$this->form = new CalcForm();
		$this->result = new CalcResult();
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
	 * @return true jeśli brak błedów, false w przeciwnym wypadku 
	 */
	public function validate() {
		// Sprawdzenie, czy przesłane parametry są poprawne
		if(!isset($this->form->credit_value) || !isset($this->form->credit_period) || !isset($this->form->interest_rate) || !isset($this->form->period_type) || !isset($this->form->type)) return false;
	
		// Ukrycie wstępu strony, jeśli przesłane są parametry
		$this->hide_intro = true;
		
		// Sprawdzenie, czy przesłane dane nie są puste
		if ($this->form->credit_value == "") getMessages()->addError('Nie podano kwoty kredytu');
		if ($this->form->credit_period == "") getMessages()->addError('Nie podano okresu kredytowania');
		if ($this->form->interest_rate == "") getMessages()->addError('Nie podano oprocentowania');
		if(getMessages()->isError()) return false;
	
		// Sprawdzenie, czy przesłane dane są poprawnego typu w przypadku opcji wyboru
		if(!in_array($this->form->period_type, ['months', 'years'])) getMessages()->addError('Błędny typ okresu kredytowania');
		if(!in_array($this->form->type, ['constant', 'decreasing'])) getMessages()->addError('Błędny typ rat');
		if(getMessages()->isError()) return false;
	
		// Sprawdzenie, czy przesłane dane są liczbami
		if (!is_numeric($this->form->credit_value)) getMessages()->addError('Kwota kredytu nie jest liczbą');
		if (!is_numeric($this->form->credit_period)) getMessages()->addError('Okres kredytowania nie jest liczbą');
		if (!is_numeric($this->form->interest_rate)) getMessages()->addError('Oprocentowanie nie jest liczbą');
		if(getMessages()->isError()) return false;
		
		// Sprawdzenie, czy liczby spełniają wymagania
		if($this->form->credit_value <= 0) getMessages()->addError('Kwota kredytu musi być liczbą dodatnią');
		if($this->form->credit_period <= 0) getMessages()->addError('Okres kredytowania musi być liczbą dodatnią');
		if($this->form->interest_rate <= 0) getMessages()->addError('Oprocentowanie musi być liczbą dodatnią');
	
		// Jeśli wystąpiły błędy, zwróć false, w przeciwnym wypadku true
		return !getMessages()->isError();

	}
	
	/** 
	 * Pobranie wartości, walidacja, obliczenie i wyświetlenie
	 */
	public function action_calcCompute(){

		$this->getParams();
		
		if ($this->validate()) {
			
			getMessages()->addInfo('Parametry poprawne.');
				
			//konwersja parametrów na int
			$this->form->credit_value = floatval($this->form->credit_value);
			$this->form->credit_period = intval($this->form->credit_period);
			$this->form->interest_rate = floatval($this->form->interest_rate);
			
			
			
			//wykonanie operacji
			// konwersja przesłanych danych na odpowiedni typ
			$this->form->credit_value = floatval($this->form->credit_value);
			$this->form->credit_period = intval($this->form->credit_period);
			$this->form->interest_rate = floatval($this->form->interest_rate);
			
			if (!inRole('admin') && $this->form->credit_value > 100000) {
				getMessages()->addError('Kwota kredytu przekracza 100000 zł. Maksymalna kwota dla użytkowników to 100000 zł.');
			} else {

			
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
				
				getMessages()->addInfo('Wykonano obliczenia.');
			}
		}
		
		$this->generateView();
	}
	
	public function action_calcShow(){
		getMessages()->addInfo('Witaj w kalkulatorze');
		$this->generateView();
	}
	
	/**
	 * Wygenerowanie widoku
	 */
	public function generateView(){

		getSmarty()->assign('user',unserialize($_SESSION['user']));
				
		getSmarty()->assign('page_title','Super kalkulator - role');

		getSmarty()->assign('form',$this->form);
		getSmarty()->assign('res',$this->result);
		
		getSmarty()->display('CalcView.tpl');
	}
}
