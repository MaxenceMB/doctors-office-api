<?php
include 'getlinkpdo.php';

    /**********************************************
     * ADD PATIENT
     * Ajoute un patient à la base de données
     * 
     * - Prend le $_POST de la page ajout en argument
     **********************************************/
    function addPatient($PATIENT) {
        global $linkpdo;

        // Vérification si le patient existe déjà
        $req = $linkpdo->prepare('SELECT *
                                  FROM   patient
                                  WHERE  numSecu = :numSecu;');

        $req->execute(array(':numSecu' => $PATIENT["numSecu"]));
        if ($req->rowCount() > 0) {
            die("Numéro de sécurité déjà existant dans la base de données (Le patient existe déjà dans la base de données).<br>");
        }

        // Variables
        $nom        = $PATIENT["nom"];
        $prenom     = $PATIENT["prenom"];
        $civilite   = $PATIENT["civilite"];
        $adresse1   = $PATIENT["adresse1"];
        $adresse2   = $PATIENT["adresse2"];
        $ville      = $PATIENT["ville"];
        $codePostal = $PATIENT["codePostal"];
        $villeN     = $PATIENT["villeN"];
        $dateN      = $PATIENT["dateN"];
        $numSecu    = $PATIENT["numSecu"];
        $idMedecin  = $PATIENT["medecinTraitant"];

        // Préparation
        $req = $linkpdo->prepare('INSERT INTO patient(nom, prenom, civilite, adresse1, adresse2, ville, codePostal, villeNaissance, dateNaissance, numSecu, idMedecin)
                                  VALUES(:nom, :prenom, :civilite, :adresse1, :adresse2, :ville, :codePostal, :villeN, :dateN, :numSecu, :idMedecin)');

        // Requête
        $req->execute(array('nom'        => $nom,
                            'prenom'     => $prenom,
                            'civilite'   => $civilite,
                            'adresse1'   => $adresse1,
                            'adresse2'   => $adresse2,
                            'ville'      => $ville,
                            'codePostal' => $codePostal,
                            'villeN'     => $villeN,
                            'dateN'      => $dateN,
                            'numSecu'    => $numSecu,
                            'idMedecin'  => $idMedecin));
    }

    
    /**********************************************
     * ADD MEDECIN
     * Ajoute un médecin à la base de données
     * 
     * - Prend le $_POST de la page ajout en argument
     **********************************************/
    function addMedecin($MEDECIN) {
        global $linkpdo;

        // Vérification si le medecin existe déjà
        $req = $linkpdo->prepare('SELECT * 
                                  FROM   medecin 
                                  WHERE  LOWER(nom)    = LOWER(:nom)  
                                  AND    LOWER(prenom) = LOWER(:prenom);');

        $req->execute(array(':nom' => $MEDECIN["nom"], ':prenom' => $MEDECIN["prenom"]));
        if ($req->rowCount() > 0) {
            die("Le médecin existe déjà dans la base de données.<br>");
        }
        
        // Variables
        $nom        = $_POST["nom"];
        $prenom     = $_POST["prenom"];
        $civilite   = $_POST["civilite"];

        // Préparation
        $req = $linkpdo->prepare('INSERT INTO medecin(nom, prenom, civilite)
                                  VALUES(:nom, :prenom, :civilite)');

        // Requête
        $req->execute(array('nom' => $nom,
                            'prenom' => $prenom,
                            'civilite' => $civilite));
    }


    /**********************************************
     * GET PATIENT
     * Select * d'un patient en ayant son id
     * 
     * - Prend l'id du patient en argument
     * - Renvoie un array avec toutes les données du patient
     **********************************************/
    function getPatient($id) {
        global $linkpdo;

        $req = $linkpdo->prepare('SELECT *
                                  FROM   patient
                                  WHERE  idPatient = :id;');
                                                                // Return un array comprenant:
        $req->execute(array(':id' => $id));                     // $patient[0]  = idPatient
        if ($req->rowCount() == 0 || $req->rowCount() > 1) {    // $patient[1]  = nom
            die("Erreur requête patient.<br>");                 // $patient[2]  = prénom
        } else {                                                // $patient[3]  = civilité
            $data = $req->fetch();                              // $patient[4]  = adresse 1
            $patient = "";                                      // $patient[5]  = adresse 2
            for($i = 0; $i < 11; $i++) {                        // $patient[6]  = ville
                $patient .= $data[$i].", ";                     // $patient[7]  = code postal
            }                                                   // $patient[8]  = ville naissance
            $patient .= $data[11];                              // $patient[9]  = date naissance
                                                                // $patient[10] = numéro sécu
            return explode(", ", $patient);                     // $patient[11] = idMedecin
        }

    }


    /**********************************************
     * GET MEDECIN
     * Select * d'un médecin en ayant son id
     * 
     * - Prend l'id du médecin en argument
     * - Renvoie un array avec toutes les données du médecin
     **********************************************/
    function getMedecin($id) {
        global $linkpdo;

        $req = $linkpdo->prepare('SELECT *
                                  FROM   medecin
                                  WHERE  idMedecin = :id;');

        $req->execute(array(':id' => $id));
        if ($req->rowCount() == 0 && $req->rowCount() > 1) {
            die("Erreur requête médecin.<br>");
        } else {
            $data = $req->fetch();
            $medecin = "";
            for($i = 0; $i < 3; $i++) {
                $medecin .= $data[$i].", ";                     // Return un array comprenant:
            }                                                   // $medecin[0] = idPatient
            $medecin .= $data[3];                               // $medecin[1] = nom
                                                                // $medecin[2] = prénom
            return explode(", ", $medecin);                     // $medecin[3] = civilité
        }

    }


    /**********************************************
     * UPDATE PATIENT
     * Modifie un patient de la base de données
     * 
     * - Prend l'id et le $_POST de la page ajout en argument
     **********************************************/
    function updatePatient($id, $PATIENT) {
        global $linkpdo;

        // Variables
        $nom        = $PATIENT["nom"];
        $prenom     = $PATIENT["prenom"];
        $civilite   = $PATIENT["civilite"];
        $adresse1   = $PATIENT["adresse1"];
        $adresse2   = $PATIENT["adresse2"];
        $ville      = $PATIENT["ville"];
        $codePostal = $PATIENT["codePostal"];
        $villeN     = $PATIENT["villeN"];
        $dateN      = $PATIENT["dateN"];
        $numSecu    = $PATIENT["numSecu"];
        $idMedecin  = $PATIENT["medecinTraitant"];

        // Préparation
        $req = $linkpdo->prepare('UPDATE patient 
                                  SET nom            = :nom, 
                                      prenom         = :prenom, 
                                      civilite       = :civilite, 
                                      adresse1       = :adresse1, 
                                      adresse2       = :adresse2, 
                                      ville          = :ville, 
                                      codePostal     = :codePostal, 
                                      villeNaissance = :villeN, 
                                      dateNaissance  = :dateN, 
                                      numSecu        = :numSecu, 
                                      idMedecin      = :idMedecin
                                  WHERE idPatient = :idPatient;');

        // Requête
        $req->execute(array('nom'        => $nom,
                            'prenom'     => $prenom,
                            'civilite'   => $civilite,
                            'adresse1'   => $adresse1,
                            'adresse2'   => $adresse2,
                            'ville'      => $ville,
                            'codePostal' => $codePostal,
                            'villeN'     => $villeN,
                            'dateN'      => $dateN,
                            'numSecu'    => $numSecu,
                            'idMedecin'  => $idMedecin,
                            'idPatient'  => $id));
    }

?>