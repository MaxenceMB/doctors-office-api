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
        $patient = getPatientVide();

        $req = $linkpdo->prepare('SELECT civilite, nom, prenom, numSecu, idMedecin, adresse1, adresse2, ville, codePostal, villeNaissance, dateNaissance 
                                  FROM   patient
                                  WHERE  idPatient = :id;');
                                                                
        $req->execute(array(':id' => $id));                     // Return un array comprenant:
        if ($req->rowCount() == 0 || $req->rowCount() > 1) {
            die("Erreur requête patient.<br>");                 // $patient[0]  = civilite
        } else {                                                // $patient[1]  = nom
            $data = $req->fetch();                              // $patient[2]  = prenom
            $i = 0;                                             // $patient[3]  = numSecu
                                                                // $patient[4]  = idMedecin
            foreach(array_keys($patient) as &$key) {            // $patient[5]  = adresse1
                $patient[$key] = $data[$i];                     // $patient[6]  = adresse2
                $i++;                                           // $patient[7]  = ville
            }                                                   // $patient[8]  = code postal
                                                                // $patient[9]  = ville naissance
            return $patient;                                    // $patient[10] = date naissance
        }

    }

    /**********************************************
     * GET PATIENT VIDE
     * Remplie un array avec du vide et les clés correspondantes
     * 
     * - Renvoie un array vide
     **********************************************/
    function getPatientVide() {
        $vide = array("civilite"        => "",
                      "nom"             => "",
                      "prenom"          => "",
                      "numSecu"         => "",
                      "medecinTraitant" => "",
                      "adresse1"        => "",
                      "adresse2"        => "",
                      "ville"           => "",
                      "codePostal"      => "",
                      "villeN"          => "",
                      "dateN"           => "",);

        return $vide;
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
        $medecin = getMedecinVide();

        $req = $linkpdo->prepare('SELECT civilite, nom, prenom 
                                  FROM   medecin
                                  WHERE  idMedecin = :id;');

        $req->execute(array(':id' => $id));
        if ($req->rowCount() == 0 && $req->rowCount() > 1) {
            die("Erreur requête médecin.<br>");
        } else {
            $data = $req->fetch();
            $i = 0;

            foreach(array_keys($medecin) as &$key) {    // Return un array comprenant:
                $medecin[$key] = $data[$i];                    
                $i++;                                   // $medecin[0] = civilite
            }                                           // $medecin[1] = nom      
                                                        // $medecin[2] = prenom
            return $medecin;                     
        }

    }


    /**********************************************
     * GET MEDECIN VIDE
     * Remplie un array avec du vide et les clés correspondantes
     * 
     * - Renvoie un array vide
     **********************************************/
    function getMedecinVide() {
        $vide = array("civilite"        => "",
                      "nom"             => "",
                      "prenom"          => "",);

        return $vide;
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
                            'idMedecin'  => ($idMedecin == -1) ? NULL : $idMedecin,
                            'idPatient'  => $id));
    }

?>