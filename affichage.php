<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Affichage</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
    <link rel="stylesheet" type="text/css" href="styles/okk.css">
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


    <main>
        <div id = "tabs">
            <button class="tablinks" onclick="showTab('Patient')" <?php echo $type=='patient' ? 'id=current': ''?>>Patient</button>
            <button class="tablinks" onclick="showTab('Medecin')" <?php echo $type=='medecin' ? 'id=current': ''?>>Medecin</button>
        </div>
        <h2 style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient">Liste des patients</h2>
        <h2 style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin">Liste des médecins</h2>

        <div style="display:<?php echo $type=='patient' ? 'block': 'none'?>" class="Patient" id="formPatient">

            <form class="research" onsubmit="return checkValidPatient(event, this)" method="post" action="affichage.php?type=patient">
                <div class="flex-research">
                    <div class="searchinput">
                        <label for="searchinput">Recherche avancé</label>
                        <input onclick="false" name="searchinput" id="searchinput" value="<?php echo (isset($_POST['rechercherPatient'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un patient ici (Optionnel)">
                    </div>

                    <div class="medecinTraitant">
                        <label for="medecinTraitant">Filtre médecin</label>
                        <select name="medecinTraitant" id="medecinTraitant">
                            <option>Indifférent</option>
                            <?php
                                try {
                                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                                } catch (Exception $e) {
                                    die('Erreur : ' . $e->getMessage());
                                }
                                $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite, idMedecin FROM medecin ORDER BY nom");
                                $resMedecinString->execute();

                                while ($data = $resMedecinString->fetch()) {
                                    $string = $data[0]." ".$data[1]." (".$data[2].")";
                                    $idMedecinT = $data[3];
                            ?>


                            <option value="<?php echo $idMedecinT?>" <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['medecinTraitant'] == $idMedecinT ? 'selected' : '') : '' ?>><?php echo $string; ?></option>

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
                        <select name="civilite" id="civilite">
                            <option>Indifférent</option>
                            <option <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
                            <option <?php echo (isset($_POST['rechercherPatient'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
                        </select>
                    </div>
                </div>

                <div class="submit">
                    <input type="submit" name="rechercherPatient" value="" class="btna blue" id="confirm">
                    <input type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
                </div>
            </form>




            <div class="liste-usagers">
            
            <?php

            if (isset($_GET['idMedecin'])) {
                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }

                $res = $linkpdo->prepare("SELECT * FROM patient WHERE idMedecin = :idMedecin");
                $res->execute(array('idMedecin' => $_GET['idMedecin']));

                if ($res->rowcount() == 0) {
                    ?>
                <p class="nbResultat">Aucun résultat</p>
                    <?php
                } else {
                    ?>
                <p class="nbResultat"><?php echo $res->rowcount() ?> résultat(s)</p>
                    <?php
                }
                    

                while ($data = $res->fetch()) {
                    $resMedecinString = $linkpdo->prepare("SELECT nom, prenom, civilite FROM medecin WHERE idMedecin = :idMedecin");
                    $resMedecinString->execute(array('idMedecin' => $data['idMedecin']));
                    $result = $resMedecinString->fetch();
                    $medecinString = $result[2]." ".$result[0]." ".$result[1];
            ?>

            <div>
                <div class="first-part">
                    <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                    <p class="adresse"><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
                    <p class="ville"><span class="label">Ville</span><?php echo $data['ville'] ?></p>
                    <p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
                    <p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
                    <p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']?>" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
                </div>
                <div class="second-part">
                    <button class="btna bluenoshadow">Modifier</button>
                    <button class="btna rednoshadow">Supprimer</button>
                </div>
            </div>


            <?php }}
            else if (isset($_POST['rechercherPatient'])) {
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

                

                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }
                $res = $linkpdo->prepare("SELECT * FROM patient".$where_requete." ORDER BY nom");
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

                ?>
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
                        <p class="adresse"><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
                        <p class="ville"><span class="label">Ville</span><?php echo $data['ville'] ?></p>
                        <p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
                        <p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
                        <p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']?>" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
                    </div>
                    <div class="second-part">
                        <button class="btna bluenoshadow">Modifier</button>
                        <button class="btna rednoshadow">Supprimer</button>
                    </div>
                </div>

            <?php
            }
            } else {
                ?>
                <p class="nbResultat">Voici un aperçu des 11 premiers patients du cabinet médical</p>
                <div id="createButton">
                    <a href="ajout.php?type=medecin" class="btna blue">
                        Ajouter un patient
                    </a>
                </div>
                <?php
                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                }
                catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }
                $res = $linkpdo->prepare("SELECT * FROM patient ORDER BY nom");
                $res->execute();

                $max11 = 0;

                while ($data = $res->fetch() and $max11 < 11) {
                    $max11++;
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
                        <p class="adresse"><span class="label">Adresse</span><?php echo $data['adresse1'] ?>;<br><?php echo $data['adresse2'] ?></p>
                        <p class="ville"><span class="label">Ville</span><?php echo $data['ville'] ?></p>
                        <p><span class="label">Code postal</span><?php echo $data['codePostal'] ?></p>
                        <p><span class="label">Numéro de sécu</span><?php echo $data['numSecu'] ?></p>
                        <p><span class="label">Médecin traitant</span><?php echo $medecinString  ?><?php if ($medecinString!="Aucun") { ?> <span class="detail">(</span><a href="affichage.php?type=medecin&id=<?php echo $data['idMedecin']?>" class="detail">voir fiche</a><span class="detail">)</span><?php }?></p>
                    </div>
                    <div class="second-part">
                        <button class="btna bluenoshadow">Modifier</button>
                        <button class="btna rednoshadow">Supprimer</button>
                    </div>
                </div>
                <?php
                }}
                ?>
            </div>
        </div>

        <div style="display:<?php echo $type=='medecin' ? 'block': 'none'?>" class="Medecin" id="formMedecin">
            <form class="research" onsubmit="return checkValidMedecin()" method="post" action="affichage.php?type=medecin">
                <div class="flex-research">
                    <div class="searchinput">
                        <label for="searchinput">Recherche avancé</label>
                        <input name="searchinput" id="searchinput" value="<?php echo (isset($_POST['rechercherMedecin'])) ? $_POST['searchinput'] : '' ?>" placeholder="Recherchez un médecin ici  (Optionnel)">
                    </div>

                    <div class="civilite">
                        <label for="civilite">Filtre civilité</label>
                        <select name="civilite" id="civilite">
                            <option>Indifférent</option>
                            <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'M.' ? 'selected' : '') : '' ?>>M.</option>
                            <option <?php echo (isset($_POST['rechercherMedecin'])) ? ($_POST['civilite'] == 'Mme' ? 'selected' : '') : '' ?>>Mme</option>
                        </select>
                    </div>
                </div>

                <div class="submit">
                    <input type="submit" name="rechercherMedecin" value="" class="btna blue" id="confirm">
                    <input type="submit" name="reset" value="" class="btna blue" id="reset" formnovalidate>
                </div>
            </form>


            <div class="liste-usagers">
                <?php

            if (isset($_GET['id'])) {
                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }

                $res = $linkpdo->prepare("SELECT * FROM medecin WHERE idMedecin = :idMedecin");
                $res->execute(array('idMedecin' => $_GET['id']));

                $data = $res->fetch();
                $resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
                $resCountPatientAssocie->execute(array('idMedecin' => $_GET['id']));
                $countPatient = $resCountPatientAssocie->fetch()[0];
            ?>

            <div>
                <div class="first-part">
                    <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                    <p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?type=patient&idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
                </div>
                <div class="second-part">
                    <button class="btna bluenoshadow">Modifier</button>
                    <button class="btna rednoshadow">Supprimer</button>
                </div>
            </div>


            <?php
        }

            
            else if (isset($_POST['rechercherMedecin'])) {
                $recherche = explode(" ", $_POST['searchinput']);

                $where_requete = "";
                $where_lst = array();
                foreach ($recherche as $key => $value) {
                    $where_requete .= (($key == 0) ? " WHERE (" : " OR")." nom LIKE :keyword$key OR prenom LIKE :keyword$key OR civilite LIKE :keyword$key";
                    $where_lst["keyword$key"] = "%$value%";
                }

                $where_requete .= ")";

                if ($_POST["civilite"] != "Indifférent") {
                    $where_requete .= " AND civilite = :civilite";
                    $where_lst["civilite"] = $_POST['civilite'];
                }

                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                } catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
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

                ?>
                <div id="createButton">
                    <a href="ajoutMedecin.php?type=medecin" class="btna blue">
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
                        <button class="btna rednoshadow">Supprimer</button>
                    </div>
                </div>

            <?php
            }
            } else {
                ?>
                <p class="nbResultat">Voici un aperçu des 11 premiers médecins du cabinet médical</p>
                <div id="createButton">
                    <a href="ajout.php" class="btna blue">
                        Ajouter un médecin
                    </a>
                </div>
                <?php
                try {
                    $linkpdo = new PDO("mysql:host=localhost;dbname=cabinet", 'root', '');
                }
                catch (Exception $e) {
                    die('Erreur : ' . $e->getMessage());
                }
                $res = $linkpdo->prepare("SELECT * FROM medecin ORDER BY nom");
                $res->execute();

                $max11 = 0;

                while ($data = $res->fetch() and $max11 < 11) {
                    $resCountPatientAssocie = $linkpdo->prepare("SELECT count(*) FROM patient WHERE idMedecin = :idMedecin");
                    $resCountPatientAssocie->execute(array('idMedecin' => $data['idMedecin']));
                    $countPatient = $resCountPatientAssocie->fetch()[0];

                    $max11++;
            ?>

                <div>
                    <div class="first-part">
                        <p class="name"><?php echo $data['civilite']." ".$data['nom']." ".$data['prenom'] ?></p>
                        <p class="countPatient"><span class="label">Patients attitrés</span><?php echo $countPatient ?><?php if ($countPatient != 0) {?> <span class="detail">(</span><a href="affichage.php?type=patient&idMedecin=<?php echo $data['idMedecin']?>" class="detail">voir la liste</a><span class="detail">)</span><?php }?></p>
                    </div>
                    <div class="second-part">
                        <button class="btna bluenoshadow">Modifier</button>
                        <button class="btna rednoshadow">Supprimer</button>
                    </div>
                </div>
                <?php
                }}
                ?>
            </div>
        </div>

        <div class=""></div>
    </main>

    <?php include "footer.php";?>

    <script src="js/affichage.js"></script>
    <script src = "js/ajout.js"></script>
</body>
</html>