<?php
function checkNom($nom, $nomVariable) {
    $nomspe = str_replace("-", "", str_replace(" ", "", $nom));

    // Si le nom est vide
    if(strlen($nomspe) == 0) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_VIDE",
            "data"           => "Le ".$nomVariable." est vide."
        ];
    }      

    // Si le nom est trop long
    if(strlen($nom) > 20) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_LONG", 
            "data"           => "Le ".$nomVariable." est trop long."
        ];
    }       

    // Si le nom contient des caractères spéciaux
    if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $nom, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_SPE",
            "data"           => "Le ".$nomVariable." contient des caractères spéciaux et/ou nombres."
        ];
    }

    return "";
}


function checkCivilite($civilite) { 

    // Si le format de la civilité est incorrect
    if($civilite != "Mme" && $civilite != "M.") {
        return [
            "status_code"    => 400,
            "status_message" => "CIV_INV",
            "data"           => "Le format de la civilité est invalide. ('M.' et 'Mme' sont les deux valeurs acceptées)"
        ];
    }

    return "";
}


function checkSexe($sexe) { 

    // Si le format du sexe est incorrect
    if($sexe != "H" && $sexe != "F") {
        return [
            "status_code"    => 400,
            "status_message" => "SEX_INV",
            "data"           => "Le format du sexe est invalide. ('H' et 'F' sont les deux valeurs acceptées)"
        ];
    }

    return "";
}


function checkAdresse($adresse) {
    $adspe = str_replace("-", "", str_replace(" ", "", $adresse));

    // Si l'adresse est vide
    if(strlen($adspe) == 0) {
        return [
            "status_code"    => 400,
            "status_message" => "ADR_VIDE",
            "data"           => "L'adresse est vide."
        ];
    }      

    // Si l'adresse est trop longue
    if(strlen($adresse) > 50) {
        return [
            "status_code"    => 400,
            "status_message" => "ADR_LONG", 
            "data"           => "L'adresse est trop long."
        ];
    }

    return "";
}


function checkVille($ville, $nomVariable) {
    $villespe = str_replace("-", "", str_replace(" ", "", $ville));
    
    // Si la ville est vide
    if(strlen($villespe) == 0) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_VIDE",
            "data"           => "La ".str_replace("_", " ", $nomVariable)." vide."
        ];
    }      

    // Si la ville est trop long
    if(strlen($ville) > 30) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_LONG", 
            "data"           => "La ".str_replace("_", " ", $nomVariable)." est trop long."
        ];
    }       

    // Si la ville contient des caractères spéciaux
    if(preg_match('/[^a-zA-Zà-üÀ-Ü -]+/', $ville, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper($nomVariable)."_SPE",
            "data"           => "La ".str_replace("_", " ", $nomVariable)." contient des caractères spéciaux et/ou nombres."
        ];
    }

    return "";
}


function checkCodePostal($cp) {

    // Si le code postal ne fait pas 5 caractères de long
    if(strlen($cp) != 5) { 
        return [
            "status_code"    => 400,
            "status_message" => "CP_INV",
            "data"           => "Le code postal n'est pas de la bonne taille."
        ];
    }

    // Si le code postal contient des caractères spéciaux
    if(preg_match('/[^0-9]+/', $cp, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "CP_SPE",
            "data"           => "Le code postal contient des caractères spéciaux."
        ];
    }

    return "";
}


function checkDateNaissance($dateN) {
    $realDate = DateTime::createFromFormat("d/m/Y", $dateN);
    $today = DateTime::createFromFormat("d/m/Y", date("d/m/Y"));
    $old = DateTime::createFromFormat("d/m/Y", "01/01/1900");

    if(!preg_match('/^([012]?[0-9]|3[0-1])\/+([0]?[0-9]|1[0-2])\/+[1-2][0-9][0-9][0-9]$/', $dateN, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "DATE_SPE",
            "data"           => "La date est invalide."
        ];
    }

    // Si la date est supérieure à aujourd'hui
    if($realDate > $today) {
        return [
            "status_code"    => 400,
            "status_message" => "DATN_SUP",
            "data"           => "La date de naissance est supérieure à la date du jour."
        ];
    }  

    // Si la date est inferieure au 01/01/1900
    if($realDate < $old) {
        return [
            "status_code"    => 400,
            "status_message" => "DATN_INF",
            "data"           => "La date de naissance est inférieure au 01/01/1900."
        ];
    }  

    return "";
}


function checkSecurite($secu) {

    // Si le numéro de sécurité sociale a une taille anormale (000000000000000 = 15 caractères)
    if(strlen($secu) != 15) {
        return [
            "status_code"    => 400,
            "status_message" => "SECU_INV",
            "data"           => "Le numéro de sécurité sociale est invalide."
        ];
    }

    // Si le numéro de sécurité sociale contient des caractères spéciaux
    if(preg_match('/[^0-9]+/', $secu, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "SECU_SPE",
            "data"           => "Le numéro de sécurité sociale contient des caractères spéciaux."
        ];
    }  
    
    return "";
}

function checkId($pdo, $id, $table) {

    $requete = "SELECT * FROM ".$table." WHERE id_".$table." = :id";
    $req = $pdo->prepare($requete);
    $args = ["id" => $id];

    $req->execute($args);

    // Si il n'y a aucun résultat avec cet id
    if ($req->rowCount() == 0) {
        return [
            "status_code"    => 400,
            "status_message" => strtoupper(substr($table, 0, 2))."_INV",
            "data"           => "Un ".$table." avec cet id n'a pas été trouvé."
        ];
    }

    return "";
}


function checkDateConsultation($dateC) {
    $realDate = DateTime::createFromFormat("d/m/Y", $dateC);
    $today = DateTime::createFromFormat("d/m/Y", date("d/m/Y"));  

    if(!preg_match('/^([012]?[0-9]|3[0-1])\/+([0]?[0-9]|1[0-2])\/+[1-2][0-9][0-9][0-9]$/', $dateC, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "DATE_SPE",
            "data"           => "La date est invalide."
        ];
    }

    if($realDate < $today) {
        return [
            "status_code"    => 400,
            "status_message" => "DATE_ANT",
            "data"           => "La date est antérieure à la date du jour."
        ];
    }

    return "";
}


function checkHeure($heure, $duree) {

    // Si la taille de l'heure est anormale (00:00 = 5 caractères)
    if(strlen($heure) != 5) {
        return [
            "status_code"    => 400,
            "status_message" => "HR_INV",
            "data"           => "L'heure est invalide."
        ];
    }  

    if(!preg_match('/^([01]?[0-9]|2[0-3])\:+[0-5][0-9]$/', $heure, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "HR_SPE",
            "data"           => "L'heure est invalide."
        ];
    }

    if(preg_match('/[^0-9]+/', $duree, $matches)) {
        return [
            "status_code"    => 400,
            "status_message" => "DR_SPE",
            "data"           => "La durée contient des caractères spéciaux."
        ];
    }

    $heureMinutes = intval(substr($heure, 0, 2)) * 60 + intval(substr($heure, 3, 2));
    $heureFin = $heureMinutes + intval($duree);

    if($heureFin > 20 * 60) {
        return [
            "status_code"    => 400,
            "status_message" => "HR_SUP",
            "data"           => "Le rendez-vous dépasse 20h."
        ];
    }  


    if($heureMinutes < 8 * 60) {
        return [
            "status_code"    => 400,
            "status_message" => "HR_INF",
            "data"           => "L'heure est inférieure à 8h."
        ];
    }  

    return "";
}


function toDatabaseFormat($prettyDate) : String {
    $values = explode('/', $prettyDate);
    $newDate = $values[2]."-".$values[1]."-".$values[0];
    return $newDate;
}

function toPrettyFormat($uglyDate) : String {
    $values = explode('-', $uglyDate);
    $newDate = $values[2]."/".$values[1]."/".$values[0];
    return $newDate;
}