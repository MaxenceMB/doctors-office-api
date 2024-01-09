<?php

$protocol="mysql:host=localhost;dbname=cabinet";
$login="root";
$password="";
try {
    $linkpdo = new PDO($protocol, $login, $password);
}
catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>