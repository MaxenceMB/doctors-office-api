<?php

if (isset($_GET['idPatient']) && isset($_GET['idMedecin'])) { 
	include 'getlinkpdo.php';

	$res = $linkpdo->prepare("DELETE FROM consultation WHERE idPatient = :idPatient AND idMedecin = :idMedecin AND dateRDV = :dateRDV AND heureRDV = :heureRDV");
    $res->execute(array('idPatient' => $_GET['idPatient'], 'idMedecin' => $_GET['idMedecin'], 'dateRDV' => $_GET['dateRDV'], 'heureRDV' => $_GET['heureRDV']));

    if ($res->rowcount() == 0) {
    	header("Location: consultations.php?consultationSuppr=error");
    } else {
    	header("Location: consultations.php?consultationSuppr=".$_GET['idPatient']);
    }

} elseif (isset($_GET['idPatient'])) {
	include 'getlinkpdo.php';

    $res = $linkpdo->prepare("DELETE FROM patient WHERE idPatient = :idPatient");
    $res->execute(array('idPatient' => $_GET['idPatient']));

    if ($res->rowcount() == 0) {
    	header("Location: affichage.php?type=patient&patientSuppr=error");
    } else {
    	header("Location: affichage.php?type=patient&patientSuppr=".$_GET['idPatient']);
    }

} elseif (isset($_GET['idMedecin'])) {
	include 'getlinkpdo.php';

    $res = $linkpdo->prepare("DELETE FROM medecin WHERE idMedecin = :idMedecin");
    $res->execute(array('idMedecin' => $_GET['idMedecin']));

    if ($res->rowcount() == 0) {
    	header("Location: affichage.php?type=medecin&medecinSuppr=error");
    } else {
    	header("Location: affichage.php?type=medecin&medecinSuppr=".$_GET['idMedecin']);
    }
}



?>