<?php 
    // Connexion au serveur MySQL
    try {
        $link = new PDO("mysql:host=localhost;dbname=cabinet", "root", '');
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
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
        <header id="header">
            <h3 id = "header-title">Cabinet médical de Mac-Sens et Haine-Zoo</h3>

            <nav>
                <ul>
                    <li id="currentPage"><a href="#">Patient</a></li>
                    <li><a href="affichageMedecin.php">Médecin</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <?php
                if(empty($_POST["nom"])) {
                    $nom  = "";
                } else {

                    // Valeurs du formulaire
                    $nom        = $_POST["nom"];
                    $prenom     = $_POST["prenom"];
                    $civilite   = $_POST["civilite"];
                    $adresse1   = $_POST["adresse1"];
                    $adresse2   = $_POST["adresse2"];            
                    $ville      = $_POST["ville"];
                    $codePostal = $_POST["codePostal"];
                    $numSecu    = $_POST["numSecu"];
        
                    // Vérification si le patient existe déjà
                    $req = $link->prepare('SELECT nom, prenom, ville
                                        FROM   patient
                                        WHERE  LOWER(numSecu) = LOWER(:numSecu);');
        
                    $req->execute(array(':numSecu' => $numSecu));
                    if($req->rowCount() > 0) {
                        die("Le patient existe déjà dans la base de données.");
                    }
        
                    // Préparation
                    $req = $link->prepare('INSERT INTO patient(nom, prenom, civilite, adresse1, adresse2, ville, codePostal, numSecu)
                                        VALUES(:nom, :prenom, :civilite, :adresse1, :adresse2, :ville, :codePostal, :numSecu)');
        
                    $req->execute(array('nom'        => $nom,
                                        'prenom'     => $prenom,
                                        'civilite'   => $civilite,
                                        'adresse1'   => $adresse1,
                                        'adresse2'   => $adresse2,
                                        'ville'      => $ville,
                                        'codePostal' => $codePostal,
                                        'numSecu'    => $numSecu));
                }
            ?>

            <div id = "tabs">
                <button class = "tablinks" onclick = "openTab(event, 'Patient')" id = "current">Patient</button>
                <button class = "tablinks" onclick = "openTab(event, 'Medecin')">Medecin</button>
            </div>
            <h2>Ajouter un patient</h2>

            <!-- Formulaire principal d'ajout d'un patient -->
            <form method = "post" action = "ajout.php">
                <div id = "Patient" class = "mainForm">
                    <div class = "formColumn">
                        <div class = "formInput">
                            <div class = "formLabel">Civilité:</div>
                            <label for = "monsieur" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civilite" id = "monsieur" value = "" required>
                            <label for = "madame"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civilite" id = "madame"   value = "" required> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "nom">Nom:</label></div>
                            <input type = "text" name = "nom" id = "nom" class = "shortInput" value = "" pattern = "\w{1, 20}" required> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "prenom" >Prénom:</label></div>
                            <input type = "text" name = "prenom" id = "prenom" class = "shortInput" value = "" pattern = "\w{1, 20}" required> <br>
                        </div> 

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "numSecu">Numéro de Sécurité Sociale:</label></div>
                            <input type = "text" name = "numSecu" id = "numSecu" class = "shortInput" value = "" required>
                        </div>
                    </div>

                    <div class = "formColumn">
                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse1">Adresse:</label></div>
                            <input type = "text" name = "adresse1" id = "adresse1" class = "longInput" value = "" required> <br>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "adresse2">Complément d'adresse:</label></div>
                            <input type = "text" name = "adresse2" id = "adresse2" class = "longInput" value = ""> <br>
                        </div>

                        <div class = "formDoubleInput">
                            <div class = "formInput">
                                <div class = "formLabel"><label for = "ville">Ville:</label></div>
                                <input type = "text" name = "ville" id = "ville" class = "shortInput" value = "" required>
                            </div>

                            <div class = "formInput">
                                <div class = "formLabel formSecondLabel"><label for = "codePostal">Code postal:</label></div>
                                <input type = "text" name = "codePostal" id = "codePostal" value = "" pattern = "\d{5,5}" required>
                            </div>
                        </div>

                        <div class = "formInput">
                            <div class = "formLabel"><label for = "medecin">Médecin traitant:</label></div>
                            <input type = "text" name = "medecin" id = "medecin" class = "longInput" value = "" required> <br>
                        </div>
                    </div>         
                </div>

                <div id = "Medecin" class = "mainForm">
                    <div class = "formInput">
                        <div class = "formLabel">Civilité:</div>
                        <label for = "monsieur" class = "formRadioLabel">Monsieur</label> <input type = "radio" name = "civilite" id = "monsieur" value = "" required>
                        <label for = "madame"   class = "formRadioLabel">Madame</label>   <input type = "radio" name = "civilite" id = "madame"   value = "" required> <br>
                    </div>
                    <div class = "formInput">
                        <div class = "formLabel"><label for = "nom">Nom:</label></div>
                        <input type = "text" name = "nom" id = "nom" class = "shortInput" value = "" pattern = "\w{1, 20}" required> <br>
                    </div>
                    <div class = "formInput">
                        <div class = "formLabel"><label for = "prenom" >Prénom:</label></div>
                        <input type = "text" name = "prenom" id = "prenom" class = "shortInput" value = "" pattern = "\w{1, 20}" required> <br>
                    </div>    
                </div>

                <div class = "formButtons">
                    <div class="ma"><input type = "reset"   name = "reset"   class="btna red"></div>
                    <div class="ma"><input type = "submit"  name = "valider" class="btna green"></div>
                </div>
            </form>
        </main>

        <footer>
            <div id = "footer-links">
                <a href = "affichage.php"   >Affichage</a>
                <a href = "ajout.php"       >Ajout</a>
                <a href = "modification.php">Modifier</a>
                <a href = "suppréssion.php"> Supprimer</a>
            </div> 
            
            <p>🄯 LOUIS Enzo & MAURY-BALIT Maxence</p>
        </footer> 
    </body>
</html>