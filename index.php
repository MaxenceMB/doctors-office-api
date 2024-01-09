<?php 
session_start();

if (isset($_POST['connecter']) && $_POST['username'] == "root" && $_POST['password'] == "root") {
	$_SESSION['username'] = $_POST['username'];
	$_SESSION['password'] = $_POST['password'];
	header("Location: affichage.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="styles/index.css">
	<title>ddd</title>
</head>
<body>

			<form method="post" class="div-sign div-signin">
				<h2 class="top">
					Connexion au cabinet
				</h2>

				<?php

				if (isset($_GET['disconnect'])) {
					session_destroy();
				}

					if (isset($_POST['connecter'])) {
						if ($_POST['username'] != "root") {
							?>
					<div class="error">
						Le nom d'utilisateur n'existe pas
					</div>
					<?php
						} elseif ($_POST['password'] != "root") {
							?>
					<div class="error">
						Le mot de passe est invalide
					</div>
					<?php
						}
					}
				?>
				

				<div class="middle">
					<div class="parts">
						<label>Nom d'utilisateur</label>
						<div class="input">
							<input value="root" maxlength="150" required name="username" placeholder="username">
							<button class="loginIcon" type="button">
								<img src="images/compte.png">
							</button>
						</div>

						<!--<span class="is_valid">{{ username_is_valid }}</span>-->
					</div>

					<div class="parts">
						<label>Mot de passe</label>

						<div class="input">
							<input value="root" maxlength="100" required id="password" name="password" type="password" placeholder="motdepasse">
							<button class="loginIcon" type="button" onclick="showPassword(this)">
								<img src="images/oeilFerme.png">
							</button>
						</div>
						
						<!--<span class="is_valid">{{ password_is_valid }}<span> -->
					</div>
				</div>

				<input type="submit" class="btna blue submitLogin" name="connecter" value="Se connecter">

				<div class="separator"></div>

				<a class="forgotten-password" href="#" onclick="alert('Dommage')">J'ai oublié mon mot de passe</a>
			</form>

		<script type="text/javascript" src="js/index.js"></script>

		
</body>
</html>