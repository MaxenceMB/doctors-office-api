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
     * CHECK ID MEDECIN
     * Vérifie que l'id médecin indiqué soit valide, soit:
     * - égal à -1 pour aucun
     * - Ou juste existant
     * 
     * - Prend la variable à vérifier (l'id médecin) en argument.
     * - Renvoie un string vide si tout va bien, des messages d'erreurs sinon.
     **********************************************/
    function checkIdMedecin($medecin) {
        include 'getlinkpdo.php';

        // Vérification si le medecin existe
        $req = $linkpdo->prepare('SELECT *
                                  FROM   medecin
                                  WHERE  idMedecin = :idMedecin;');

        $req->execute(array(':idMedecin' => $medecin));                                                  //////////////////////////
        if ($req->rowCount() == 0 && $medecin != -1) {                                                   // Introuvable & pas -1 //
            return "MED_INV";                                                                            //////////////////////////
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
        return ((isset($PATIENT["civilite"]))        ? checkCivilite($PATIENT["civilite"])         : "CIV_INV"    ) ?:
               ((isset($PATIENT["nom"]))             ? checkNom($PATIENT["nom"], "nom")            : "NOM_INV"    ) ?:
               ((isset($PATIENT["prenom"]))          ? checkNom($PATIENT["prenom"], "prenom")      : "PRENOM_INV" ) ?:
               ((isset($PATIENT["numSecu"]))         ? checkSecurite($PATIENT["numSecu"])          : "SECU_INV"   ) ?:
               ((isset($PATIENT["medecinTraitant"])) ? checkIdMedecin($PATIENT["medecinTraitant"]) : ""           ) ?:
               ((isset($PATIENT["adresse1"]))        ? checkAdresse($PATIENT["adresse1"], true)    : "ADR_INV"    ) ?:
               ((isset($PATIENT["adresse2"]))        ? checkAdresse($PATIENT["adresse2"], false)   : ""           ) ?:
               ((isset($PATIENT["ville"]))           ? checkVille($PATIENT["ville"], "ville")      : "VILLE_INV"  ) ?:
               ((isset($PATIENT["codePostal"]))      ? checkCodePostal($PATIENT["codePostal"])     : "CP_INV"     ) ?:
               ((isset($PATIENT["villeN"]))          ? checkVille($PATIENT["villeN"], "villen")    : "VILLEN_INV" ) ?:
               ((isset($PATIENT["dateN"]))           ? checkDateNaissance($PATIENT["dateN"])       : "DATEN_INV"  );
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
        return ((isset($MEDECIN["civilite"])) ? checkCivilite($MEDECIN["civilite"])    : "CIV_INV"   ) ?:
               ((isset($MEDECIN["nom"]))      ? checkNom($MEDECIN["nom"], "nom")       : "NOM_INV"   ) ?:
               ((isset($MEDECIN["prenom"]))   ? checkNom($MEDECIN["prenom"], "prenom") : "PRENOM_INV");
    }

?>