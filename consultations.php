<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affichage</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
    <link rel="stylesheet" type="text/css" href="styles/consultations.css">
</head>

<body>
    <?php include "header.php";?>
    <?php include 'getlinkpdo.php';?>

    <div id="suppression">
        <p>Voulez-vous vraiment supprimer cette personne ?</p>
        <div>
            <button onclick="annulationSuppression(this)" href="#" class="btna rednoshadow">Non</button>
            <form method="GET" action="suppression.php"><input id="personneASupprimer" name="" type="hidden" value=""><input type="submit" value="Oui" class="btna greennoshadow"></form>
        </div>
    </div>

    <main>

        <!-- ----------------------------------------------------- -->
        <!-- PATIENT: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <section class="Patient path">
            <a href="consultations.php">Consultation</a><span>></span><a href="consultations.php">Liste</a>
            <?php

            if (isset($_POST['rechercher'])) {

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

        <!-- ----------------------------------------------------- -->
        <!-- CONTENU PRINCIPAL DE LA PAGE (Les deux onglets)       -->
        <!-- ----------------------------------------------------- -->
        <section class="mainSubject">
            <div id = "tabs">
                <button class="tablinks" id="current">Consultation</button>
            </div>
            <h2 class="Patient">Liste des consultations</h2>


            <!-- ----------------------------------------------------- -->
            <!-- PATIENT: ONGLET PATIENT                               -->
            <!-- ----------------------------------------------------- -->
            <div class="Patient" id="formPatient">
                <!-- ----------------------------------------------------- -->
                <!-- PATIENT: FORMULAIRE DE RECHERCHE                      -->
                <!-- ----------------------------------------------------- -->
                <form class="research" onsubmit="return checkValidConsultation()" method="post" action="consultations.php">
                    <div class="flex-research">
                        <div>
                            <label for="startDate">Depuis le</label>
                            <input type="date" id="startDate" name="startDate" min="2018-01-01" max="2030-12-31" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['startDate'] : '' ?>"/>
                        </div>

                        <div>
                            <label for="endDate">Jusqu'au</label>
                            <input type="date" id="endDate" name="endDate" min="2018-01-01" max="2030-12-31" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['endDate'] : '' ?>"/>
                        </div>

                        <div>
                            <label for="startHours">De</label>
                            <input type="time" id="startHours" name="startHours" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['startHours'] : '' ?>"/>
                        </div>

                        <div>
                            <label for="endHours">À</label>
                            <input type="time" id="endHours" name="endHours" value="<?php echo (isset($_POST['rechercher'])) ? $_POST['endHours'] : '' ?>"/>
                        </div>

                        <div>
                            <label for="startDuree">Durée minimum</label>
                            <select name="startDuree" id="startDuree">
                                <option>Indifférent</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['startDuree'] == '0 minute' ? 'selected' : '') : '' ?>>0 minute</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['startDuree'] == '30 minutes' ? 'selected' : '') : '' ?>>30 minutes</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['startDuree'] == '1 heure' ? 'selected' : '') : '' ?>>1 heure</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['startDuree'] == '1 heure et demi' ? 'selected' : '') : '' ?>>1 heure et demi</option>
                            </select>
                        </div>

                        <div>
                            <label for="endDuree">Durée maximum</label>
                            <select name="endDuree" id="endDuree">
                                <option>Indifférent</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['endDuree'] == '30 minutes' ? 'selected' : '') : '' ?>>30 minutes</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['endDuree'] == '1 heure' ? 'selected' : '') : '' ?>>1 heure</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['endDuree'] == '1 heure et demi' ? 'selected' : '') : '' ?>>1 heure et demi</option>
                                <option <?php echo (isset($_POST['rechercher'])) ? ($_POST['endDuree'] == '2 heures' ? 'selected' : '') : '' ?>>2 heures</option>
                            </select>
                        </div>


                        <div>
                            <label for="medecinConsultation">Filtre médecin</label>
                            <select name="medecinConsultation" id="medecinConsultation">
                                <option>Indifférent</option>
                                <?php
                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[0]." ".$data[1]." (".$data[2].")";
                                        $idMedecinT = $data[3];
                                ?>


                                <option value="<?php echo $idMedecinT?>" <?php echo isset($_POST['rechercher']) ? $_POST['medecinConsultation'] == $idMedecinT ? 'selected' : '' : ''; ?>><?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label for="patientConsultation">Filtre patient</label>
                            <select name="patientConsultation" id="patientConsultation">
                                <option>Indifférent</option>
                                <?php
                                    $resPatientString = $linkpdo->prepare("SELECT nom, prenom, civilite, idPatient FROM patient ORDER BY nom");
                                    $resPatientString->execute();

                                    while ($data = $resPatientString->fetch()) {
                                        $string = $data[0]." ".$data[1]." (".$data[2].")";
                                        $idPatientT = $data[3];
                                ?>


                                <option value="<?php echo $idPatientT?>" <?php echo isset($_POST['rechercher']) ? $_POST['patientConsultation'] == $idPatientT ? 'selected' : '' : ''; ?>><?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="submit">
                        <input onclick="fromButtonSearch=true;" type="submit" name="rechercher" value="" class="btna blue" id="confirm">
                        <input onclick="fromButtonSearch=false;" type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
                    </div>
                </form>



                <!-- ----------------------------------------------------- -->
                <!-- PATIENT: LISTE DES PATIENTS EN RESULTAT DE RECHERCHE  -->
                <!-- ----------------------------------------------------- -->
                <div class="liste-usagers">
                
                <?php

                // ----------------------------------------------------------------------------------------------
                // PATIENT: CAS 2 : On fait une recherche (POST) dans l'onglet patient
                // ----------------------------------------------------------------------------------------------
                if (isset($_POST['rechercher'])) {
                    $where_requete = "";
                    $where_lst = array();

                    if ($_POST["startDate"] != "") {
                        $where_requete .= " AND dateRDV >= :startDate";
                        $where_lst["startDate"] = $_POST['startDate'];
                    }

                    if ($_POST["endDate"] != "") {
                        $where_requete .= " AND dateRDV <= :endDate";
                        $where_lst["endDate"] = $_POST['endDate'];
                    }

                    if ($_POST["startHours"] != "") {
                        $where_requete .= " AND heureRDV >= :startHours";
                        $where_lst["startHours"] = $_POST['startHours'];
                    }

                    if ($_POST["endHours"] != "") {
                        $where_requete .= " AND heureRDV <= :endHours";
                        $where_lst["endHours"] = $_POST['endHours'];
                    }

                    if ($_POST["startDuree"] != "Indifférent") {
                        $sDuree = 0;
                        switch ($_POST["startDuree"]) {
                            case "0 minute":
                                $sDuree = 0;
                                break;
                            case "30 minutes":
                                $sDuree = 30;
                                break;
                            case "1 heure":
                                $sDuree = 60;
                                break;
                            case "1 heure et demi":
                                $sDuree = 90;
                        }
                        $where_requete .= " AND duree >= :startDuree";
                        $where_lst["startDuree"] = $sDuree;
                    }

                    if ($_POST["endDuree"] != "Indifférent") {
                        $eDuree = 0;
                        switch ($_POST["endDuree"]) {
                            case "30 minutes":
                                $eDuree = 30;
                                break;
                            case "1 heure":
                                $eDuree = 60;
                                break;
                            case "1 heure et demi":
                                $eDuree = 90;
                                break;
                            case "2 heures":
                                $eDuree = 120;
                        }
                        $where_requete .= " AND duree <= :endDuree";
                        $where_lst["endDuree"] = $eDuree;
                    }

                    if ($_POST["medecinConsultation"] != "Indifférent") {
                        $where_requete .= " AND idMedecin = :idMedecin";
                        $where_lst["idMedecin"] = $_POST['medecinConsultation'];
                    }

                    if ($_POST["patientConsultation"] != "Indifférent") {
                        $where_requete .= " AND idPatient = :idPatient";
                        $where_lst["idPatient"] = $_POST['patientConsultation'];
                    }

                    $res = $linkpdo->prepare("SELECT * FROM consultation WHERE 1=1".$where_requete." ORDER BY dateRDV desc, heureRDV desc");
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
                // ----------------------------------------------------------------------------------------------
                // PATIENT: CAS 3 : Premier arrivée sur la page (GET), on affiche un aperçu de la table consultation
                // ----------------------------------------------------------------------------------------------
                } else {
                    $res = $linkpdo->prepare("SELECT * FROM consultation ORDER BY dateRDV desc, heureRDV desc");
                    $res->execute();
                    ?>

                    <p class="nbResultat">Voici la liste des <?php echo $res->rowcount() ?> consultations du cabinet médical</p>

                    <?php } ?>




                <div id="createButton">
                    <a href="saisieConsultation.php" class="btna blue">
                        Ajouter une consultation
                    </a>
                </div>
                
                <table>
                  <thead>
                    <tr>
                      <th>Jour (jj/mm/aaaa)</th>
                      <th>Heure</th>
                      <th>Durée</th>
                      <th>Médecin</th>
                      <th>Patient</th>
                    </tr>
                   </thead>
                   <tbody>
                <?php

                    while ($data = $res->fetch()) {
                        $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                        $resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
                        $resultMedecin = $resMedecinString->fetch();
                        $resPatientString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM patient WHERE idPatient = :idPatient");
                        $resPatientString->execute(array('idPatient' => $data['idPatient']));
                        $resultPatient = $resPatientString->fetch();
                        $medecinString = $resultMedecin[2]." ".$resultMedecin[0]." ".$resultMedecin[1];
                        $patientString = $resultPatient[2]." ".$resultPatient[0]." ".$resultPatient[1];
                ?>

                    <tr>
                        <td><?php echo date("d/m/Y", strtotime($data['dateRDV'])) ?></td>
                        <td><?php echo $data['heureRDV'] ?></td>
                        <td><?php echo (intdiv($data['duree'], 60) == 0 ? "" : intdiv($data['duree'], 60)."h").($data['duree'] % 60 == 0 ? "" : ($data['duree'] % 60)."m"); ?></td>
                        <td><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']; ?>"><?php echo $medecinString ?></a></td>
                        <td><?php echo $patientString ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <?php include "footer.php";?>

    <script src="js/consultations.js"></script>
</body>
</html>