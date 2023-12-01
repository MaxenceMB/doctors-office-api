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
				<li id="currentPage"><a href="#">Patient</a></li>
				<li><a href="affichageMedecin.php">Médecin</a></li>
			</ul>
		</nav>
	</header>

	<main>

            <h2>Liste des patients</h2>


		<form class="research" method="post" action="affichage.php">
			<div class="flex-research">
				<div class="searchinput">
					<label for="searchinput">Recherche avancé</label>
					<input name="searchinput" required id="searchinput" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un patient ici (Optionnel)">
				</div>

				<div class="medecinTraitant">
					<label for="medecinTraitant">Filtre médecin</label>
					<select name="medecinTraitant" id="medecinTraitant">
						<option>Indifférent</option>
						<?php
							try {
				      			$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
							} catch (Exception $e) {
								die('Erreur : ' . $e->getMessage());
							}
							$resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin ORDER BY nom");
							$resMedecinString->execute();

   							while ($data = $resMedecinString->fetch()) {
   								$string = $data[0]." ".$data[1]." (".$data[2].")";
						?>

						<option <?php echo (isset($_POST['rechercher'])) ? ($_POST['medecinTraitant'] == $string ? 'selected' : '') : '' ?>><?php echo $string ?></option>

						<?php
						}
						?>
					</select>
				</div>

				<div class="toulouse">
					<label for="toulouse">Filtre Toulouse</label>
					<select name="toulouse" id="toulouse">
						<option value="">Indifférent</option>
						<option value="toulouse" <?php echo (isset($_POST['rechercher'])) ? ($_POST['toulouse'] == 'toulouse' ? 'selected' : '') : '' ?>>Toulouse</option>
					</select>
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
		
		if (isset($_POST['rechercher'])) {
			$recherche = explode(" ", $_POST['searchinput']);

			$where_requete = "";
			$where_lst = array();
			foreach ($recherche as $key => $value) {
				$where_requete .= (($key == 0) ? " WHERE" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key OR adresse1 LIKE :keyword$key OR adresse2 LIKE :keyword$key OR ville LIKE :keyword$key OR codePostal LIKE :keyword$key OR numSecu LIKE :keyword$key";
				$where_lst["keyword$key"] = "%$value%";
			}

			try {
      			$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
			} catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}
			$res = $linkpdo->prepare("SELECT * FROM patient".$where_requete." ORDER BY nom");
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
				<a href="ajout.php" class="btna blue">
					Ajouter un patient
				</a>
			</div>
			<?php

   			while ($data = $res->fetch()) {
   				if ($data['idMedecin'] != null) {
	   				$resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
   					$resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
   					$result = $resMedecinString->fetch();
   					$medecinString = $result[2]." ".$result[0]." ".$result[1];
   					// pour un foreach affiche en double la liste : il y a 6 execution alors que $result[3] renvoit une erreur
   					// echo "test";
   					//foreach($result as $d) {
   					//	echo $d."DD";
   					//}
   				} else {
   					$medecinString = "Aucun";
   				}

   			?>

			<div>
				<div class="first-part">
					<p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
					<p class="adresse"><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
					<p class="ville"><span class="label">Ville</span><?php echo $data['ville'] ?></p>
					<p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
					<p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
					<p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="#" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
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
			<p class="nbResultat">Voici un aperçu des 11 premiers patients du cabinet médical</p>
			<div id="createButton">
				<a href="ajout.php" class="btna blue">
					Ajouter un patient
				</a>
			</div>
			<?php
			try {
				$linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
			}
			catch (Exception $e) {
				die('Erreur : ' . $e->getMessage());
			}
			$res = $linkpdo->prepare("SELECT * FROM patient ORDER BY nom");
			$res->execute();

			$max11 = 0;

			while ($data = $res->fetch() and $max11 < 11) {
				$max11++;
				if ($data['idMedecin'] != null) {
	   				$resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
   					$resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
   					$result = $resMedecinString->fetch();
   					$medecinString = $result[2]." ".$result[0]." ".$result[1];
   				} else {
   					$medecinString = "Aucun";
   				}
		?>

			<div>
				<div class="first-part">
					<p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
					<p class="adresse"><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
					<p class="ville"><span class="label">Ville</span><?php echo $data['ville'] ?></p>
					<p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
					<p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
					<p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="#" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
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


</body>
</html>