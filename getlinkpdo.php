<?php
try {
    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
}
catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>