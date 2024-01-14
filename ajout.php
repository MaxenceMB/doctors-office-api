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
        <title>Ajout patient</title>
        <link rel = "stylesheet" href = "styles/styles.css">
        <link rel = "stylesheet" href = "styles/ajout.css">
    </head>

    <body>
        <?php include "header.php";?>

        <main>
            <?php
                include "formatverif.php";
                include "gestionbd.php";

                // Initialisation des variables
                // Tous les inputs sont remplis avec les valeurs du patient ou médecin qu'on souhaite modifier.
                // Mais si on ne modifie pas, juste la création pure et dure, on remplit tous les champs avec du vide.
                $patientModif = array_fill(0, 11, "");
                $medecinModif = array_fill(0, 3,  "");

                if(isset($_GET['idModif'])) {
                    $idModif = $_GET['idModif'];
                    switch($type) {
                        case 'patient':
                            $patientModif = getPatient($idModif);
                            break;

                        case 'medecin':
                            $medecinModif = getMedecin($idModif);
                            break;

                        case 'consultation':
                            //$consultation = getConsultation($idModif);
                            break;
                    }
                }

                // Ajout d'un patient
                if(isset($_POST["validerPatient"])) {
                    
                    $err = checkPatient($_POST);
                    if($err != "") {
                        die($err);
                    }
                    addPatient($_POST);

                } else if(isset($_POST["validerMedecin"])) {

                    $err = checkMedecin($_POST);
                    if($err != "") {
                        die($err);
                    }
                    addMedecin($_POST);
                }
            ?>

            <div id = "tabs">
                <button class = "tablinks" onclick = "showTab('Patient')"      <?php echo $type == 'patient'      ? 'id = current': ''?>>Patient</button>
                <button class = "tablinks" onclick = "showTab('Medecin')"      <?php echo $type == 'medecin'      ? 'id = current': ''?>>Medecin</button>
                <button class = "tablinks" onclick = "showTab('Consultation')" <?php echo $type == 'consultation' ? 'id = current': ''?>>Consultation</button>
            </div>
            <h2 style = "display:<?php echo $type == 'patient'      ? 'block': 'none'?>" class = "Patient">Nouveau patient</h2>
            <h2 style = "display:<?php echo $type == 'medecin'      ? 'block': 'none'?>" class = "Medecin">Nouveau medecin</h2>
            <h2 style = "display:<?php echo $type == 'consultation' ? 'block': 'none'?>" class = "Consultation">Nouvelle consultation</h2>

            <!-- Formulaire principal d'ajout d'un patient -->
            <form method = "post" action = "ajout.php?type=patient" style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class = "Patient" id = "formPatient">
                <div class = "mainForm">
                    <div class = "formColumn">

                        <p class = "formHeader">Patient</p>

                        <div class = "formInput">
                            <div class = "formLabel">Civilité:</div>
                            <label for = "madame"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civilite" id = "madame"   value = "Mme" checked = "<?php echo ($patientModif[3] == "Mme") ? "checked" : "" ?>"> 
                            <label for = "monsieur" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civilite" id = "monsieur" value = "M"   checked = "<?php echo ($patientModif[3] == "M")   ? "checked" : "" ?>"> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "nom">Nom:</label></div>
                            <input type = "text" name = "nom" id = "nom" class = "shortInput" value = "<?php echo $patientModif[1] ?>" > <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "prenom" >Prénom:</label></div>
                            <input type = "text" name = "prenom" id = "prenom" class = "shortInput" value = "<?php echo $patientModif[2] ?>" > <br>
                        </div> 

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "numSecu">Numéro de Sécurité Sociale:</label></div>
                            <input type = "text" name = "numSecu" id = "numSecu" class = "shortInput" value = "<?php echo $patientModif[10] ?>">
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "medecinTraitant">Médecin traitant:</label></div>
                            <select name = "medecinTraitant" id = "medecinTraitant" class = "shortInput">
                                <option value = "-1">Aucun</option>
                                <?php

                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2].". ".$data[0]." ".$data[1];
                                        $idMedecinT = $data[3]; ?>
                                        <option value = "<?php echo $idMedecinT?>" <?php echo ($patientModif[11] == $data[3]) ? "selected" : "" ?>> <?php echo $string; ?> </option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class = "formColumn">

                        <p class = "formHeader">Lieu de résidence</p>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse1">Adresse:</label></div>
                            <input type = "text" name = "adresse1" id = "adresse1" class = "longInput" value = "<?php echo $patientModif[4] ?>"> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse2">Complément d'adresse:</label></div>
                            <input type = "text" name = "adresse2" id = "adresse2" class = "longInput" value = "<?php echo $patientModif[5] ?>"> <br>
                        </div>

                        <div class = "formDoubleInput">
                            <div class = "formInput">
                                <div class = "formLabel"><label for = "ville">Ville:</label></div>
                                <input type = "text" name = "ville" id = "ville" class = "shortInput" value = "<?php echo $patientModif[6] ?>">
                            </div>

                            <div class = "formInput">
                                <div class = "formLabel formSecondLabel"><label for = "codePostal">Code postal:</label></div>
                                <input type = "text" name = "codePostal" id = "codePostal" value = "<?php echo $patientModif[7] ?>">
                            </div>
                        </div>

                        <p class = "formHeader">Naissance</p>

                        <div class = "formDoubleInput">
                            <div class = "formInput">
                                <div class = "formLabel"><label for = "villeN">Ville:</label></div>
                                <input type = "text" name = "villeN" id = "villeN" class = "shortInput" value = "<?php echo $patientModif[8] ?>">
                            </div>

                            <div class = "formInput">
                                <div class = "formLabel formSecondLabel"><label for = "dateN">Date:</label></div>
                                <input type = "date" id = "dateN" name = "dateN" min = "1900-01-01" value = "<?php echo $patientModif[9] ?>"/>             
                            </div>
                        </div>
                        
                    </div> 
                </div>
                    
                <div class = "formButtons">
                    <div class="ma"><input type = "reset"   name = "reset"          class="btna red"></div>
                    <div class="ma"><input type = "submit"  name = "validerPatient" class="btna green"></div>
                </div>      
            </form>

            <!-- Formulaire principal d'ajout d'un Medecin -->
            <form method = "post" action = "ajout.php?type=medecin" style = "display:<?php echo $type == 'medecin' ? 'block': 'none'?>" class = "Medecin" id = "formMedecin">
                <div class = "mainForm">
                    <div class = "formInput">
                        <div class = "formLabel">Civilité:</div>
                        <label for = "monsieur" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civilite" id = "monsieur" value = "M">
                        <label for = "madame"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civilite" id = "madame"   value = "Mme"> <br>
                    </div>
                    <div class = "formInput">
                        <div class = "formLabel"><label for = "nom">Nom:</label></div>
                        <input type = "text" name = "nom" id = "nom" class = "shortInput" value = ""> <br>
                    </div>
                    <div class = "formInput">
                        <div class = "formLabel"><label for = "prenom" >Prénom:</label></div>
                        <input type = "text" name = "prenom" id = "prenom" class = "shortInput" value = ""> <br>
                    </div>    
                </div>
                
                <div class = "formButtons">
                    <div class="ma"><input type = "reset"   name = "reset"          class="btna red"></div>
                    <div class="ma"><input type = "submit"  name = "validerMedecin" class="btna green"></div>
                </div>
            </form>

            <!-- Formulaire principal de création d'une consultation -->
            <form method = "post" action = "ajout.php?type=consultation" style="display:<?php echo $type =='consultation' ? 'block': 'none'?>" class = "Consultation" id = "formConsultation">
                <div class = "mainForm">
                    <div class = "formColumn" id = "firstColumn">
                        <div class = "formInput">
                            <div class = "formLabel"><label for = "patient">Patient:</label></div>
                            <select name="patient" id="patient">
                                <option>Aucun</option>
                                <?php
                                    try {
                                        $linkpdopdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                                    } catch (Exception $e) {
                                        die('Erreur : ' . $e->getMessage());
                                    }
                                    $resMedecinString = $linkpdopdo->prepare("SELECT nom, prenom, civilite, idPatient, idMedecin FROM patient ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2].". ".$data[0]." ".$data[1];
                                        $idPatientC = $data[3];
                                        $idMedecinT = $data[4];
                                ?>
                                <option data-idMedecin = "<?php echo $idMedecinT?>" value ="<?php echo $idPatientC?>"> <?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class = "formInput">
                            <div class = "formLabel"><label for = "medecin">Médecin :</label></div>
                            <select name="medecin" id="medecin">
                                <option>Aucun</option>
                                <?php
                                    try {
                                        $linkpdopdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                                    } catch (Exception $e) {
                                        die('Erreur : ' . $e->getMessage());
                                    }
                                    $resMedecinString = $linkpdopdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[2].". ".$data[0]." ".$data[1];
                                        $idMedecin = $data[3];
                                ?>
                                <option class = "longInput" value="<?php echo $idMedecin?>"> <?php echo $string; ?> </option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class = "formColumn">
                        <div class = "formInput">
                            <label class = "formLabel" for = "startDate">Date</label>
                            <input class = "longInput" type = "date" id="startDate" name="startDate" min="2018-01-01" max="2030-12-31" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['startDate'] : '' ?>"/>                     
                        </div>

                        <div class = "formDoubleInput">
                            <div class = "formInput">
                                <label class = "formLabel" for = "startDate">Heure</label>
                                <input type = "time" id="startHours" name="startHours" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['startHours'] : '' ?>"/>         
                            </div>

                            <div class = "formInput">
                                <label class = "formLabel formSecondLabel" for = "startDate">Durée</label>
                                <input type = "date" id="startDate" name="startDate" min="2018-01-01" max="2030-12-31" value="<?php echo (isset($_POST['rechercherConsultation'])) ? $_POST['startDate'] : '' ?>"/>                      
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

        <?php include "footer.php";?>
        <script src = "js/ajout.js"></script>
    </body>
</html>