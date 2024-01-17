<?php

if (isset($_GET['idPatient']) && isset($_GET['idMedecin'])) { 
    include 'getlinkpdo.php';

    $res = $linkpdo->prepare("DELETE FROM consultation WHERE idPatient = :idPatient AND idMedecin = :idMedecin AND dateRDV = :dateRDV AND heureRDV = :heureRDV");
    $res->execute(array('idPatient' => $_GET['idPatient'], 'idMedecin' => $_GET['idMedecin'], 'dateRDV' => $_GET['dateRDV'], 'heureRDV' => $_GET['heureRDV']));

    if ($res->rowcount() == 0) {
        header("Location: affichage.php?type=consultation&message=medecinSupprError");
    } else {
        header("Location: affichage.php?type=consultation&message=medecinSupprSuccess");
    }

} elseif (isset($_GET['idPatient'])) {
    include 'getlinkpdo.php';

    $resJointureConsul = $linkpdo->prepare("DELETE FROM consultation WHERE idPatient = :idPatient");
    $resJointureConsul->execute(array('idPatient' => $_GET['idPatient']));

    $res = $linkpdo->prepare("DELETE FROM patient WHERE idPatient = :idPatient");
    $res->execute(array('idPatient' => $_GET['idPatient']));

    if ($res->rowcount() == 0) {
        header("Location: affichage.php?type=patient&message=patientSupprError");
    } else {
        header("Location: affichage.php?type=patient&message=patientSupprSuccess");
    }

} elseif (isset($_GET['idMedecin'])) {
    include 'getlinkpdo.php';

    $resJointureConsul = $linkpdo->prepare("DELETE FROM consultation WHERE idMedecin = :idMedecin");
    $resJointureConsul->execute(array('idMedecin' => $_GET['idMedecin']));

    $resForeignKeyPatient = $linkpdo->prepare("UPDATE patient SET idMedecin = NULL WHERE idMedecin = :idMedecin");
    $resForeignKeyPatient->execute(array('idMedecin' => $_GET['idMedecin']));

    $res = $linkpdo->prepare("DELETE FROM medecin WHERE idMedecin = :idMedecin");
    $res->execute(array('idMedecin' => $_GET['idMedecin']));

    if ($res->rowcount() == 0) {
        header("Location: affichage.php?type=medecin&message=medecinSupprError");
    } else {
        header("Location: affichage.php?type=medecin&message=medecinSupprSuccess");
    }
}



?>