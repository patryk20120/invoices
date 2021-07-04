<?php

define('DB_SERVER', 'localhost');
define('DB_DATABASE', 'iai_zadanie_faktury');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

$dataBase = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if (mysqli_connect_errno()) {
    echo "Błąd połączenia do MySQL: " . mysqli_connect_error();
    echo "<br>Sprawdź plik konfiguracyjny (incl/config.php) w celu poprawności danych dostępowych.";
    exit;
}

$dataBase->set_charset("utf8");

checkForDataBaseTables();

$invoiceProductCountTypeArray = array(
    1 => 'szt.',
    2 => 'kg',
    3 => 'op.',
);

$invoiceProductVATArray = array(
    1 => '23%',
    2 => '8%',
    3 => '5%',
    4 => '0%',
    5 => 'z.w.',
);

$invoicePaymentMethodArray = array(
    1 => 'Płatność gotówką',
    2 => 'Płatność przelewem',
    3 => 'Płatność kartą',
    4 => 'Płatność online',
);