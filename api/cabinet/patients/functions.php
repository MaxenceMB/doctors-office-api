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
        && !empty($data["num_secu"]);
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
                       ||  !empty($data["num_secu"]));
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
        && !empty($data["num_secu"]);
}

function isDeleteValid($id) {
    return isset($id);
}


class Patient {

    // template de retour JSON pour les erreurs de PREPARE/EXECUTE qui sont forcément entièrement notre faute
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

    public static function getAll(PDO $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM usager");

        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        if (!$stmt->execute()) return Patient::TEMPLATE_403_ERROR;
    
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Tous les patients ont été reçus.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id):array {
        $stmt = $pdo->prepare("SELECT * FROM usager WHERE id_usager = :id_usager");

        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_403_ERROR;

        $usager = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usager) return Patient::TEMPLATE_404_NOT_FOUND;
        
        return [
            "status_code"    => 200,
            "status_message" => "Le patient a été reçu.",
            "data"           => $usager
        ];
    }


    public static function create(PDO $pdo, $data):array {
        if (!isPostValid($data)) return Patient::TEMPLATE_400_BAD_REQUEST;
        
        $stmt = $pdo->prepare("INSERT INTO usager(civilite, nom, prenom, sexe, adresse, code_postal, ville, date_nais, lieu_nais, num_secu)
                               VALUES(:civilite, :nom, :prenom, :sexe, :adresse, :code_postal, :ville, :date_nais, :lieu_nais, :num_secu);");
                               
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
    
        $args = ["civilite"    => $data["civilite"],
                 "nom"         => $data['nom'],
                 "prenom"      => $data["prenom"],
                 "sexe"        => $data["sexe"],
                 "adresse"     => $data["adresse"],
                 "code_postal" => $data["code_postal"],
                 "ville"       => $data["ville"],
                 "date_nais"   => $data["date_nais"],
                 "lieu_nais"   => $data["lieu_nais"],
                 "num_secu"    => $data["num_secu"]];

        if (!$stmt->execute($args)) return Patient::TEMPLATE_403_ERROR;

        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été ajouté.",
            "data"           => Patient::getById($pdo, $pdo->lastInsertId())["data"]
        ];
    
        return $matchingData;
    }

    public static function partialEdit(PDO $pdo, $id, $data) {
        if (!isPatchValid($id, $data)) return Patient::TEMPLATE_400_BAD_REQUEST;

        $id = htmlspecialchars($id);
        $columns = ["civilite", "nom", "prenom", "sexe", "adresse", "code_postal", "ville", "date_nais", "lieu_nais", "num_secu"];

        $requestContent = "UPDATE usager SET ";
        $requestArray = [];

        foreach ($columns as $key) {
            if (!empty($data[$key])) {
                $requestContent .= ($requestArray ? ", " : "") . "$key = :$key";
                $requestArray[$key] = $data[$key];
            }
        }
        
        $requestContent .= " WHERE id_usager = :id_usager";
        $requestArray["id_usager"] = $id;

        $stmt = $pdo->prepare($requestContent);

        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        if (!$stmt->execute($requestArray)) return Patient::TEMPLATE_403_ERROR;
        if ($stmt->rowcount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;

        return [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été modifié partiellement.",
            "data"           => Patient::getById($pdo, $id)["data"]
        ];
    }

    public static function completeEdit(PDO $pdo, $id, $data) {
        // faire cas si inexistant (edit sur un truc pas existant, le crée (pas PATCH))
        if (!isPutValid($id, $data)) return Patient::TEMPLATE_400_BAD_REQUEST;

        $id = htmlspecialchars($id);
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
                                   num_secu    = :num_secu
                               WHERE id_usager = :id_usager");

        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
    
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
                 "id_usager"   => $id];

        if (!$stmt->execute($args)) return Patient::TEMPLATE_403_ERROR;
        if ($stmt->rowcount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;

        return [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été modifié entièrement.",
            "data"           => Patient::getById($pdo, $id)["data"]
        ];
    }

    public static function delete($pdo, $id) {
        if (!isDeleteValid($id)) return Patient::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        $stmt = $pdo->prepare("DELETE FROM usager WHERE id_usager = :id_usager");
        
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        if (!$stmt->execute(['id_usager' => $id])) return Patient::TEMPLATE_403_ERROR;
        if ($stmt->rowCount() == 0) return Patient::TEMPLATE_404_NOT_FOUND;

        return [
            "status_code"    => 200,
            "status_message" => "Le patient a bien été supprimé.",
            "data"           => null
        ];
    }
}






