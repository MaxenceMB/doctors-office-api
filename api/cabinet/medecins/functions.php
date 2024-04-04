<?php

include "../formats.php";
class Medecin {

    // Templates de retour JSON pour les erreurs de PREPARE/EXECUTE qui sont forcément entièrement notre faute
    public const TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR = [
        "status_code"    => 500,
        "status_message" => "Internal Server Error : Un problème interne s'est produit.",
        "data"           => null
    ];

    public const TEMPLATE_400_BAD_REQUEST = [
        "status_code"    => 400,
        "status_message" => "Bad request",
        "data"           => null
    ];

    public const TEMPLATE_404_NOT_FOUND = [
        "status_code"    => 404,
        "status_message" => "Not found : Le médecin n'a pas été trouvé.",
        "data"           => null
    ];

    public static function getAll(PDO $pdo) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT * FROM medecin");

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute()) return Medecin::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()
    
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Tous les médecins ont été reçus.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT * FROM medecin WHERE id_medecin = :id_medecin");

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                  // Erreur du prepare()
        if (!$stmt->execute(['id_medecin' => $id])) return Medecin::TEMPLATE_400_BAD_REQUEST; // Erreur du execute()

        // Prend le resultat de la reqête
        $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si aucun médecin n'a été trouvé (réponse à la requête vide)
        if (!$medecin) return Medecin::TEMPLATE_404_NOT_FOUND;
        
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le médecin a été reçu.",
            "data"           => $medecin
        ];

        return $matchingData;
    }


    public static function create(PDO $pdo, $data) : array {

        // Vérifie que tous les champs ont bien été saisis
        $valide = isPostValid($data);
        if(is_array($valide)) return $valide;
        
        // Vérifie si aucun médecin avec ce nom et prénom existe déjà
        $existe = Medecin::alreadyExists($pdo, $data); 
        if($existe["status_code"] == 403) return $existe;
        
        // Requête
        $stmt = $pdo->prepare("INSERT INTO medecin(nom, prenom, civilite)
                               VALUES(:nom, :prenom, :civilite)");

        // Arguments de la requête
        $args = ["nom"      => $data['nom'],
                 "prenom"   => $data["prenom"],
                 "civilite" => $data["civilite"]];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute($args)) return Medecin::TEMPLATE_400_BAD_REQUEST;   // Erreur du execute()

        // Fin de la transaction
        $newId = $pdo->lastInsertId();  // Récupération du l'ID du médecin inséré
        $pdo->commit();                 // Commit l'insertion dans la BD
        
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 201,
            "status_message" => "Le médecin a bien été ajouté.",
            "data"           => Medecin::getById($pdo, $newId)["data"]
        ];
    
        return $matchingData;
    }

    public static function partialEdit(PDO $pdo, $id, $data) : array {

        // Vérifie que l'id et qu'au moins un champ ont été saisis
        $valide = isPatchValid($id, $data);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Tableau avec les noms des arguments possibles
        $columns = ['nom', 'prenom', 'civilite'];

        // Requête
        $requestContent = "UPDATE medecin SET ";
        $requestArray = [];

        // Pour chaque valeur dans $data correspondante au tableau des arguments possibles
        foreach ($columns as $key) {
            if (!empty($data[$key])) {
                $requestContent .= ($requestArray ? ", " : "") . "$key = :$key";
                $requestArray[$key] = $data[$key];
            }
        }
        
        // Ajoute le WHERE à la fin
        $requestContent .= " WHERE id_medecin = :id_medecin";
        $requestArray["id_medecin"] = $id;

        // Prépare la requête
        $stmt = $pdo->prepare($requestContent);

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (Medecin::getById($pdo, $id)["status_code"] == 404) return Medecin::TEMPLATE_404_NOT_FOUND;  // Aucun médecin n'a cet id
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                            // Erreur du prepare()
        if (!$stmt->execute($requestArray)) return Medecin::TEMPLATE_400_BAD_REQUEST;                   // Erreur du execute()

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été modifié partiellement.",
            "data"           => Medecin::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function completeEdit(PDO $pdo, $id, $data) {

        // Vérifie que tous les champs ont été renseignés (id + champs de la table)
        $valide = isPutValid($id, $data);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);
        
        // Requête
        $stmt = $pdo->prepare("UPDATE medecin
                               SET nom      = :nom,
                                   prenom   = :prenom,
                                   civilite = :civilite
                               WHERE id_medecin = :id_medecin");

        // Arguments
        $args = ["nom"        => $data['nom'],
                 "prenom"     => $data['prenom'],
                 "civilite"   => $data['civilite'],
                 "id_medecin" => $id];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (Medecin::getById($pdo, $id)["status_code"] == 404) return Medecin::TEMPLATE_404_NOT_FOUND;  // Aucun médecin n'a cet id
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                            // Erreur du prepare()
        if (!$stmt->execute($args)) return Medecin::TEMPLATE_400_BAD_REQUEST;                           // Erreur du execute()

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été modifié entièrement.",
            "data"           => Medecin::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function delete($pdo, $id) {

        // Vérifie que l'id a bien été saisi
        $valide = isDeleteValid($id);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Début de la transaction
        $pdo->beginTransaction();

        // Requête: suppression du médecin
        $stmt = $pdo->prepare("DELETE FROM medecin WHERE id_medecin = :id_medecin");

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                    // Erreur du prepare()
        if (!$stmt->execute(['id_medecin' => $id])) return Medecin::TEMPLATE_400_BAD_REQUEST;   // Erreur du execute()
        if ($stmt->rowCount() == 0) return Medecin::TEMPLATE_404_NOT_FOUND;                     // Aucune ligne supprimée

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Le médecin a bien été supprimé.",
            "data"           => null
        ];

        return $matchingData;
    }


    public static function alreadyExists(PDO $pdo, $data) : array {

        // Requête qui vérifie si le médecin existe déjà
        $stmt = $pdo->prepare("SELECT * 
                               FROM medecin 
                               WHERE nom = :nom
                               AND prenom = :prenom");

        // Arguments
        $args = ["nom"    => $data['nom'],
                 "prenom" => $data["prenom"]];

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare()
        if (!$stmt->execute($args)) return Medecin::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()

        // Prend le resultat de la requête, si vide c'est que la voie est libre
        $medecin = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$medecin) {
            $matchingData =  [
                "status_code"    => 200,
                "status_message" => "Le médecin n'existe pas.",
                "data"           => null
            ];
        } else {
            $matchingData = [
                "status_code"    => 403,
                "status_message" => "Un médecin nommé ".$data["prenom"]." ".strtoupper($data["nom"])." existe déjà.",
                "data"           => $medecin
            ];
        }

        return $matchingData;
    }
}


function isPostValid($data) {
    $err = ((isset($data["civilite"])) ? checkCivilite($data["civilite"])    : ["status_code" => 400, "status_message" => "CIV_INV.", "data" => "La civilité n'a pas été définie."]) ?:
           ((isset($data["nom"]))      ? checkNom($data["nom"], "nom")       : ["status_code" => 400, "status_message" => "NOM_INV.", "data" => "Le nom n'a pas été défini."]) ?:
           ((isset($data["prenom"]))   ? checkNom($data["prenom"], "prenom") : ["status_code" => 400, "status_message" => "PRENOM_INV.", "data" => "Le prénom n'a pas été défini."]);
    
    return ($err == "") ? true : $err;
}

function isPatchValid($id, $data) {

    if(empty($data["civilite"]) && empty($data["nom"]) && empty($data["prenom"])) {
        $err = ["status_code" => 400, "status_message" => "PATCH_INV.", "data" => "Aucun champ n'a été défini."];
    } else {
        $err = ((!empty($data["civilite"])) ? checkCivilite($data["civilite"])    : "") ?:
               ((!empty($data["nom"]))      ? checkNom($data["nom"], "nom")       : "") ?:
               ((!empty($data["prenom"]))   ? checkNom($data["prenom"], "prenom") : "") ?:
               ((empty($id))                ? ["status_code" => 400, "status_message" => "ID_INV.", "data" => "L'id n'a pas été défini'."] : "");
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