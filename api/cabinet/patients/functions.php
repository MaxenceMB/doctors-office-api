<?php

function isPostValid($data) {
    return !empty($data["civilite"]) 
        && !empty($data["nom"])
        && !empty($data["prenom"])
        && !empty($data["sexe"])
        && !empty($data["adresse"])
        && !empty($data["code_postal"])
        && !empty($data["ville"])
        && !empty($data["date_nais"])
        && !empty($data["lieu_nais"])
        && !empty($data["num_secu"])
        && !empty($data["id_medecin"]);
}

function isPatchValid($id, $data) {
    return !empty($id) && (!empty($data["civilite"]) 
                       ||  !empty($data["nom"])
                       ||  !empty($data["prenom"])
                       ||  !empty($data["sexe"])
                       ||  !empty($data["adresse"])
                       ||  !empty($data["code_postal"])
                       ||  !empty($data["ville"])
                       ||  !empty($data["date_nais"])
                       ||  !empty($data["lieu_nais"])
                       ||  !empty($data["num_secu"])
                       ||  !empty($data["id_medecin"]));
}

function isPutValid($id, $data) {
    return !empty($id)
        && !empty($data["civilite"]) 
        && !empty($data["nom"])
        && !empty($data["prenom"])
        && !empty($data["sexe"])
        && !empty($data["adresse"])
        && !empty($data["code_postal"])
        && !empty($data["ville"])
        && !empty($data["date_nais"])
        && !empty($data["lieu_nais"])
        && !empty($data["num_secu"])
        && !empty($data["id_medecin"]);
}

function isDeleteValid($id) {
    return isset($id);
}


class Patient {

    // Templates de retour JSON pour les erreurs de PREPARE/EXECUTE qui sont forcément entièrement notre faute
    public const TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR = [
        "status_code"    => 500,
        "status_message" => "Internal Server Error : Un problème interne s'est produit.",
        "data"           => null
    ];

    public const TEMPLATE_403_ERROR = [
        "status_code"    => 403,
        "status_message" => "Forbidden : Problème interne côté client.",
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
        if (!$stmt->execute()) return Patient::TEMPLATE_403_ERROR;              // Erreur du execute()
    
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Tous les patients ont été reçus.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id) : array {
        
        // Requête
        $stmt = $pdo->prepare("SELECT * FROM usager WHERE id_usager = :id_usager");

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;            // Erreur du prepare()
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_403_ERROR;  // Erreur du execute()

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
        if (!isPostValid($data)) return Patient::TEMPLATE_400_BAD_REQUEST;
        
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
                 "date_nais"   => $data["date_nais"],
                 "lieu_nais"   => $data["lieu_nais"],
                 "num_secu"    => $data["num_secu"],
                 "id_medecin"  => $data["id_medecin"]];


        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute($args)) return Patient::TEMPLATE_403_ERROR;         // Erreur du execute()

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
        if (!isPatchValid($id, $data)) return Patient::TEMPLATE_400_BAD_REQUEST;
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
                $requestArray[$key] = $data[$key];
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
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;        // Erreur du prepare()
        if (!$stmt->execute($requestArray)) return Patient::TEMPLATE_403_ERROR;     // Erreur du execute()
        if ($stmt->rowcount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;         // Aucune ligne modifiée

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
        if (!isPutValid($id, $data)) return Patient::TEMPLATE_400_BAD_REQUEST;
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
                 "date_nais"   => $data["date_nais"],
                 "lieu_nais"   => $data["lieu_nais"],
                 "num_secu"    => $data["num_secu"],
                 "id_medecin"  => $data["id_medecin"],
                 "id_usager"   => $id];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute($args)) return Patient::TEMPLATE_403_ERROR;         // Erreur du execute()
        if ($stmt->rowcount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;     // Aucune ligne modifiée
        
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
        if (!isDeleteValid($id)) return Patient::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        // Requête
        $stmt = $pdo->prepare("DELETE FROM usager WHERE id_usager = :id_usager");
        
        // Début de la transaction
        $pdo->beginTransaction();
        
        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;            // Erreur du prepare()
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_403_ERROR;  // Erreur du execute()
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
}