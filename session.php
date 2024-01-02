<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] != 'root' || $_SESSION['password'] != 'root') {
	// verif si c'est le bon username et login jsp si c'est utile parce que si $_SESSION['username'] est défini on suppose que ca peut 
	// être que lui sauf qu'un utilisateur peut peut etre créer une session lui-même donc on vérif le mot de passe
	header("Location: index.php"); // ou ./ mais pas sur si y'a 2 pages .php qui se suivent (i.php/p.php ca va revenir à i.php)
}
?>