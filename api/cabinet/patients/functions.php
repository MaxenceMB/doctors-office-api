<?php
include "../formats.php";
class Patient {

    // Templates de retour JSON pour les erreurs de PREPARE/EXECUTE qui sont forcément entièrement notre faute
    public const TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR = [
        "status_code"    => 500,
        "status_message" => "Internal Server Error : Un problème interne s'est produit.",
        "data"           => null
    ];

    public const TEMPLATE_400_BAD_REQUEST = [
        "status_code" => 400,
        "status_message" => "Bad request",
        "data"           => null
    ];

    public const TEMPLATE_404_NOT_FOUND = [
        "status_code"    => 404,
        "status_message" => "Not found : Le patient n'a pas été trouvé.",
        "data"           => null
    ];

    public static function getAll(PDO $pdo) : array {
        
        // Requête
        $stmt = $pdo->prepare("SELECT * FROM usager");

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute()) return Patient::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()
    
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Tous les patients ont été reçus.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)               // Il faudrait modifier l'affichage de la date que l'on reçoit mais ça demanderait un traitement
        ];                                                                      // supplémentaire inutile, car la date au format YYYY-MM-DD est compréhensible
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id) : array {
        
        // Requête
        $stmt = $pdo->prepare("SELECT * FROM usager WHERE id_usager = :id_usager");

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                  // Erreur du prepare()
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()

        // Prend le resultat de la reqête
        $usager = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si aucun médecin n'a été trouvé (réponse à la requête vide)
        if (!$usager) return Patient::TEMPLATE_404_NOT_FOUND;
        
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le patient a été reçu.",
            "data"           => $usager
        ];

        return $matchingData;
    }


    public static function create(PDO $pdo, $data) : array {

        // Vérifie que tous les champs ont bien été saisis
        $valide = isPostValid($pdo, $data);
        if(is_array($valide)) return $valide;

        // Vérifie si aucun patient avec ce numéro de sécu existe déjà
        $existe = Patient::alreadyExists($pdo, $data); 
        if($existe["status_code"] != 200) return $existe;
        
        // Requête
        $stmt = $pdo->prepare("INSERT INTO usager(civilite, nom, prenom, sexe, adresse, code_postal, ville, date_nais, lieu_nais, num_secu, id_medecin)
                               VALUES(:civilite, :nom, :prenom, :sexe, :adresse, :code_postal, :ville, :date_nais, :lieu_nais, :num_secu, :id_medecin);");
                            
        // Arguments de la requête
        $args = ["civilite"    => $data["civilite"],
                 "nom"         => $data['nom'],
                 "prenom"      => $data["prenom"],
                 "sexe"        => $data["sexe"],
                 "adresse"     => $data["adresse"],
                 "code_postal" => $data["code_postal"],
                 "ville"       => $data["ville"],
                 "date_nais"   => toDatabaseFormat($data["date_nais"]),
                 "lieu_nais"   => $data["lieu_nais"],
                 "num_secu"    => $data["num_secu"],
                 "id_medecin"  => $data["id_medecin"]];


        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute($args)) return Patient::TEMPLATE_400_BAD_REQUEST;   // Erreur du execute()

        // Fin de la transaction
        $newId = $pdo->lastInsertId();  // Récupération du l'ID du médecin inséré
        $pdo->commit();                 // Commit l'insertion dans la BD
        
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 201,
            "status_message" => "Le patient a bien été ajouté.",
            "data"           => Patient::getById($pdo, $newId)["data"]
        ];
    
        return $matchingData;
    }

    public static function partialEdit(PDO $pdo, $id, $data) : array {

        // Vérifie que l'id et qu'au moins un champ ont été saisis
        $valide = isPatchValid($pdo, $id, $data);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Tableau avec les noms des arguments possibles
        $columns = ["civilite", "nom", "prenom", "sexe", "adresse", "code_postal", "ville", "date_nais", "lieu_nais", "num_secu", "id_medecin"];

        // Requête
        $requestContent = "UPDATE usager SET ";
        $requestArray = [];

        // Pour chaque valeur dans $data correspondante au tableau des arguments possibles
        foreach ($columns as $key) {
            if (!empty($data[$key])) {
                $requestContent .= ($requestArray ? ", " : "") . "$key = :$key";
                $requestArray[$key] = ($key == "date_nais") ? toDatabaseFormat($data['date_nais']) : $data[$key];
            }
        }
        
        // Ajoute le WHERE à la fin
        $requestContent .= " WHERE id_usager = :id_usager";
        $requestArray["id_usager"] = $id;

        // Prépare la requête
        $stmt = $pdo->prepare($requestContent);

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (Patient::getById($pdo, $id)["status_code"] == 404) return Patient::TEMPLATE_404_NOT_FOUND;  // Aucun patient n'a cet id
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                            // Erreur du prepare()
        if (!$stmt->execute($requestArray)) return Patient::TEMPLATE_400_BAD_REQUEST;                   // Erreur du execute()

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData =  [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été modifié partiellement.",
            "data"           => Patient::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function completeEdit(PDO $pdo, $id, $data) : array {
        
        // Vérifie que tous les champs ont été renseignés (id + champs de la table)
        $valide = isPutValid($id, $data);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Requête
        $stmt = $pdo->prepare("UPDATE usager 
                               SET civilite    = :civilite,
                                   nom         = :nom,
                                   prenom      = :prenom,
                                   sexe        = :sexe,
                                   adresse     = :adresse,
                                   code_postal = :code_postal,
                                   ville       = :ville,
                                   date_nais   = :date_nais,
                                   lieu_nais   = :lieu_nais,
                                   num_secu    = :num_secu,
                                   id_medecin  = :id_medecin
                               WHERE id_usager = :id_usager");

        // Arguments
        $args = ["civilite"    => $data["civilite"],
                 "nom"         => $data['nom'],
                 "prenom"      => $data["prenom"],
                 "sexe"        => $data["sexe"],
                 "adresse"     => $data["adresse"],
                 "code_postal" => $data["code_postal"],
                 "ville"       => $data["ville"],
                 "date_nais"   => toDatabaseFormat($data["date_nais"]),
                 "lieu_nais"   => $data["lieu_nais"],
                 "num_secu"    => $data["num_secu"],
                 "id_medecin"  => $data["id_medecin"],
                 "id_usager"   => $id];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (Patient::getById($pdo, $id)["status_code"] == 404) return Patient::TEMPLATE_404_NOT_FOUND;  // Aucun patient n'a cet id
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                            // Erreur du prepare()
        if (!$stmt->execute($args)) return Patient::TEMPLATE_400_BAD_REQUEST;                           // Erreur du execute()
        
        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData =  [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été modifié entièrement.",
            "data"           => Patient::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function delete($pdo, $id) : array {

        // Vérifie que l'id a bien été saisi
        $valide = isDeleteValid($id);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Requête
        $stmt = $pdo->prepare("DELETE FROM usager WHERE id_usager = :id_usager");
        
        // Début de la transaction
        $pdo->beginTransaction();
        
        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;            // Erreur du prepare()
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()
        if ($stmt->rowCount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;             // Aucune ligne supprimée

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été supprimé.",
            "data"           => null
        ];

        return $matchingData;
    }


    public static function alreadyExists(PDO $pdo, $data) : array {

        // Requête qui vérifie si le patient existe déjà
        $stmt = $pdo->prepare("SELECT * 
                               FROM usager 
                               WHERE num_secu = :num_secu");

        // Arguments
        $args = ["num_secu" => $data['num_secu']];

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare()
        if (!$stmt->execute($args)) return Patient::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()

        // Prend le resultat de la requête, si vide c'est que la voie est libre
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$patient) {
            $matchingData =  [
                "status_code"    => 200,
                "status_message" => "Le patient n'existe pas.",
                "data"           => null
            ];
        } else {
            $matchingData = [
                "status_code"    => 403,
                "status_message" => "Un patient avec ce numéro de sécurité sociale existe déjà.",
                "data"           => $patient
            ];
        }

        return $matchingData;
    }
}

function isPostValid($pdo, $data) {
    $err = ((isset($data["civilite"]))    ? checkCivilite($data["civilite"])        : ["status_code" => 400, "status_message" => "CIV_INV.", "data" => "La civilité n'a pas été définie."]) ?:
           ((isset($data["nom"]))         ? checkNom($data["nom"], "nom")           : ["status_code" => 400, "status_message" => "NOM_INV.", "data" => "Le nom n'a pas été défini."]) ?:
           ((isset($data["prenom"]))      ? checkNom($data["prenom"], "prenom")     : ["status_code" => 400, "status_message" => "PRENOM_INV.", "data" => "La civilité n'a pas été définie."]) ?:
           ((isset($data["sexe"]))        ? checkSexe($data["sexe"])                : ["status_code" => 400, "status_message" => "SEX_INV.", "data" => "Le sexe n'a pas été défini."]) ?:
           ((isset($data["adresse"]))     ? checkAdresse($data["adresse"])          : ["status_code" => 400, "status_message" => "ADR_INV.", "data" => "L'adresse n'a pas été définie."]) ?:
           ((isset($data["code_postal"])) ? checkCodePostal($data["code_postal"])   : ["status_code" => 400, "status_message" => "CP_INV.", "data" => "Le code postal n'a pas été défini."]) ?:
           ((isset($data["ville"]))       ? checkVille($data["ville"], "ville")     : ["status_code" => 400, "status_message" => "VILLE_INV.", "data" => "La ville n'a pas été définie."]) ?:
           ((isset($data["date_nais"]))   ? checkDateNaissance($data["date_nais"])  : ["status_code" => 400, "status_message" => "DATN_INV.", "data" => "La date de naissance n'a pas été définie."]) ?:
           ((isset($data["lieu_nais"]))   ? checkVille($data["lieu_nais"], "ville_naissance") : ["status_code" => 400, "status_message" => "LIEU_INV.", "data" => "La ville naissance n'a pas été défini."]) ?:
           ((isset($data["num_secu"]))    ? checkSecurite($data["num_secu"])        : ["status_code" => 400, "status_message" => "SECU_INV.", "data" => "Le numéro de sécurité sociale n'a pas été défini."]) ?:
           ((isset($data["id_medecin"]))  ? checkId($pdo, $data["id_medecin"], "medecin") : "");
    
    return ($err == "") ? true : $err;
}

function isPatchValid($pdo, $id, $data) {

    $vide = empty($data["civilite"]) && empty($data["nom"]) && empty($data["prenom"]) && empty($data["sexe"]) && empty($data["adresse"]) && empty($data["code_postal"]) && empty($data["ville"]) && empty($data["date_nais"]) && empty($data["lieu_nais"]) && empty($data["num_secu"]) && empty($data["id_medecin"]);

    if($vide) {
        $err = ["status_code" => 400, "status_message" => "PATCH_INV.", "data" => "Aucun champ n'a été défini."];
    } else {
        $err = ((isset($data["civilite"]))    ? checkCivilite($data["civilite"])        : "") ?:
               ((isset($data["nom"]))         ? checkNom($data["nom"], "nom")           : "") ?:
               ((isset($data["prenom"]))      ? checkNom($data["prenom"], "prenom")     : "") ?:
               ((isset($data["sexe"]))        ? checkSexe($data["sexe"])                : "") ?:
               ((isset($data["adresse"]))     ? checkAdresse($data["adresse"])          : "") ?:
               ((isset($data["code_postal"])) ? checkCodePostal($data["code_postal"])   : "") ?:
               ((isset($data["ville"]))       ? checkVille($data["ville"], "ville")     : "") ?:
               ((isset($data["date_nais"]))   ? checkDateNaissance($data["date_nais"])  : "") ?:
               ((isset($data["lieu_nais"]))   ? checkVille($data["lieu_nais"], "ville_naissance") : "") ?:
               ((isset($data["num_secu"]))    ? checkSecurite($data["num_secu"])        : "") ?:
               ((isset($data["id_medecin"]))  ? checkId($pdo, $data["id_medecin"], "medecin") : "") ?:
               ((empty($id))                  ? ["status_code" => 400, "status_message" => "ID_INV.", "data" => "L'id n'a pas été défini'."] : "");
    }

    return ($err == "") ? true : $err;
}

function isPutValid($id, $data) {
    $post = isPostValid($data);
    if(is_array($post)) return $post;

    $err = !empty($id) ? "" : ["status_code" => 400, "status_message" => "ID_INV.", "data" => "L'id n'a pas été défini'."];
    return ($err == "") ? true : $err;
}

function isDeleteValid($id) {
    return isset($id) ? true : ["status_code" => 400, "status_message" => "ID_INV.", "data" => "L'id n'a pas été défini'."];
}