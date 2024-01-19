<?php
include 'getlinkpdo.php';

    // PATIENTS //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
                                  WHERE  numSecu = :numSecuP;');

        $req->execute(array(':numSecuP' => $PATIENT["numSecuP"]));
        if ($req->rowCount() > 0) {
            die("Numéro de sécurité déjà existant dans la base de données (Le patient existe déjà dans la base de données).<br>");
        }

        // Variables
        $nom        = $PATIENT["nomP"];
        $prenom     = $PATIENT["prenomP"];
        $civilite   = $PATIENT["civiliteP"];
        $adresse1   = $PATIENT["adresse1P"];
        $adresse2   = $PATIENT["adresse2P"];
        $ville      = $PATIENT["villeP"];
        $codePostal = $PATIENT["codePostalP"];
        $villeN     = $PATIENT["villeNP"];
        $dateN      = $PATIENT["dateNP"];
        $numSecu    = $PATIENT["numSecuP"];
        $idMedecin  = $PATIENT["medecinTraitantP"];

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
        $vide = array("civiliteP"        => "",
                      "nomP"             => "",
                      "prenomP"          => "",
                      "numSecuP"         => "",
                      "medecinTraitantP" => "",
                      "adresse1P"        => "",
                      "adresse2P"        => "",
                      "villeP"           => "",
                      "codePostalP"      => "",
                      "villeNP"          => "",
                      "dateNP"           => "",);

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
        $nom        = $PATIENT["nomP"];
        $prenom     = $PATIENT["prenomP"];
        $civilite   = $PATIENT["civiliteP"];
        $adresse1   = $PATIENT["adresse1P"];
        $adresse2   = $PATIENT["adresse2P"];
        $ville      = $PATIENT["villeP"];
        $codePostal = $PATIENT["codePostalP"];
        $villeN     = $PATIENT["villeNP"];
        $dateN      = $PATIENT["dateNP"];
        $numSecu    = $PATIENT["numSecuP"];
        $idMedecin  = $PATIENT["medecinTraitantP"];

        // Préparation
        $req = $linkpdo->prepare('UPDATE patient 
                                  SET    nom            = :nom, 
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
                                  WHERE  idPatient      = :idPatient;');

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



    // MEDECINS //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
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

        $req->execute(array(':nom' => $MEDECIN["nomM"], ':prenom' => $MEDECIN["prenomM"]));
        if ($req->rowCount() > 0) {
            die("Le médecin existe déjà dans la base de données.<br>");
        }
        
        // Variables
        $nom        = $MEDECIN["nomM"];
        $prenom     = $MEDECIN["prenomM"];
        $civilite   = $MEDECIN["civiliteM"];

        // Préparation
        $req = $linkpdo->prepare('INSERT INTO medecin(nom, prenom, civilite)
                                  VALUES(:nom, :prenom, :civilite)');

        // Requête
        $req->execute(array('nom' => $nom,
                            'prenom' => $prenom,
                            'civilite' => $civilite));
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
        $vide = array("civiliteM"        => "",
                      "nomM"             => "",
                      "prenomM"          => "",);

        return $vide;
    }


    /**********************************************
     * UPDATE MEDECIN
     * Modifie un medecin de la base de données
     * 
     * - Prend l'id et le $_POST de la page ajout en argument
     **********************************************/
    function updateMedecin($id, $MEDECIN) {
        global $linkpdo;

        // Variables
        $nom        = $MEDECIN["nomM"];
        $prenom     = $MEDECIN["prenomM"];
        $civilite   = $MEDECIN["civiliteM"];

        // Préparation
        $req = $linkpdo->prepare('UPDATE medecin 
                                  SET    nom            = :nom, 
                                         prenom         = :prenom, 
                                         civilite       = :civilite 
                                  WHERE  idMedecin      = :idMedecin;');

        // Requête
        $req->execute(array('nom'        => $nom,
                            'prenom'     => $prenom,
                            'civilite'   => $civilite,
                            'idMedecin'  => $id));
    }



    // CONSULTATIONS //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /**********************************************
     * ADD CONSULTATION
     * Ajoute une consultation à la base de données
     * 
     * - Prend le $_POST de la page ajout en argument
     **********************************************/
    function addConsultation($CONSULTATION) {
        global $linkpdo;
        
        // Variables
        $patient = $CONSULTATION["patientC"];
        $medecin = $CONSULTATION["medecinC"];
        $datec   = $CONSULTATION["dateC"];
        $heure   = $CONSULTATION["heureC"];
        $duree   = intval(substr($CONSULTATION["dureeC"], 0, 2)) * 60 + intval(substr($CONSULTATION["dureeC"], 3, 2));

        // Préparation
        $req = $linkpdo->prepare('INSERT INTO consultation(idPatient, dateRDV, heureRDV, duree, idMedecin)
                                  VALUES(:patient, :datec, :heure, :duree, :idMedecin)');

        // Requête
        $req->execute(array('patient'    => $patient,
                            'datec'      => $datec,
                            'heure'      => $heure,
                            'duree'      => $duree,
                            'idMedecin'  => $medecin,));
    }


    /**********************************************
     * GET CONSULTATION
     * Select * d'une consultation en ayant sa date, l'heure et le patient 
     * 
     * - Prend la date, l'heure et le patient en argument
     * - Renvoie un array avec toutes les données de la consultation
     **********************************************/
    function getConsultation($id) {
        global $linkpdo;
        $consultation = getConsultationVide();

        $patient = $id[0];
        $dateC   = $id[1];
        $heure   = substr($id[2], 0, 5);

        $req = $linkpdo->prepare('SELECT idPatient, idMedecin, dateRDV, heureRDV, duree 
                                  FROM   consultation 
                                  WHERE  idPatient   = :patient 
                                  AND    dateRDV     = :datec 
                                  AND    heureRDV    = :heure;');


        $req->execute(array('patient'  => $patient,
                            'datec'    => $dateC,
                            'heure'    => $heure));

        if ($req->rowCount() == 0 && $req->rowCount() > 1) {
            die("Erreur requête consultation.<br>");
        } else {
            $data = $req->fetch();
            $i = 0;

            foreach(array_keys($consultation) as &$key) {   // Return un array comprenant:
                $consultation[$key] = $data[$i];                    
                $i++;                                       // $consultation[0] =
            }                                               // $consultation[1] =    
                                                            // $consultation[2] = 

            $consultation['heureC'] = substr($consultation['heureC'], 0, 5);
            $consultation['dureeC'] = sprintf("%02d:%02d", intdiv($consultation['dureeC'], 60), $consultation['dureeC']%60);
            return $consultation;                     
        }

    }


    /**********************************************
     * GET CONSULTATION VIDE
     * Remplie un array avec du vide et les clés correspondantes
     * 
     * - Renvoie un array vide
     **********************************************/
    function getConsultationVide() {
        $vide = array("patientC" => "",
                      "medecinC" => "",
                      "dateC"    => "",
                      "heureC"   => "",
                      "dureeC"   => "",);

        return $vide;
    }

    /**********************************************
     * UPDATE CONSULTATION
     * Modifie une consultation de la base de données
     * 
     * - Prend l'id et le $_POST de la page ajout en argument
     **********************************************/
    function updateConsultation($id, $CONSULTATION) {
        global $linkpdo;

        // Variables
        $patient = $CONSULTATION["patientC"];
        $medecin = $CONSULTATION["medecinC"];
        $datec   = $CONSULTATION["dateC"];
        $heure   = $CONSULTATION["heureC"];
        $duree   = intval(substr($CONSULTATION["dureeC"], 0, 2)) * 60 + intval(substr($CONSULTATION["dureeC"], 3, 2));

        $oldPatient = $id[0];
        $oldDate    = $id[1];
        $oldHeure   = substr($id[2], 0, 5);


        // Préparation
        $req = $linkpdo->prepare('UPDATE consultation 
                                  SET    dateRDV     = :datec, 
                                         heureRDV    = :heure,
                                         duree       = :duree,
                                         idMedecin   = :idMedecin 
                                  WHERE  idPatient   = :oldPatient 
                                  AND    dateRDV     = :oldDate 
                                  AND    heureRDV    = :oldHeure;');

        // Requête
        $req->execute(array('datec'      => $datec,
                            'heure'      => $heure,
                            'duree'      => $duree,
                            'idMedecin'  => $medecin,
                            'oldPatient' => $oldPatient,
                            'oldDate'    => $oldDate,
                            'oldHeure'   => $oldHeure));
    }

?>