<?php

    /**********************************************
     * CHECK NOM
     * Vérifie qu'un nom (ou prénom) ne soit pas vide,
     * trop long ou contienne des caractères interdits
     * 
     * - Prend la variable à vérifier (le nom ou prénom) en argument (+ son appélation pour l'affichage)
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkNom($nom, $nomVariable) {
        $result = "";

        $nomspe = str_replace("-", "", str_replace(" ", "", $nom));                                                                             //////////////////////////
        if(strlen($nomspe) == 0) { $result .= "Le ".$nomVariable." ne peut pas être vide.<br>"; }                                               // Vide                 //
        if(strlen($nom) > 20) { $result .= "Le ".$nomVariable." ne peut pas dépasser 20 caractères.<br>"; }                                     // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $nom, $matches)) { $result .= "Le ".$nomVariable." contient des charactères interdits.<br>"; }    // Caractères spéciaux  //
                                                                                                                                                //////////////////////////
        return $result;
    }


    /**********************************************
     * CHECK CIVILITE
     * Vérifie que la valeur civilité soit conforme (soit 'M' soit 'Mme')
     * 
     * - Prend la variable à vérifier (la civilité) en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkCivilite($civilite) {                                                                                                         //////////////////////////
        if($civilite != "Mme" && $civilite != "M") { return "La valeur civilité est invalide.<br>"; }                                           // Format invalide      //
        return "";                                                                                                                              //////////////////////////
    }


    /**********************************************
     * CHECK ADRESSE
     * Vérifie que l'adresse ne soit pas vide, trop
     * longue ou contienne des caractères interdits
     * 
     * - Prend la variable à vérifier (l'adresse) en argument
     *   plus un 'booléen', vrai si c'est l'adresse principale et faux si c'est la complémentaire
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkAdresse($adresse, $primaire) {
        $result = "";
        
        $adspe = str_replace("-", "", str_replace(" ", "", $adresse));                                                                          //////////////////////////
        if(strlen($adspe) == 0 && $primaire) { $result .= "L'adresse ne peut pas être vide.<br>"; }                                             // Vide                 //
        if(strlen($adresse) > 30) { $result .= "L'adresse ne peut pas dépasser 30 caractères.<br>"; }                                           // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü0-9 -]+/', $adresse, $matches)) { $result .= "L'adresse contient des charactères interdits.<br>"; }       // Caractères spéciaux  //
                                                                                                                                                //////////////////////////
        return $result;
    }


    /**********************************************
     * CHECK VILLE
     * Vérifie que la ville spécifiée ne soit pas vide,
     * trop longue ou contienne des caractères interdits
     * 
     * - Prend la variable à vérifier (la ville) en argument (+ son appélation pour l'affichage)
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkVille($ville, $nomVariable) {
        $result = "";

        $villespe = str_replace("-", "", str_replace(" ", "", $ville));                                                                         //////////////////////////
        if($villespe == "") { $result .= "La ".$nomVariable." ne peut pas être vide.<br>"; }                                                    // Vide                 //
        if(strlen($ville) > 20) { $result .= "La ".$nomVariable." ne peut pas dépasser 20 caractères.<br>"; }                                   // Trop long            //
        if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $ville, $matches)) { $result .= "La ".$nomVariable." contient des charactères interdits.<br>"; }  // Caractères spéciaux  //
                                                                                                                                                //////////////////////////
        return $result;
    }


    /**********************************************
     * CHECK CIVILITE
     * Vérifie que la valeur civilité soit conforme (soit 'M' soit 'Mme')
     * 
     * - Prend la variable à vérifier (la civilité) en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkCodePostal($cp) {
        $result = "";
                                                                                                                                                //////////////////////////
        if(strlen($cp) != 5) { $result .= "La taille du code postal est invalide.<br>"; }                                                       // Taille invalide      //
        if(preg_match('/[^0-9]+/', $cp, $matches)) { $result .= "Le code postal contient des charactères interdits.<br>"; }                     // Pas des chiffres     //
                                                                                                                                                //////////////////////////        
        return $result;
    }


    /**********************************************
     * CHECK DATE NAISSANCE
     * Vérifie que la date de naissance soit entre
     * le 01/01/1900 et la date du jour
     * 
     * - Prend la variable à vérifier (la date de naissance) en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkDateNaissance($dateN) {
        $result = "";
                                                                                                                                                //////////////////////////
        if(strlen($dateN) != 10) { $result .= "La taille de la date est invalide.<br>"; }                                                       // Taille invalide      //
        if(preg_match('/[^0-9-]+/', $dateN, $matches)) { $result .= "Le date contient des charactères interdits.<br>"; }                        // Caractères spéciaux  //
        if($dateN > date("Y-m-d")) { $result .= "La date est supérieure à la date du jour.<br>"; }                                              // Date trop élevée     //
        if($dateN < "1900-01-01") { $result .= "La date est inférieure a la date minimum.<br>"; }                                               // Date trop ancienne   //
                                                                                                                                                //////////////////////////
        return $result;
    }


    /**********************************************
     * CHECK SECURITE
     * Vérifie que le numéro de sécurité sociale
     * soit bien une chaine de 15 chiffres
     * 
     * - Prend la variable à vérifier (le numéro de sécu) en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkSecurite($secu) {
        $result = "";
                                                                                                                                                //////////////////////////
        if(strlen($secu) != 15) { $result .= "La taille du numéro de sécurité sociale est invalide.<br>"; }                                     // Taille invalide      //
        if(preg_match('/[^0-9]+/', $secu, $matches)) { $result .= "Le numéro de sécurité sociale contient des charactères interdits.<br>"; }    // Pas des chiffres     //
                                                                                                                                                //////////////////////////
        return $result;
    }


    /**********************************************
     * CHECK ID MEDECIN
     * Vérifie que l'id médecin indiqué soit valide, soit:
     * - égal à -1 pour aucun
     * - Ou juste existant
     * 
     * - Prend la variable à vérifier (l'id médecin') en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkIdMedecin($medecin) {
        include 'getlinkpdo.php';

        // Vérification si le medecin existe
        $req = $linkpdo->prepare('SELECT *
                                  FROM   medecin
                                  WHERE  idMedecin = :idMedecin;');

        $req->execute(array(':idMedecin' => $medecin));                                                                                         //////////////////////////
        if ($req->rowCount() == 0 && $medecin != -1) {                                                                                          // Introuvable & pas -1 //
            return "Le médecin n'existe pas.<br>";                                                                                              //////////////////////////
        }

        return "";
    }


    /**********************************************
     * CHECK PATIENT
     * Vérifie les formats du nom, prénom, civilité,
     * adresses, ville, code postal, ville et date de naissance,
     * numéro de sécu et l'id médecin traitant
     * 
     * Pour ce faire, il effectue les tests sur tous les champs
     * les uns après les autres et concatènes les messages d'erreurs
     * rencontrés. Si un string vide est renvoyé, ça veut dire que tout
     * est passé sans problème
     * 
     * - Prend le $_POST de la page ajout en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkPatient($PATIENT) {
        include 'getlinkpdo.php';

        $result = "";

        $result .= (isset($PATIENT["civilite"]))        ? checkCivilite($PATIENT["civilite"])                   : "La civilité n'a pas été renseignée.<br>";
        $result .= (isset($PATIENT["nom"]))             ? checkNom($PATIENT["nom"], "nom")                      : "Le nom n'a pas été renseigné.<br>";
        $result .= (isset($PATIENT["prenom"]))          ? checkNom($PATIENT["prenom"], "prénom")                : "Le prénom n'a pas été renseigné.<br>";
        $result .= (isset($PATIENT["numSecu"]))         ? checkSecurite($PATIENT["numSecu"])                    : "Le numéro de sécurité sociale n'a pas été renseignée.<br>";
        $result .= (isset($PATIENT["medecinTraitant"])) ? checkIdMedecin($PATIENT["medecinTraitant"])           : "";
        $result .= (isset($PATIENT["adresse1"]))        ? checkAdresse($PATIENT["adresse1"], true)              : "L'adresse n'a pas été renseignée.<br>";
        $result .= (isset($PATIENT["adresse2"]))        ? checkAdresse($PATIENT["adresse2"], false)             : "";
        $result .= (isset($PATIENT["ville"]))           ? checkVille($PATIENT["ville"], "ville")                : "La ville n'a pas été renseignée.<br>";
        $result .= (isset($PATIENT["codePostal"]))      ? checkCodePostal($PATIENT["codePostal"])               : "Le code postal n'a pas été renseigné.<br>";
        $result .= (isset($PATIENT["villeN"]))          ? checkVille($PATIENT["villeN"], "ville de naissance")  : "La ville de naissance n'a pas été renseignée.<br>";
        $result .= (isset($PATIENT["dateN"]))           ? checkDateNaissance($PATIENT["dateN"])                 : "La date de naissance n'a pas été renseignée.<br>";

        // Vérification si le patient existe déjà
        $req = $linkpdo->prepare('SELECT *
                                  FROM   patient
                                  WHERE  numSecu = :numSecu;');

        $req->execute(array(':numSecu' => $PATIENT["numSecu"]));
        if ($req->rowCount() > 0) {
            $result .= "Numéro de sécurité déjà existant dans la base de données (Le patient existe déjà dans la base de données).<br>";
        }

        return $result;
    }


    /**********************************************
     * CHECK MEDECIN
     * Vérifie les formats du nom, prénom et la civilité
     * 
     * Pour ce faire, il effectue les tests sur tous les champs
     * les uns après les autres et concatènes les messages d'erreurs
     * rencontrés. Si un string vide est renvoyé, ça veut dire que tout
     * est passé sans problème
     * 
     * - Prend le $_POST de la page ajout en argument
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon
     **********************************************/
    function checkMedecin($MEDECIN) {
        include 'getlinkpdo.php';

        $result = "";

        $result .= (isset($MEDECIN["civilite"])) ? checkCivilite($MEDECIN["civilite"])    : "La civilité n'a pas été renseignée.<br>";
        $result .= (isset($MEDECIN["nom"]))      ? checkNom($MEDECIN["nom"], "nom")       : "Le nom n'a pas été renseigné.<br>";
        $result .= (isset($MEDECIN["prenom"]))   ? checkNom($MEDECIN["prenom"], "prénom") : "Le prénom n'a pas été renseigné.<br>";

        // Vérification si le medecin existe déjà
        $req = $linkpdo->prepare('SELECT * 
                                  FROM   medecin 
                                  WHERE  LOWER(nom)    = LOWER(:nom)  
                                  AND    LOWER(prenom) = LOWER(:prenom);');

        $req->execute(array(':nom' => $MEDECIN["nom"], ':prenom' => $MEDECIN["prenom"]));
        if ($req->rowCount() > 0) {
            $result .= "Le médecin existe déjà dans la base de données.<br>";
        }

        return $result;
    }

?>