<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Affichage</title>
	<link rel="stylesheet" type="text/css" href="styles/styles.css">
	<link rel="stylesheet" type="text/css" href="styles/index.css">
</head>

<body>

	<header id="header">
		<h3 id="header-title">Cabinet médical de Mac-Sens et Haine-Zoo</h3>

		<nav>
			<ul>
				<li><a href="affichage.php">Patient</a></li>
				<li id="currentPage"><a href="#">Médecin</a></li>
			</ul>
		</nav>
	</header>

	<main>

		<h2>Liste des médecins</h2>

		<form class="research" onsubmit="return checkValidMedecin()" method="post" action="affichageMedecin.php">
			<div class="flex-research">
				<div class="searchinput">
					<label for="searchinput">Recherche avancé</label>
					<input name="searchinput" id="searchinput" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un médecin ici  (Optionnel)">
				</div>

				<div class="civilite">
					<label for="civilite">Filtre civilité</label>
					<select name="civilite" id="civilite">
						<option>Indifférent</option>
						<option <?php echo (isset($_POST['rechercher'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
						<option <?php echo (isset($_POST['rechercher'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
					</select>
				</div>
			</div>

			<div class="submit">
				<input type="submit" name="rechercher" value="" class="btna blue" id="confirm">
				<input type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
			</div>
		</form>


		<div class="liste-usagers">
			<?php

		if (isset($_GET['id'])) {
			try {
      			$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
			} catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}

			$res = $linkpdo->prepare("SELECT * FROM medecin WHERE idMedecin = :idMedecin");
   			$res->execute(array('idMedecin' => $_GET['id']));

			$data = $res->fetch();
			$resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
			$resCountPatientAssocie->execute(array('idMedecin' => $_GET['id']));
			$countPatient = $resCountPatientAssocie->fetch()[0];
   		?>

   		<div>
			<div class="first-part">
				<p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
				<p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
			</div>
			<div class="second-part">
				<button class="btna bluenoshadow">Modifier</button>
				<button class="btna rednoshadow">Supprimer</button>
			</div>
		</div>


		<?php
	}

		
		else if (isset($_POST['rechercher'])) {
			$recherche = explode(" ", $_POST['searchinput']);

			$where_requete = "";
			$where_lst = array();
			foreach ($recherche as $key => $value) {
				$where_requete .= (($key == 0) ? " WHERE (" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key";
				$where_lst["keyword$key"] = "%$value%";
			}

			$where_requete .= ")";

			if ($_POST["civilite"] != "Indifférent") {
				$where_requete .= " AND civilite = :civilite";
				$where_lst["civilite"] = $_POST['civilite'];
			}

			try {
      			$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
			} catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}

			$res = $linkpdo->prepare("SELECT * FROM medecin".$where_requete." ORDER BY nom");
   			$res->execute($where_lst);

   			if ($res->rowcount() == 0) {
   			?>
   			<p class="nbResultat">Aucun résultat</p>
   			<?php
   		} else {
   			?>
   			<p class="nbResultat"><?php echo $res->rowcount() ?> résultat(s)</p>
   			<?php
   		}

   			?>
   			<div id="createButton">
				<a href="ajoutMedecin.php" class="btna blue">
					Ajouter un médecin
				</a>
			</div>
			<?php

   			while ($data = $res->fetch()) {
   				$resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
				$resCountPatientAssocie->execute(array('idMedecin' => $data['idMedecin']));
				$countPatient = $resCountPatientAssocie->fetch()[0];

   			?>

			<div>
				<div class="first-part">
					<p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
					<p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
				</div>
				<div class="second-part">
					<button class="btna bluenoshadow">Modifier</button>
					<button class="btna rednoshadow">Supprimer</button>
				</div>
			</div>

		<?php
		}
		} else {
			?>
			<p class="nbResultat">Voici un aperçu des 11 premiers médecins du cabinet médical</p>
			<div id="createButton">
				<a href="ajout.php" class="btna blue">
					Ajouter un médecin
				</a>
			</div>
			<?php
			try {
				$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
			}
			catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}
			$res = $linkpdo->prepare("SELECT * FROM medecin ORDER BY nom");
			$res->execute();

			$max11 = 0;

			while ($data = $res->fetch() and $max11 < 11) {
				$resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
				$resCountPatientAssocie->execute(array('idMedecin' => $data['idMedecin']));
				$countPatient = $resCountPatientAssocie->fetch()[0];

				$max11++;
		?>

			<div>
				<div class="first-part">
					<p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
					<p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
				</div>
				<div class="second-part">
					<button class="btna bluenoshadow">Modifier</button>
					<button class="btna rednoshadow">Supprimer</button>
				</div>
			</div>
			<?php
			}}
			?>
		</div>
	</main>

	<script src="js/affichage.js"></script>

</body>
</html>