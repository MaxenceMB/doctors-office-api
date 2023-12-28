<?php

	$LINK_PATIENT = '/doctors-office-website/affichage.php';
	$LINK_MEDECIN = '/doctors-office-website/affichage.php?type=medecin';
	$LINK_CONSULTATION = '/doctors-office-website/consultations.php';
	$LINK_STATISTIQUES = '/doctors-office-website/statistiques.php';

?>

<header id="header">
	<h3 id="header-title">Cabinet médical de Mac-Sens et Haine-Zoo</h3>

	<nav>
		<ul>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) && !str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? '#' : 'affichage.php?type=patient'?>">
					Usagers
				</a>
			</li>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_MEDECIN) ? '#' : 'affichage.php?type=medecin'?>">
					Médecins
				</a>
			</li>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_CONSULTATION) ? '#' : 'consultations.php'?>">
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