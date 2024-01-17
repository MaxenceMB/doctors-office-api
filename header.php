<?php


	$LINK_PATIENT = '/doctors-office-website/affichage.php'; /* par defaut on a ça, meme si il y a tout le temps ?type=..., c'est fait la moitié du temps en js donc php comprend pas */
	$LINK_MEDECIN = '/doctors-office-website/affichage.php?type=medecin';
	$LINK_CONSULTATION = '/doctors-office-website/affichage.php?type=consultation';
	$LINK_STATISTIQUES = '/doctors-office-website/statistiques.php';

?>
<img id="background" src="images/background3.jpg">

<header id="header">
	<h3 id="header-title">
		Cabinet médical de Mac-Sens et Haine-Zoo
		<?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'root' && $_SESSION['password'] == 'iutinfo') { ?>	


		<?php } ?>
	</h3>
		<form method="GET" action="index.php"><img src="images/compte.png"> <input type="submit" name="disconnect" value="Déconnexion"></form>

	<nav>
		<ul>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? '#' : 'affichage.php?type=patient'?>">
					Usagers
				</a>
			</li>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? '#' : 'affichage.php?type=medecin'?>">
					Médecins
				</a>
			</li>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? '#' : 'affichage.php?type=consultation'?>">
					Consultations
				</a>
			</li>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_STATISTIQUES) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_STATISTIQUES) ? '#' : 'statistiques.php'?>">
					Statistiques
				</a>
			</li>
		</ul>
	</nav>
</header>

