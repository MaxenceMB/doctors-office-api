<?php

function isPostValid($data) {
    return !empty($data["nom"]) && !empty($data["prenom"]) && !empty($data["civilite"]);
}

function isPatchValid($id, $data) {
    return !empty($id) && (!empty($data["nom"]) || !empty($data["prenom"]) || !empty($data["civilite"]));
}

function isPutValid($id, $data) {
    return !empty($id) && !empty($data["nom"]) && !empty($data["prenom"]) && !empty($data["civilite"]);
}

function isDeleteValid($id) {
    return isset($id);
}


class Medecin {

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
        "status_message" => "Not found : Le médecin n'a pas été trouvé.",
        "data"           => null
    ];

    public static function getAll(PDO $pdo) {
        $stmt = $pdo->prepare("SELECT * FROM medecin");

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }

        if (!$stmt->execute()) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        $medecins = array();
        while ($medecin = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medecins[] = $medecin;
        }
    
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Tous les médecins ont été reçus.",
            "data"           => $medecins
        ];
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id):array {
        $stmt = $pdo->prepare("SELECT * FROM medecin WHERE id_medecin = :id_medecin");

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }

        if (!$stmt->execute(['id_medecin' => $id])) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$medecin) {
            return Medecin::TEMPLATE_404_NOT_FOUND;
        }
        
        return [
            "status_code"    => 200,
            "status_message" => "Le médecin a été reçu.",
            "data"           => $medecin
        ];
    }


    public static function create(PDO $pdo, $data):array {
        if (!isPostValid($data))
            return Medecin::TEMPLATE_400_BAD_REQUEST;
        
        $stmt = $pdo->prepare("INSERT INTO medecin(nom, prenom, civilite) VALUES(:nom, :prenom, :civilite)");

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }
    
        if (!$stmt->execute(["nom" => $data['nom'], "prenom" => $data["prenom"], "civilite" => $data["civilite"]])) {
            return Medecin::TEMPLATE_403_ERROR;
        }
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été ajouté.",
            "data"           => Medecin::getById($pdo, $pdo->lastInsertId())["data"]
        ];
    
        return $matchingData;
    }

    public static function partialEdit(PDO $pdo, $id, $data) {
        if (!isPatchValid($id, $data)) 
            return Medecin::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        $columns = ['nom', 'prenom', 'civilite'];

        $requestContent = "UPDATE medecin SET ";
        $requestArray = [];

        foreach ($columns as $key) {
            if (!empty($data[$key])) {
                $requestContent .= ($requestArray ? ", " : "") . "$key = :$key";
                $requestArray[$key] = $data[$key];
            }
        }
        
        $requestContent .= " WHERE id_medecin = :id_medecin";
        $requestArray["id_medecin"] = $id;

        $stmt = $pdo->prepare($requestContent);

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }
    
        if (!$stmt->execute($requestArray)) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        if ($stmt->rowcount() == 0) {
            return Medecin::TEMPLATE_404_NOT_FOUND;
        }

        return [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été modifié partiellement.",
            "data"           => Medecin::getById($pdo, $id)["data"]
        ];
    }

    public static function completeEdit(PDO $pdo, $id, $data) {
        // faire cas si inexistant (edit sur un truc pas existant, le crée (pas PATCH))
        if (!isPutValid($id, $data))
            return Medecin::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);
        
        $stmt = $pdo->prepare("UPDATE medecin SET nom = :nom, prenom = :prenom, civilite = :civilite WHERE id_medecin = :id_medecin");

        if (!$stmt)
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
    
        if (!$stmt->execute(["nom" => $data['nom'], 'prenom' => $data['prenom'], 'civilite' => $data['civilite'], 'id_medecin' => $id]))
            return Medecin::TEMPLATE_403_ERROR;
        
        if ($stmt->rowcount() == 0)
            return Medecin::TEMPLATE_404_NOT_FOUND;

        return [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été modifié entièrement.",
            "data"           => Medecin::getById($pdo, $id)["data"]
        ];
    }

    public static function delete($pdo, $id) {
        if (!isDeleteValid($id))
            return Medecin::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        $stmt = $pdo->prepare("DELETE FROM medecin WHERE id_medecin = :id_medecin");
        
        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }

        if (!$stmt->execute(['id_medecin' => $id])) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        if ($stmt->rowCount() == 0) {
            return Medecin::TEMPLATE_404_NOT_FOUND;
        }

        return [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été supprimé.",
            "data"           => null
        ];
    }
}






