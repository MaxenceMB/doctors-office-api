<?php
function createConnection() : PDO {
    $pdo = null;
    try {
        $pdo = new PDO('mysql:host=mysql-ember.alwaysdata.net;dbname=ember_cabinet_medical_auth;charset=utf8', 'ember', '$iutinfo');
    } catch (Exception $e) {
        echo ("Failed to load database");
        exit(1);
    }
    return $pdo;
}