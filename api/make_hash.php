<?php
// wGENERATOR HASU POZDRAWIAM
$haslo = 'test123';

$hash = password_hash($haslo, PASSWORD_DEFAULT);


echo "Hasło: $haslo<br>";
echo "Hash do wklejenia w bazie:<br><pre>$hash</pre>";
