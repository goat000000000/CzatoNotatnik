<?php
// wpisz tutaj hasło, jakie chcesz mieć do logowania
$haslo = 'test123';

// wygeneruj hash
$hash = password_hash($haslo, PASSWORD_DEFAULT);

// pokaż wynik
echo "Hasło: $haslo<br>";
echo "Hash do wklejenia w bazie:<br><pre>$hash</pre>";
