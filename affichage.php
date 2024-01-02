<?php include "session.php";?>
<?php include 'getlinkpdo.php';?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affichage</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
</head>

<body>
    <?php 
    if (!isset($_GET['type'])) {
        // on aurait pu rediriger vers affichage.php?type=patient mais ca coutait 2 requête pour le même affichage, on va juste gérer le type dans une variable
        $type = 'patient';
    } elseif ($_GET['type'] == "patient" or $_GET['type'] == "medecin") {
        $type = $_GET['type'];
    } else {
        // redirection si l'utilisateur entre manuellement (pas possible autrement) un type autre que médecin/patient
        header("Location: affichage.php?type=patient");
    }
    ?>

    <?php include "header.php";?>



    <div id="suppression">
        <p>Voulez-vous vraiment supprimer cette personne ?</p>
        <div>
            <button onclick="annulationSuppression(this)" class="btna rednoshadow">Non</button>
            <form method="GET" action="suppression.php"><input id="personneASupprimer" name="" type="hidden" value=""><input type="submit" value="Oui" class="btna greennoshadow"></form>
        </div>
    </div>

    <main>
        <!-- ----------------------------------------------------- -->
        <!-- PATIENT: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <section style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient path">
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
        </section>
        <!-- ----------------------------------------------------- -->
        <!-- MEDECIN: AFFICHAGE DE LA NAVIGATION ACTUEL DE LA PAGE -->
        <!-- ----------------------------------------------------- -->
        <section style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin path">
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
        </section>

        <!-- ----------------------------------------------------- -->
        <!-- CONTENU PRINCIPAL DE LA PAGE (Les deux onglets)       -->
        <!-- ----------------------------------------------------- -->
        <section class="mainSubject">
            <div id = "tabs">
                <button class="tablinks" onclick="showTab('Patient')" <?php echo $type=='patient' ? 'id=current': ''?>>Patient</button>
                <button class="tablinks" onclick="showTab('Medecin')" <?php echo $type=='medecin' ? 'id=current': ''?>>Medecin</button>
            </div>
            <h2 style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient">Liste des patients</h2>
            <h2 style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin">Liste des médecins</h2>


            <!-- ----------------------------------------------------- -->
            <!-- PATIENT: ONGLET PATIENT                               -->
            <!-- ----------------------------------------------------- -->
            <div style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient" id="formPatient">
                <!-- ----------------------------------------------------- -->
                <!-- PATIENT: FORMULAIRE DE RECHERCHE                      -->
                <!-- ----------------------------------------------------- -->
                <form class="research" onsubmit="return checkValidPatient()" method="post" action="affichage.php?type=patient">
                    <div class="flex-research">
                        <div class="searchinput">
                            <label for="searchinput">Recherche avancé</label>
                            <input onclick="false" name="searchinput" id="searchinputPatient" value="<?php echo (isset($_POST['rechercherPatient'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un patient ici (Optionnel)">
                        </div>

                        <div class="medecinTraitant">
                            <label for="medecinTraitant">Filtre médecin</label>
                            <select name="medecinTraitant" id="medecinTraitant">
                                <option>Indifférent</option>
                                <?php
                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[0]." ".$data[1]." (".$data[2].")";
                                        $idMedecinT = $data[3];
                                ?>


                                <option value="<?php echo $idMedecinT?>" <?php echo isset($_POST['rechercherPatient']) ? ($_POST['medecinTraitant'] == $idMedecinT ? 'selected' : '') : (isset($_GET['idMedecin']) ? ($_GET['idMedecin'] == $idMedecinT ? 'selected' : '') : ''); ?>><?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="toulouse">
                            <label for="toulouse">Filtre Toulouse</label>
                            <select name="toulouse" id="toulouse">
                                <option>Indifférent</option>
                                <option value="toulouse" <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['toulouse'] == 'toulouse' ? 'selected' : '') : '' ?>>Toulouse</option>
                            </select>
                        </div>

                        <div class="civilite">
                            <label for="civilite">Filtre civilité</label>
                            <select name="civilite" id="civilitePatient">
                                <option>Indifférent</option>
                                <option <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
                                <option <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
                            </select>
                        </div>
                    </div>

                    <div class="submit">
                        <!-- lorsque l'on clique sur "voir la liste" dans un médecin de l'onglet médecin -->
                        <input <?php echo isset($_GET['idMedecin']) ? "id=openFormImmediatly" : "" ?> onclick="fromButtonSearch=true;" type="submit" name="rechercherPatient" value="" class="btna blue" id="confirm">
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
                if (isset($_POST['rechercherPatient'])) {
                    $recherche = explode(" ", $_POST['searchinput']);

                    $where_requete = "";
                    $where_lst = array();
                    foreach ($recherche as $key => $value) {
                        $where_requete .= (($key == 0) ? " WHERE (" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key OR adresse1 LIKE :keyword$key OR adresse2 LIKE :keyword$key OR ville LIKE :keyword$key OR codePostal LIKE :keyword$key OR numSecu LIKE :keyword$key";
                        $where_lst["keyword$key"] = "%$value%";
                    }
                    $where_requete .= ")";

                    if ($_POST["medecinTraitant"] != "Indifférent") {
                        $where_requete .= " AND idMedecin = :idMedecin";
                        $where_lst["idMedecin"] = $_POST['medecinTraitant'];
                    }

                    if ($_POST["toulouse"] != "Indifférent") {
                        $where_requete .= " AND lower(ville) = 'toulouse'";
                    }

                    if ($_POST["civilite"] != "Indifférent") {
                        $where_requete .= " AND civilite = :civilite";
                        $where_lst["civilite"] = $_POST['civilite'];
                    }

                    
                    $res = $linkpdo->prepare("SELECT * FROM patient".$where_requete." ORDER BY nom");
                    $res->execute($where_lst);

                    if ($res->rowcount() == 0) { ?>
                        <p class="nbResultat">Aucun résultat</p>
                    <?php } else { ?>
                        <p class="nbResultat"><?php echo $res->rowcount() ?> résultat(s)</p>
                    <?php }
                
                
                // ----------------------------------------------------------------------------------------------
                // PATIENT: CAS 3 : Premier arrivée sur la page (GET), on affiche un aperçu de la table patient
                // ----------------------------------------------------------------------------------------------
                } else {
                    // ----------------------------------------------------------------------------------------------
                    // PATIENT: CAS 3.1 : La requête GET provient d'une suppression d'un patient
                    // ----------------------------------------------------------------------------------------------
                    $res = $linkpdo->prepare("SELECT * FROM patient ORDER BY nom");
                    $res->execute();

                    if (isset($_GET['patientSuppr'])) {
                        if ($_GET['patientSuppr'] == "error") {
                    ?>
                    <p class="nbResultat nbResultatRed">❌ Une erreur s'est produite lors de la suppression du patient</p>
                    <?php 
                        } else {
                    ?>
                    <p class="nbResultat nbResultatGreen">✔️ Le patient a bien été supprimé</p>
                    <?php 
                    }} else {
                    ?>
                    <p class="nbResultat">Voici la liste des <?php echo $res->rowcount() ?> patients du cabinet médical</p>
                <?php }
                } ?>



                <!-- DANS LES 2 CAS -->
                    <div id="createButton">
                        <a href="ajout.php?type=patient" class="btna blue">
                            Ajouter un patient
                        </a>
                    </div>
                    <?php

                    while ($data = $res->fetch()) {
                        if ($data['idMedecin'] != null) {
                            $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                            $resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
                            $result = $resMedecinString->fetch();
                            $medecinString = $result[2]." ".$result[0]." ".$result[1];
                        } else {
                            $medecinString = "Aucun";
                        }
                ?>

                    <div>
                        <div class="first-part">
                            <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                            <p><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
                            <p><span class="label">Ville</span><?php echo $data['ville'] ?></p>
                            <p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
                            <p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
                            <p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']?>" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
                        </div>
                        <div class="second-part">
                            <button class="btna bluenoshadow">Modifier</button>
                            <button onclick="deletePatient(this)" data-patient-id="<?php echo $data['idPatient']; ?>" class="btna rednoshadow">Supprimer</button>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>














            <!-- ----------------------------------------------------- -->
            <!-- MEDECIN: ONGLET MEDECIN                               -->
            <!-- ----------------------------------------------------- -->
            <div style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin" id="formMedecin">
                <!-- ----------------------------------------------------- -->
                <!-- MEDECIN: FORMULAIRE DE RECHERCHE                      -->
                <!-- ----------------------------------------------------- -->
                <form class="research" onsubmit="return checkValidMedecin()" method="post" action="affichage.php?type=medecin">
                    <div class="flex-research">
                        <div class="searchinput">
                            <label for="searchinput">Recherche avancé</label>
                            <input name="searchinput" id="searchinputMedecin" value="<?php echo (isset($_POST['rechercherMedecin'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un médecin ici  (Optionnel)">
                        </div>

                        <div class="medecinTraitant">
                            <label for="medecinTraitant">Filtre médecin</label>
                            <select name="medecinTraitant" id="medecinTraitantMedecin">
                                <option>Indifférent</option>
                                <?php
                                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                    $resMedecinString->execute();

                                    while ($data = $resMedecinString->fetch()) {
                                        $string = $data[0]." ".$data[1]." (".$data[2].")";
                                        $idMedecinT = $data[3];
                                ?>


                                <option value="<?php echo $idMedecinT?>" <?php echo isset($_POST['rechercherMedecin']) ? ($_POST['medecinTraitant'] == $idMedecinT ? 'selected' : '') : (isset($_GET['id']) ? ($_GET['id'] == $idMedecinT ? 'selected' : '') : ''); ?>><?php echo $string; ?></option>

                                <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="civilite">
                            <label for="civilite">Filtre civilité</label>
                            <select name="civilite" id="civiliteMedecin">
                                <option>Indifférent</option>
                                <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
                                <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
                            </select>
                        </div>
                    </div>

                    <div class="submit">
                        <input onclick="fromButtonSearch=true;" <?php echo isset($_GET['id']) ? "id=openFormImmediatly" : "" ?> type="submit" name="rechercherMedecin" value="" class="btna blue" id="confirm">
                        <input onclick="fromButtonSearch=false;" type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
                    </div>
                </form>

                <!-- ----------------------------------------------------- -->
                <!-- MEDECIN: LISTE DES MEDECINS EN RESULTAT DE RECHERCHE  -->
                <!-- ----------------------------------------------------- -->
                <div class="liste-usagers">

                <?php
                // ----------------------------------------------------------------------------------------------
                // MEDECIN: CAS 2 : On fait une recherche (POST) dans l'onglet médecin
                // ----------------------------------------------------------------------------------------------
                if (isset($_POST['rechercherMedecin'])) {
                    $recherche = explode(" ", $_POST['searchinput']);

                    $where_requete = "";
                    $where_lst = array();
                    foreach ($recherche as $key => $value) {
                        $where_requete .= (($key == 0) ? " WHERE (" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key";
                        $where_lst["keyword$key"] = "%$value%";
                    }

                    $where_requete .= ")";

                    if ($_POST["medecinTraitant"] != "Indifférent") {
                        $where_requete .= " AND idMedecin = :idMedecin";
                        $where_lst["idMedecin"] = $_POST['medecinTraitant'];
                    }

                    if ($_POST["civilite"] != "Indifférent") {
                        $where_requete .= " AND civilite = :civilite";
                        $where_lst["civilite"] = $_POST['civilite'];
                    }

                    $res = $linkpdo->prepare("SELECT * FROM medecin".$where_requete." ORDER BY nom");
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
                // MEDECIN: CAS 3 : Premier arrivée sur la page (GET), on affiche un aperçu de la table médecin
                // ----------------------------------------------------------------------------------------------
                } else {
                    // ----------------------------------------------------------------------------------------------
                    // PATIENT: CAS 3.1 : La requête GET provient d'une suppression d'un patient
                    // ----------------------------------------------------------------------------------------------
                    $res = $linkpdo->prepare("SELECT * FROM medecin ORDER BY nom");
                    $res->execute();

                    if (isset($_GET['medecinSuppr'])) {
                        if ($_GET['medecinSuppr'] == "error") {
                    ?>
                    <p class="nbResultat nbResultatRed">❌ Une erreur s'est produite lors de la suppression du médecin</p>
                    <?php 
                } else {
                    ?>
                    <p class="nbResultat nbResultatGreen">✔️ Le médecin a bien été supprimé</p>
                    <?php 
                    }} else {
                    ?>
                    <p class="nbResultat">Voici la liste des <?php echo $res->rowcount() ?> médecins du cabinet médical</p>
                <?php } 
            } ?>


                <!-- DANS LES 2 CAS -->
                    <div id="createButton">
                        <a href="ajout.php?type=medecin" class="btna blue">
                            Ajouter un médecin
                        </a>
                    </div>
                    <?php
                    


                    while ($data = $res->fetch()) {
                        $resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
                        $resCountPatientAssocie->execute(array('idMedecin' => $data['idMedecin']));
                        $countPatient = $resCountPatientAssocie->fetch()[0];
                ?>

                    <div>
                        <div class="first-part">
                            <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                            <p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?type=patient&idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
                        </div>
                        <div class="second-part">
                            <button class="btna bluenoshadow">Modifier</button>
                            <button onclick="deleteMedecin(this)" data-patient-id="<?php echo $data['idMedecin']; ?>" class="btna rednoshadow">Supprimer</button>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <?php include "footer.php";?>

    <script src="js/affichage.js"></script>
    <script src="js/ajout.js"></script>
</body>
</html>