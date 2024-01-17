<?php include "session.php";?>
<?php include 'getlinkpdo.php';?>
<?php include 'gestionbd.php';?>
<?php 
if (!isset($_GET['type'])) {
    // on aurait pu rediriger vers affichage.php?type=patient mais ca coutait 2 requête pour le même affichage, on va juste gérer le type dans une variable
    $type = 'patient';
} elseif ($_GET['type'] == "patient" or $_GET['type'] == "medecin" or $_GET['type'] == "consultation") {
    $type = $_GET['type'];
} else {
    // redirection si l'utilisateur entre manuellement (pas possible autrement) un type autre que médecin/patient
    header("Location: affichage.php?type=patient");
}

if (isset($_POST['rechercherPatient'])) {
    if (!isset($_POST['searchinput']) || !isset($_POST["medecinTraitant"]) || !isset($_POST["toulouse"]) || !isset($_POST["civilite"])) {
        header("Location: affichage.php?type=patient&message=errorRecherche");
    }
}

if (isset($_POST['rechercherMedecin'])) {
    if (!isset($_POST['searchinput']) || !isset($_POST["medecinTraitant"]) || !isset($_POST["civilite"])) {
        header("Location: affichage.php?type=medecin&message=errorRecherche");
    }
}

if (isset($_POST['rechercherConsultation'])) {
    if (!isset($_POST['startDate']) || !isset($_POST["endDate"]) || !isset($_POST["startHours"]) || !isset($_POST["endHours"]) || !isset($_POST["startDuree"]) || !isset($_POST["endDuree"]) || !isset($_POST["medecinConsultation"]) || !isset($_POST["patientConsultation"])) {
        header("Location: affichage.php?type=consultation&message=errorRecherche");
    }
}



?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affichage</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/affichage.css">
</head>

<body>
    <?php include "header.php";?>

    <div id="suppression">
        <p>Voulez-vous vraiment supprimer cet objet ?</p>
        <div>
            <button onclick="annulationSuppression(this)" class="btna rednoshadow">Non</button>
            <form method="GET" action="suppression.php">
                <input id="patientSuppr" name="" type="hidden" value="">
                <input id="medecinSuppr" name="" type="hidden" value="">
                <input id="consultationDateRDV" name="" type="hidden" value="">
                <input id="consultationHeureRDV" name="" type="hidden" value="">

                <input type="submit" value="Oui" class="btna greennoshadow">
            </form>
        </div>
    </div>

    <?php

    include 'errorMessage.php';

    ?>

    <main>
        
        <!-- ----------------------------------------------------- -->
        <!-- PATIENT: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <!--<section style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient path">
            <a href="affichage.php?type=patient">Patient</a><span>></span><a href="affichage.php?type=patient">Liste</a>
            <?php if (isset($_POST['rechercherPatient'])) {

            $phrase = "";

            if ($_POST["searchinput"] != "") {
                $phrase .= ', contenant "'.$_POST["searchinput"].'" dans ses informations';
            }

            if ($_POST["medecinTraitant"] != "Indifférent") {
                $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                $resMedecinString->execute(array('idMedecin' => $_POST['medecinTraitant']));
                $result = $resMedecinString->fetch();
                $medecinString = $result[2]." ".$result[0]." ".$result[1];

                $phrase .= ", ayant comme médecin traitant ".$medecinString;
            }

            if ($_POST["toulouse"] != "Indifférent") {
                $phrase .= ", provenant de Toulouse";
            }

            if ($_POST["civilite"] != "Indifférent") {
                $phrase .= ", de civilité ".$_POST['civilite'];
            }
            ?>

            <span>></span><a href="#">Patient<?php echo $phrase;?></a>
            
        <?php } ?>
        </section>-->
        <!-- ----------------------------------------------------- -->
        <!-- MEDECIN: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <!--<section style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin path">
            <a href="affichage.php?type=medecin">Médecin</a><span>></span><a href="affichage.php?type=medecin">Liste</a>


            <?php

            if (isset($_GET['id'])) {
                $res = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                $res->execute(array('idMedecin' => $_GET['id']));
                $result = $res->fetch();
                $medecinString = $result[2]." ".$result[0]." ".$result[1];
            ?>
            <span>></span><a href="#"><?php echo $medecinString;?></a>
        <?php } elseif (isset($_POST['rechercherMedecin'])) {

            $phrase = "";

            if ($_POST["searchinput"] != "") {
                $phrase .= ', contenant "'.$_POST["searchinput"].'"';
            }
            if ($_POST["medecinTraitant"] != "Indifférent") {
                $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                $resMedecinString->execute(array('idMedecin' => $_POST['medecinTraitant']));
                $result = $resMedecinString->fetch();
                $medecinString = $result[2]." ".$result[0]." ".$result[1];

                $phrase .= " ".$medecinString;
            }
            if ($_POST["civilite"] != "Indifférent") {
                $phrase .= ", de civilité ".$_POST['civilite'];
            }

        ?>

            <span>></span><a href="#">Médecin<?php echo $phrase;?></a>

        <?php } ?>
        </section>-->
        <!-- ----------------------------------------------------- -->
        <!-- CONSULTATION: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <!--<section style="display:<?php echo $type=='consultation' ? 'block': 'none'?>" class="Consultation path">
            <a href="affichage.php?type=consultation">Consultation</a><span>></span><a href="affichage.php?type=consultation">Liste</a>
            <?php

            if (isset($_POST['rechercherConsultation'])) {

            $phrase = "";

            if ($_POST["startDate"] != "") {
                $phrase .= ', depuis le '.date("d/m/Y", strtotime($_POST["startDate"]));
            }

            if ($_POST["endDate"] != "") {
                $phrase .= ', jusqu\'au '.date("d/m/Y", strtotime($_POST["endDate"]));
            }

            if ($_POST["startHours"] != "") {
                $phrase .= ', depuis '.$_POST["startHours"];
            }

            if ($_POST["endHours"] != "") {
                $phrase .= ', jusqu\'à '.$_POST["endHours"];
            }

            if ($_POST["startDuree"] != "Indifférent") {
                $phrase .= ', qui dure minimum '.$_POST["startDuree"];
            }

            if ($_POST["endDuree"] != "Indifférent") {
                $phrase .= ', qui dure maximum '.$_POST["endDuree"];
            }

            if ($_POST["medecinConsultation"] != "Indifférent") {
                $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                $resMedecinString->execute(array('idMedecin' => $_POST['medecinConsultation']));
                $result = $resMedecinString->fetch();
                $medecinString = $result[2]." ".$result[0]." ".$result[1];

                $phrase .= ", concerné par le médecin ".$medecinString;
            }

            if ($_POST["patientConsultation"] != "Indifférent") {
                $resPatientString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM patient WHERE idPatient = :idPatient");
                $resPatientString->execute(array('idPatient' => $_POST['patientConsultation']));
                $result = $resPatientString->fetch();
                $patientString = $result[2]." ".$result[0]." ".$result[1];

                $phrase .= ", incluant le patient ".$patientString;
            }
            ?>

            <span>></span><a href="#">Consultation<?php echo $phrase;?></a>
            
        <?php } ?>
        </section>
    -->


        <!-- ----------------------------------------------------- -->
        <!-- CONTENU PRINCIPAL DE LA PAGE (Les deux onglets)       -->
        <!-- ----------------------------------------------------- -->
        <section class="mainSubject">
            <div id = "tabs">
                <button class="tablinks" onclick="showTab('Patient')"      <?php echo $type=='patient'      ? 'id=current': ''?>>Patient</button>
                <button class="tablinks" onclick="showTab('Medecin')"      <?php echo $type=='medecin'      ? 'id=current': ''?>>Medecin</button>
                <button class="tablinks" onclick="showTab('Consultation')" <?php echo $type=='consultation' ? 'id=current': ''?>>Consultation</button>
            </div>
            <h2 style="display:<?php echo $type=='patient'      ? 'block': 'none'?>" class="Patient">     Liste des patients</h2>
            <h2 style="display:<?php echo $type=='medecin'      ? 'block': 'none'?>" class="Medecin">     Liste des médecins</h2>
            <h2 style="display:<?php echo $type=='consultation' ? 'block': 'none'?>" class="Consultation">Liste des consultations</h2>

            <?php include "affichagePatient.php"; ?>

            <?php include "affichageMedecin.php"; ?>

            <?php include "affichageConsultation.php"; ?>


        </section>
    </main>

    <?php include "footer.php";?>

    <script src="js/affichage.js"></script>
    <script src="js/tabsystem.js"></script>
</body>
</html>