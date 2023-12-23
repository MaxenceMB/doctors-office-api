<?php

	$LINK_PATIENT = '/doctors-office-website/affichage.php';

?>

<header id="header">
	<h3 id="header-title">Cabinet médical de Mac-Sens et Haine-Zoo</h3>

	<nav>
		<ul>
			<li <?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) ? 'id="currentPage"' : '';?> >
				<a href="<?php echo str_starts_with($_SERVER['REQUEST_URI'], $LINK_PATIENT) ? '#' : 'affichage.php'?>">
					Carnet
				</a>
			</li>
		</ul>
	</nav>
</header>