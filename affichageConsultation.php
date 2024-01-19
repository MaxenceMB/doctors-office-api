<!-- ----------------------------------------------------- -->
<!-- PATIENT: ONGLET PATIENT                               -->
<!-- ----------------------------------------------------- -->
<div style="display:<?php echo $type=='consultation' ? 'block': 'none'?>" class="Consultation" id="formConsultation">
    <!-- ----------------------------------------------------- -->
    <!-- PATIENT: FORMULAIRE DE RECHERCHE                      -->
    <!-- ----------------------------------------------------- -->
    <form class="research" onsubmit="return checkValidConsultation()" method="post" action="affichage.php?type=consultation">
        <div class="flex-research">
            <div>
                <label for="startDate">Depuis le</label>
                <input type="date" id="startDate" name="startDate" min="2023-01-01" max="2030-01-01" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['startDate'] : '' ?>"/>
            </div>

            <div>
                <label for="endDate">Jusqu'au</label>
                <input type="date" id="endDate" name="endDate" min="2023-01-01" max="2030-01-01" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['endDate'] : '' ?>"/>
            </div>

            <div>
                <label for="startHours">De</label>
                <input type="time" id="startHours" name="startHours" min="08:00" max="20:00" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['startHours'] : '' ?>"/>
            </div>

            <div>
                <label for="endHours">À</label>
                <input type="time" id="endHours" name="endHours" min="08:00" max="20:00" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['endHours'] : '' ?>"/>
            </div>

            <div>
                <label for="startDuree">Durée minimum</label>
                <select name="startDuree" id="startDuree">
                    <option>Indifférent</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['startDuree'] == '0 minute' ? 'selected' : '') : '' ?>>0 minute</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['startDuree'] == '30 minutes' ? 'selected' : '') : '' ?>>30 minutes</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['startDuree'] == '1 heure' ? 'selected' : '') : '' ?>>1 heure</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['startDuree'] == '1 heure et demi' ? 'selected' : '') : '' ?>>1 heure et demi</option>
                </select>
            </div>

            <div>
                <label for="endDuree">Durée maximum</label>
                <select name="endDuree" id="endDuree">
                    <option>Indifférent</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['endDuree'] == '30 minutes' ? 'selected' : '') : '' ?>>30 minutes</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['endDuree'] == '1 heure' ? 'selected' : '') : '' ?>>1 heure</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['endDuree'] == '1 heure et demi' ? 'selected' : '') : '' ?>>1 heure et demi</option>
                    <option <?php echo (isset($_POST['rechercherConsultation'])) ? ($_POST['endDuree'] == '2 heures' ? 'selected' : '') : '' ?>>2 heures</option>
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


                    <option value="<?php echo $idMedecinT?>" <?php echo isset($_POST['rechercherConsultation']) ? $_POST['medecinConsultation'] == $idMedecinT ? 'selected' : '' : ''; ?>><?php echo $string; ?></option>

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


                    <option value="<?php echo $idPatientT?>" <?php echo isset($_POST['rechercherConsultation']) ? $_POST['patientConsultation'] == $idPatientT ? 'selected' : '' : ''; ?>><?php echo $string; ?></option>

                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="submit">
            <input onclick="fromButtonSearch=true;" type="submit" name="rechercherConsultation" value="" class="btna blue" id="confirm">
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
    if (isset($_POST['rechercherConsultation'])) {
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
        <a href="ajout.php?type=consultation" class="btna blue">
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
          <th>Actions</th>
        </tr>
       </thead>
       <tbody>
    <?php

        while ($data = $res->fetch()) {
            try {
                $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                $resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
            } catch (Exception $e) {
                echo "Une erreur est survenue.";
            }
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

            <td><?php echo sprintf("%02dh%02dm", intdiv($data['duree'], 60), $data['duree']%60); ?></td>
            <td><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']; ?>"><?php echo $medecinString ?></a></td>
            <td><?php echo $patientString ?></td>
            <td>
                <button class="btna bluenoshadow inside-button-modifier"></button>
                <button onclick="deleteConsultation(this)" data-patient-id="<?php echo $data['idPatient']; ?>" data-medecin-id="<?php echo $data['idMedecin']; ?>" data-daterdv="<?php echo $data['dateRDV']; ?>" data-heurerdv="<?php echo $data['heureRDV']; ?>" class="btna rednoshadow inside-button-supprimer"></button>
            </td>
        </tr>
        <?php
        }
        ?>
        </tbody>
        </table>
    </div>
</div>