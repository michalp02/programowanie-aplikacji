<?php
require_once 'init.php';

// Projekt pokazuje podstawy AJAX'a, poprzez generowanie podczas wyszukiwania jedynie tablicy osób (tylko część całego widoku HTML)
// i podmianę tej tablicy na istniejącej stronie w przeglądarce.
// Dokonuje tego specjalna, prosta funkcja JS działająca w przeglądarce.
// Druga, banalnie krótka funkcja JS, wykorzystywana jest do potwierdzenia usuwania rekordów.

// Funkcje te zawarte są w skrypcie functions.js umieszczonym w nagłówku widoku Html, pobieranym przez przeglądarkę, jak np pliki .css.

// Do realizacji tego zadania stworzono dodatkową akcję 'personListPart', która w odróżnieniu od 'personList' generuje samą tabelę.

// Widok strony listy osób został podzielony na dwa widoki: 'PersonListFullPage.tpl' - wywołany generuje cała stronę Html -
// oraz widok 'PersonListTable.tpl' - generujący tylko część strony Html (tylko tabelę).

// Sprawdź:
// 1 - dodatkową ścieżkę w routingu
// 2 - dodatkową metodę akcji w kontrolerze PersonListCtrl
// 3 - widok 'PersonListFullPage.tpl'
// 4 - widok 'PersonListTable.tpl'
// 5 - oraz plik 'functions.js', umieszczony w nagłówku html ('main.tpl') i jego zawartość - dwie wykorzystywane funkcje
//     (są w nim 4 funkcje, ale ta aplikacja wykorzystuje tylko dwie pierwsze)
//     również jak te funkcje są wywoływane w widokach (formularz wyszukiwania oraz przycisk usuwania rekordu)

require_once 'routing.php';

\core\SessionUtils::loadMessages();

\core\App::getRouter()->go();