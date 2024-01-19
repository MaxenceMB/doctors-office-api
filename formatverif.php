<?php

    /**********************************************
     * CHECK NOM
     * Vérifie qu'un nom (ou prénom) ne soit pas vide,
     * trop long ou contienne des caractères interdits.
     * 
     * - Prend la variable à vérifier (le nom ou prénom) en argument (+ son appélation pour l'affichage).
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkNom($nom, $nomVariable) {
        $nomspe = str_replace("-", "", str_replace(" ", "", $nom));                                           //////////////////////////
        if(strlen($nomspe) == 0) { return strtoupper($nomVariable)."_VIDE"; }                                 // Vide                 //
        if(strlen($nom) > 20) { return strtoupper($nomVariable)."_LONG"; }                                    // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $nom, $matches)) { return strtoupper($nomVariable)."_SPE"; }    // Caractères spéciaux  //
                                                                                                              //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK CIVILITE
     * Vérifie que la valeur civilité soit conforme (soit 'M' soit 'Mme').
     * 
     * - Prend la variable à vérifier (la civilité) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkCivilite($civilite) {                                                                  //////////////////////////
        if($civilite != "Mme" && $civilite != "M") { return "CIV_INV"; }                                 // Format invalide      //
        return "";                                                                                       //////////////////////////
    }


    /**********************************************
     * CHECK ADRESSE
     * Vérifie que l'adresse ne soit pas vide, trop
     * longue ou contienne des caractères interdits.
     * 
     * - Prend la variable à vérifier (l'adresse) en argument
     *   plus un 'booléen', vrai si c'est l'adresse principale et faux si c'est la complémentaire.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkAdresse($adresse, $primaire) {
        $adspe = str_replace("-", "", str_replace(" ", "", $adresse));                                   //////////////////////////
        if(strlen($adspe) == 0 && $primaire) { return "ADR_VIDE"; }                                      // Vide                 //
        if(strlen($adresse) > 30) { return "ADR_LONG"; }                                                 // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü0-9 -]+/', $adresse, $matches)) { return "ADR_SPE"; }              // Caractères spéciaux  //
                                                                                                         //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK VILLE
     * Vérifie que la ville spécifiée ne soit pas vide,
     * trop longue ou contienne des caractères interdits.
     * 
     * - Prend la variable à vérifier (la ville) en argument (+ son appélation pour l'affichage).
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkVille($ville, $nomVariable) {
        $villespe = str_replace("-", "", str_replace(" ", "", $ville));                                       //////////////////////////
        if($villespe == "") { return strtoupper($nomVariable)."_VIDE"; }                                      // Vide                 //
        if(strlen($ville) > 20) { return strtoupper($nomVariable)."_LONG"; }                                  // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $ville, $matches)) { return strtoupper($nomVariable)."_SPE"; }  // Caractères spéciaux  //
                                                                                                              //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK CODE POSTAL
     * Vérifie que la valeur du code postal soit conforme (5 chiffres).
     * 
     * - Prend la variable à vérifier (le code postal) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkCodePostal($cp) {                                                                      //////////////////////////
        if(strlen($cp) != 5) { return "CP_INV"; }                                                        // Taille invalide      //
        if(preg_match('/[^0-9]+/', $cp, $matches)) { return "CP_SPE"; }                                  // Pas des chiffres     //
                                                                                                         ////////////////////////// 
        return "";
    }


    /**********************************************
     * CHECK DATE NAISSANCE
     * Vérifie que la date de naissance soit entre
     * le 01/01/1900 et la date du jour.
     * 
     * - Prend la variable à vérifier (la date de naissance) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkDateNaissance($dateN) {                                                                //////////////////////////
        if(strlen($dateN) != 10) { return "DATE_INV"; }                                                  // Taille invalide      //
        if(preg_match('/[^0-9-]+/', $dateN, $matches)) { return "DATE_SPE"; }                            // Caractères spéciaux  //
        if($dateN > date("Y-m-d")) { return "DATE_SUP"; }                                                // Date trop élevée     //
        if($dateN < "1900-01-01") { return "DAT_INF"; }                                                  // Date trop ancienne   //
                                                                                                         //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK SECURITE
     * Vérifie que le numéro de sécurité sociale
     * soit bien une chaine de 15 chiffres.
     * 
     * - Prend la variable à vérifier (le numéro de sécu) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkSecurite($secu) {                                                                      //////////////////////////
        if(strlen($secu) != 15) { return "SECU_INV"; }                                                   // Taille invalide      //
        if(preg_match('/[^0-9]+/', $secu, $matches)) { return "SECU_SPE"; }                              // Pas des chiffres     //
                                                                                                         //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK ID
     * Vérifie que l'id indiqué soit valide, soit:
     * - égal à -1 pour aucun
     * - Ou juste existant
     * 
     * - Prend la variable à vérifier (l'id) en argument.
     * - Renvoie un string vide si tout va bien, code d'erreur sinon.
     **********************************************/
    function checkId($id, $table, $nullable) {
        include 'getlinkpdo.php';

        if($table == "patient") {
            $req = $linkpdo->prepare('SELECT * 
                                      FROM   patient  
                                      WHERE  idPatient = :id;');
        } else if($table == "medecin") {
            $req = $linkpdo->prepare('SELECT * 
                                      FROM   medecin 
                                      WHERE  idMedecin = :id;');
        } else {
            return "Err interne: Table invalide dans checkId()";
        }

        $aucun = ($id != -1);
        $aucunNonAutorise = !($aucun || ($aucun || $nullable));

        $req->execute(array(':id'  => $id));                                                             //////////////////////////
        if ($req->rowCount() == 0 && $aucunNonAutorise) {                                                // Introuvable & pas -1 //
            return strtoupper(substr($table, 0, 3))."_INV";                                              //////////////////////////
        }

        return "";
    }

    /**********************************************
     * CHECK DATE CONSULTATION
     * Vérifie que la date de la consultation soit supérieure ou égale à la date du jour 
     * 
     * - Prend la variable à vérifier (la date de la consultation) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkDateConsultation($dateN) {                                                             //////////////////////////
        if(strlen($dateN) != 10) { return "DATE_INV"; }                                                  // Taille invalide      //
        if(preg_match('/[^0-9-]+/', $dateN, $matches)) { return "DATE_SPE"; }                            // Caractères spéciaux  //
        if($dateN < date("Y-m-d")) { return "DATE_ANT"; }                                                // Date antérieure      //
                                                                                                         //////////////////////////
        return "";
    }


    /**********************************************
     * CHECK HEURE
     * Vérifie que la heure de la consultation soit supérieure à 8h
     * Et que la consultation ne dépasse pas 21h
     * 
     * - Prend la variable à vérifier (l'heure de la consultation) en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkHeure($heure, $duree) {                                                                //////////////////////////
        if(strlen($heure) != 5) { return "HR_INV"; }                                                     // Taille invalide      //
        if(preg_match('/[^0-9:]+/', $heure, $matches)) { return "HR_SPE"; }                              // Caractères spéciaux  //
                                                                                                         //                      //
        $heureMinutes = intval(substr($heure, 0, 2)) * 60 + intval(substr($heure, 3, 2));                //                      //
        $heureFin = $heureMinutes + intval(substr($duree, 0, 2)) * 60 + intval(substr($duree, 3, 2));    //                      //
                                                                                                         //                      //
        if($heureFin > 20 * 60) { return "HR_SUP"; }                                                     // Heure trop élevée    //
        if($heureMinutes < 8 * 60) { return "HR_INF"; }                                                  // Heure trop ancienne  //
                                                                                                         //////////////////////////
        return "";
    }


    /**********************************************
     * EST LIBRE
     * Vérifie que l'id indiqué ne soit pas pris par une autre consultation
     * 
     * - Prend la variable à vérifier (l'id) en argument.
     * - Renvoie un string vide si tout va bien, code d'erreur sinon.
     **********************************************/
    function estLibre($qui, $CONSULTATION) {
        include 'getlinkpdo.php';

        if($qui == "patient") {
            $req = $linkpdo->prepare('SELECT *
                                      FROM  consultation
                                      WHERE idPatient = :idPatient
                                      AND   dateRDV = :datec
                                      AND   ((:heurec BETWEEN heureRDV AND (heureRDV + duree - 1))
                                      OR    ((:heurec + :duree - 1) BETWEEN heureRDV AND (heureRDV + duree - 1))
                                      OR    (heureRDV BETWEEN :heurec AND (:heurec + :duree - 1)));');

            $req->execute(array(':idPatient'  => $CONSULTATION['patientC'],
                                ':datec'      => $CONSULTATION['dateC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':duree'      => $CONSULTATION['dureeC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':duree'      => $CONSULTATION['dureeC']));     

        } else if($qui == "medecin") {
            $req = $linkpdo->prepare('SELECT *
                                      FROM  consultation
                                      WHERE idMedecin = :idMedecin
                                      AND   dateRDV = :datec
                                      AND   ((:heurec BETWEEN heureRDV AND (heureRDV + duree - 1))
                                      OR    ((:heurec + :duree - 1) BETWEEN heureRDV AND (heureRDV + duree - 1))
                                      OR    (heureRDV BETWEEN :heurec AND (:heurec + :duree - 1)));');

            $req->execute(array(':idMedecin'  => $CONSULTATION['medecinC'],
                                ':datec'      => $CONSULTATION['dateC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':duree'      => $CONSULTATION['dureeC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':heurec'     => $CONSULTATION['heureC'],
                                ':duree'      => $CONSULTATION['dureeC']));     
                                      
        } else {
            return "Err interne: 'Qui' invalide dans estLibre()";
        }
                                                                                                         //////////////////////////
        if ($req->rowCount() > 0) {                                                                      // Déja pris            //
            return strtoupper(substr($qui, 0, 3))."_PRIS";                                               //////////////////////////
        }

        return "";
    }


    /**********************************************
     * CHECK PATIENT
     * Vérifie les formats du nom, prénom, civilité,
     * adresses, ville, code postal, ville et date de naissance,
     * numéro de sécu et l'id médecin traitant.
     * 
     * Pour ce faire, il effectue les tests sur tous les champs
     * les uns après les autres et renvoie le premier code d'erreur
     * rencontré. Si un string vide est renvoyé, ça veut dire que tout
     * est passé sans problème.
     * 
     * - Prend le $_POST de la page ajout en argument.
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon.
     **********************************************/
    function checkPatient($PATIENT) {
        return ((isset($PATIENT["civiliteP"]))        ? checkCivilite($PATIENT["civiliteP"])                    : "CIV_INV"    ) ?:
               ((isset($PATIENT["nomP"]))             ? checkNom($PATIENT["nomP"], "nom")                       : "NOM_INV"    ) ?:
               ((isset($PATIENT["prenomP"]))          ? checkNom($PATIENT["prenomP"], "prenom")                 : "PRENOM_INV" ) ?:
               ((isset($PATIENT["numSecuP"]))         ? checkSecurite($PATIENT["numSecuP"])                     : "SECU_INV"   ) ?:
               ((isset($PATIENT["medecinTraitantP"])) ? checkId($PATIENT["medecinTraitantP"], "medecin", true)  : ""           ) ?:
               ((isset($PATIENT["adresse1P"]))        ? checkAdresse($PATIENT["adresse1P"], true)               : "ADR_INV"    ) ?:
               ((isset($PATIENT["adresse2P"]))        ? checkAdresse($PATIENT["adresse2P"], false)              : ""           ) ?:
               ((isset($PATIENT["villeP"]))           ? checkVille($PATIENT["villeP"], "ville")                 : "VILLE_INV"  ) ?:
               ((isset($PATIENT["codePostalP"]))      ? checkCodePostal($PATIENT["codePostalP"])                : "CP_INV"     ) ?:
               ((isset($PATIENT["villeNP"]))          ? checkVille($PATIENT["villeNP"], "villen")               : "VILLEN_INV" ) ?:
               ((isset($PATIENT["dateNP"]))           ? checkDateNaissance($PATIENT["dateNP"])                  : "DATEN_INV"  );
    }


    /**********************************************
     * CHECK MEDECIN
     * Vérifie les formats du nom, prénom et la civilité.
     * 
     * Pour ce faire, il effectue les tests sur tous les champs
     * les uns après les autres et renvoie le premier code d'erreur
     * rencontré. Si un string vide est renvoyé, ça veut dire que tout
     * est passé sans problème.
     * 
     * - Prend le $_POST de la page ajout en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkMedecin($MEDECIN) {
        return ((isset($MEDECIN["civiliteM"])) ? checkCivilite($MEDECIN["civiliteM"])    : "CIV_INV"   ) ?:
               ((isset($MEDECIN["nomM"]))      ? checkNom($MEDECIN["nomM"], "nom")       : "NOM_INV"   ) ?:
               ((isset($MEDECIN["prenomM"]))   ? checkNom($MEDECIN["prenomM"], "prenom") : "PRENOM_INV");
    }


    /**********************************************
     * CHECK CONSULTATION
     * Vérifie les formats plus les créneau.
     * 
     * Pour ce faire, il effectue les tests sur tous les champs
     * les uns après les autres et renvoie le premier code d'erreur
     * rencontré. Si un string vide est renvoyé, ça veut dire que tout
     * est passé sans problème.
     * 
     * - Prend le $_POST de la page ajout en argument.
     * - Renvoie un string vide si tout va bien, un code d'erreur sinon.
     **********************************************/
    function checkConsultation($CONSULTATION) {
        return ((isset($CONSULTATION["patientC"])) ? checkId($CONSULTATION["patientC"], "patient", false) : "PAT_INV") ?:
               ((isset($CONSULTATION["medecinC"])) ? checkId($CONSULTATION["medecinC"], "medecin", false) : "MED_INV") ?:
               ((isset($CONSULTATION["dateC"]))    ? checkDateConsultation($CONSULTATION["dateC"]) : "DAT_INV") ?:
               ((isset($CONSULTATION["heureC"]) && isset($CONSULTATION["dureeC"])) ? checkHeure($CONSULTATION["heureC"], $CONSULTATION["dureeC"]) : "HR_INV" ) ?:

               (estLibre("patient", $CONSULTATION)) ?:
               (estLibre("medecin", $CONSULTATION));
    }

?>