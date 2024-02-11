<?php include "session.php";?>
<?php include 'getlinkpdo.php';?>
<?php 
    if (!isset($_GET['type'])) {
        // on aurait pu rediriger vers affichage.php?type=patient mais ca coutait 2 requête pour le même affichage, on va juste gérer le type dans une variable
        $type = 'patient';
    } elseif ($_GET['type'] == "patient" or $_GET['type'] == "medecin" or $_GET['type'] == "consultation") {
        $type = $_GET['type'];
    } else {
        // redirection si l'utilisateur entre manuellement (pas possible autrement) un type autre que médecin/patient
        header("Location: ajout.php?type=patient");
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset = "utf-8" />
        <title>Ajout</title>
        <link rel = "stylesheet" href = "styles/styles.css">
        <link rel = "stylesheet" href = "styles/ajout.css">
    </head>

    <body>
        <?php include "header.php";?>

        <main>

            <?php

    include 'errorMessage.php';

    ?>

            <?php
                include "formatverif.php";
                include "gestionbd.php";

                // Initialisation des variables
                // Tous les inputs sont remplis avec les valeurs du patient, du médecin ou de la consultation qu'on souhaite modifier.
                // Mais si on ne modifie pas, juste la création pure et dure, on remplit tous les champs avec du vide.

                switch($type) {
                    case 'patient':
                        $champs = (isset($_GET['idModif'])) ? getPatient($_GET['idModif']) : getPatientVide();
                        break;

                    case 'medecin':
                        $champs = (isset($_GET['idModif'])) ? getMedecin($_GET['idModif']) : getMedecinVide();
                        break;

                    case 'consultation':
                        if(isset($_GET['idModif'])) {
                            $idConsultation = explode('|', $_GET['idModif']);
                            $champs = getConsultation($idConsultation);
                        } else {
                            $champs = getConsultationVide();
                        }
                        break;
                }


                // En fonction de quel bouton est cliqué, on fait des actions différentes
                if(isset($_POST["validerPatient"])) {

                    $err = checkPatient($_POST);
                    if($err != "") {
                        showMessage("messageError", $err);
                        $champs = array_slice($_POST, 0, count($_POST)-1);
                    } else {
                        if(isset($_GET['idModif'])) {
                            updatePatient($_GET['idModif'], $_POST);
                            $champs = getPatient($_GET['idModif']);
                            header("Location: affichage.php?type=patient");
                        } else {
                            addPatient($_POST);
                        }
                    }
                    
                } else if(isset($_POST["validerMedecin"])) {

                    $err = checkMedecin($_POST);
                    if($err != "") {
                        showMessage("messageError", $err);
                        $champs = array_slice($_POST, 0, count($_POST)-1);
                    } else {
                        if(isset($_GET['idModif'])) {
                            updateMedecin($_GET['idModif'], $_POST);
                            $champs = getMedecin($_GET['idModif']);
                            header("Location: affichage.php?type=medecin");
                        } else {
                            addMedecin($_POST);
                        }
                    }

                } else if(isset($_POST["validerConsultation"])) {


                    $err = checkConsultation($_POST, $champs);
                    if($err != "") {
                        showMessage("messageError", $err);
                        $champs = array_slice($_POST, 0, count($_POST)-1);
                    } else {
                        if(isset($_GET['idModif'])) {
                            updateConsultation($idConsultation, $_POST);
                            $champs = getConsultation($idConsultation);
                            header("Location: affichage.php?type=consultation");
                        } else {
                            addConsultation($_POST);
                        }
                    }
                }
            ?>

            <div id = "tabs">
                <button class = "tablinks" onclick = "showTab('Patient')"      <?php echo $type == 'patient'      ? 'id = current': ''?> style = "display:<?php echo isset($_GET['idModif']) ? 'none' : 'block'; ?>">Patient</button>
                <button class = "tablinks" onclick = "showTab('Medecin')"      <?php echo $type == 'medecin'      ? 'id = current': ''?> style = "display:<?php echo isset($_GET['idModif']) ? 'none' : 'block'; ?>">Medecin</button>
                <button class = "tablinks" onclick = "showTab('Consultation')" <?php echo $type == 'consultation' ? 'id = current': ''?> style = "display:<?php echo isset($_GET['idModif']) ? 'none' : 'block'; ?>">Consultation</button>
            </div>
            <div class = "tabHeader"> <h2 style = "display:<?php echo $type == 'patient'      ? 'block': 'none'?>" class = "Patient"     ><?php echo isset($_GET['idModif']) ? "Modification " : "Nouveau "; ?>patient</h2> </div>
            <div class = "tabHeader"> <h2 style = "display:<?php echo $type == 'medecin'      ? 'block': 'none'?>" class = "Medecin"     ><?php echo isset($_GET['idModif']) ? "Modification " : "Nouveau "; ?>medecin</h2> </div>
            <div class = "tabHeader"> <h2 style = "display:<?php echo $type == 'consultation' ? 'block': 'none'?>" class = "Consultation"><?php echo isset($_GET['idModif']) ? "Modification " : "Nouveau "; ?>consultation</h2> </div>

            <!-- Formulaire principal d'ajout d'un patient -->
            <form method = "post" action = "ajout.php?type=patient<?php echo isset($_GET['idModif']) ? '&idModif='.$_GET['idModif'] : ''?>" style = "display:<?php echo $type == 'patient' ? 'block': 'none'?>" class = "Patient" id = "formPatient">
                <div class = "mainForm">
                    <div class = "formColumn shortDouble">
                        
                        <p class = "formHeader">Identité</p>

                        <div class = "formInput">
                            <div class = "formLabel">Civilité:</div>
                            <div class = "formDouble">
                                <label for = "madameP"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civiliteP" id = "madameP"   value = "Mme" checked = "<?php echo ($type == "patient" && $champs['civiliteP'] == "Mme") ? "checked" : "" ?>"> 
                                <label for = "monsieurP" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civiliteP" id = "monsieurP" value = "M"   checked = "<?php echo ($type == "patient" && $champs['civiliteP'] == "M"  ) ? "checked" : "" ?>"> <br>
                            </div>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "nomP">Nom:</label></div>
                            <input type = "text" name = "nomP" id = "nomP" class = "shortInput" value = "<?php echo ($type == "patient") ? $champs['nomP'] : "" ?>" > <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "prenomP" >Prénom:</label></div>
                            <input type = "text" name = "prenomP" id = "prenomP" class = "shortInput" value = "<?php echo ($type == "patient") ? $champs['prenomP'] : "" ?>" > <br>
                        </div> 

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "numSecuP">Sécurité Sociale:</label></div>
                            <input type = "text" name = "numSecuP" id = "numSecuP" class = "shortInput" value = "<?php echo ($type == "patient") ? $champs['numSecuP'] : "" ?>">
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "medecinTraitantP">Médecin traitant:</label></div>
                            <select name = "medecinTraitantP" id = "medecinTraitantP" class = "shortInput">
                                <option value = "-1">Aucun</option>
                                <?php

                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2]." ".$data[0]." ".$data[1];
                                        $idMedecinT = $data[3]; ?>
                                        <option value = "<?php echo $idMedecinT?>" <?php echo ($type == "patient" && $champs['medecinTraitantP'] == $data[3]) ? "selected" : "" ?>> <?php echo $string; ?> </option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class = "formColumn longDouble">

                        <p class = "formHeader">Lieu de résidence</p>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse1P">Adresse:</label></div>
                            <input type = "text" name = "adresse1P" id = "adresse1P" class = "longInput" value = "<?php echo ($type == "patient") ? $champs['adresse1P'] : "" ?>"> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse2P">Complément d'adresse:</label></div>
                            <input type = "text" name = "adresse2P" id = "adresse2P" class = "longInput" value = "<?php echo ($type == "patient") ? $champs['adresse2P'] : "" ?>"> <br>
                        </div>

                        <div class = "formDouble">
                            <div class = "formInput longDouble">
                                <div class = "formLabel"><label for = "villeP">Ville:</label></div>
                                <input type = "text" name = "villeP" id = "villeP" class = "shortInput" value = "<?php echo ($type == "patient") ? $champs['villeP'] : "" ?>">
                            </div>

                            <div class = "formInput shortDouble">
                                <div class = "formLabel formSecondLabel"><label for = "codePostalP">Code postal:</label></div>
                                <input type = "text" name = "codePostalP" id = "codePostalP" value = "<?php echo ($type == "patient") ? $champs['codePostalP'] : "" ?>">
                            </div>
                        </div>

                        <p class = "formHeader">Naissance</p>

                        <div class = "formDouble">
                            <div class = "formInput longDouble">
                                <div class = "formLabel"><label for = "villeNP">Ville:</label></div>
                                <input type = "text" name = "villeNP" id = "villeNP" class = "shortInput" value = "<?php echo ($type == "patient") ? $champs['villeNP'] : "" ?>">
                            </div>

                            <div class = "formInput shortDouble">
                                <div class = "formLabel formSecondLabel"><label for = "dateNP">Date:</label></div>
                                <input type = "date" id = "dateNP" name = "dateNP" value = "<?php echo ($type == "patient") ? $champs['dateNP'] : "" ?>"/>             
                            </div>
                        </div>
                        
                    </div> a
                </div>
                    
                <div class = "formButtons">
                    <input type = "reset"   name = "reset"          class="btna red">
                    <input type = "submit"  name = "validerPatient" class="btna green">
                </div>
            </form>

            <!-- Formulaire principal d'ajout d'un Medecin -->
            <form method = "post" action = "ajout.php?type=medecin<?php echo isset($_GET['idModif']) ? '&idModif='.$_GET['idModif'] : ''?>" style = "display:<?php echo $type == 'medecin' ? 'block': 'none'?>" class = "Medecin" id = "formMedecin">
                <div class = "mainForm">
                    <div class = "formInput">
                        <div class = "formLabel">Civilité:</div>
                        <div class = "formDouble">
                            <label for = "madameM"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civiliteM" id = "madameM"   value = "Mme" checked = "<?php echo ($type == "medecin" && $champs['civiliteM'] == "Mme") ? "checked" : "" ?>"> 
                            <label for = "monsieurM" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civiliteM" id = "monsieurM" value = "M"   checked = "<?php echo ($type == "medecin" && $champs['civiliteM'] == "M"  ) ? "checked" : "" ?>"> <br>
                        </div>
                    </div>

                    <div class = "formInput">
                        <div class = "formLabel"><label for = "nomM">Nom:</label></div>
                        <input type = "text" name = "nomM" id = "nomM" class = "shortInput" value = "<?php echo ($type == "medecin") ? $champs['nomM'] : "" ?>"> <br>
                    </div>

                    <div class = "formInput">
                        <div class = "formLabel"><label for = "prenomM" >Prénom:</label></div>
                        <input type = "text" name = "prenomM" id = "prenomM" class = "shortInput" value = "<?php echo ($type == "medecin") ? $champs['prenomM'] : "" ?>"> <br>
                    </div>    
                </div>
                
                <div class = "formButtons">
                    <div class="ma"><input type = "reset"   name = "reset"          class="btna red"></div>
                    <div class="ma"><input type = "submit"  name = "validerMedecin" class="btna green"></div>
                </div>
            </form>

            <!-- Formulaire principal de création d'une consultation -->
            <form method = "post" action = "ajout.php?type=consultation<?php echo isset($_GET['idModif']) ? '&idModif='.$_GET['idModif'] : ''?>" style = "display:<?php echo $type =='consultation' ? 'block': 'none'?>" class = "Consultation" id = "formConsultation">
                <div class = "mainForm">
                    <div class = "formColumn longDouble">

                        <p class = "formHeader">Personnes concernées</p>

                        <div class = "formInput">
                            <div class = "formLabel">
                                <label for = "patientC">Patient:</label>
                            </div>
                            <select onchange="setMedecinTraitantFromPatient(this)" name = "patientC" id = "patientC">
                                <option value = "-1">Aucun</option>
                                <?php
                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idPatient, idMedecin FROM patient ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2]." ".$data[0]." ".$data[1];
                                        $idPatientC = $data[3];
                                        $idMedecinT = $data[4];
                                ?>
                                <option data-idMedecin = "<?php echo $idMedecinT?>" value ="<?php echo $idPatientC?>" <?php echo ($type == "consultation" && $champs['patientC'] == $data[3]) ? "selected" : "" ?>> <?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel">
                                <label for = "medecinC">Médecin :</label>
                            </div>
                            <select name = "medecinC" id = "medecinC">
                                <option value = "-1">Aucun</option>
                                <?php
                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2]." ".$data[0]." ".$data[1];
                                        $idMedecin = $data[3];
                                ?>
                                <option value = "<?php echo $idMedecin?>" <?php echo ($type == "consultation" && $champs['medecinC'] == $data[3]) ? "selected" : "" ?>> <?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class = "formColumn shortDouble">

                        <p class = "formHeader">Créneau</p>

                        <div class = "formInput">
                            <label class = "formLabel" for = "dateC">Date</label>
                            <input class = "longInput" type = "date" id = "dateC" name = "dateC" min="2018-01-01" max="2030-12-31" value = "<?php echo ($type == "consultation") ? $champs['dateC'] : '' ?>"/>                     
                        </div>

                        <div class = "formDouble">
                            <div class = "formInput longDouble">
                                <label class = "formLabel" for = "heureC">Heure</label>
                                <input type = "time" id = "heureC" name = "heureC" value = "<?php echo ($type == "consultation") ? $champs['heureC'] : '' ?>"/>         
                            </div>

                            <div class = "formInput longDouble">
                                <label class = "formLabel formSecondLabel" for = "dureeC">Durée</label>
                                <input type = "time" id = "dureeC" name = "dureeC" value = "<?php echo ($type == "consultation") ? $champs['dureeC'] : '' ?>"/>                      
                            </div>
                        </div>
                    </div> 
                </div>
                    
                <div class = "formButtons">
                    <div class="ma"><input type = "reset"   name = "reset"               class="btna red"></div>
                    <div class="ma"><input type = "submit"  name = "validerConsultation" class="btna green"></div>
                </div>      
            </form>
        </main>

        <?php include "footer.html";?>

    <script src="js/tabsystem.js"></script>


        <script type="text/javascript">
    function setMedecinTraitantFromPatient(select) {
        let medecinLie = select.options[select.selectedIndex];
        console.log(medecinLie);
        let id = medecinLie.getAttribute('data-idmedecin');
        console.log(id);
        // Vérifie si patientLie est null ou une chaîne vide
        if (!id) {
            id = "-1";
        }

        console.log(id);
        document.getElementById("medecinC").value = id;
    }
</script>

    </body>
</html>