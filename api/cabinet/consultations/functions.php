<?php

function isPostValid($data) {
    return !empty($data["date_consult"]) && !empty($data["heure_consult"]) && !empty($data["duree_consult"]) && !empty($data["id_medecin"]) && !empty($data["id_usager"]);
}

function isPatchValid($id, $data) {
    return !empty($id) && (!empty($data["date_consult"]) || !empty($data["heure_consult"]) || !empty($data["duree_consult"]) || !empty($data["id_medecin"]) || !empty($data["id_usager"]));
}

function isPutValid($id, $data) {
    return !empty($id) && !empty($data["date_consult"]) && !empty($data["heure_consult"]) && !empty($data["duree_consult"]) && !empty($data["id_medecin"]) && !empty($data["id_usager"]);
}

function isDeleteValid($id) {
    return isset($id);
}


class Consultation {

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
        "status_message" => "Not found : La consultation n'a pas été trouvée.",
        "data"           => null
    ];

    public static function getAll(PDO $pdo) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT * FROM consultation");

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute()) return Consultation::TEMPLATE_403_ERROR;              // Erreur du execute()
    
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "Toutes les consultations ont été reçues.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    
        return $matchingData;
    }

    public static function getById(PDO $pdo, $id) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT * FROM consultation WHERE id_consult = :id_consult");

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                 // Erreur du prepare()
        if (!$stmt->execute(['id_consult' => $id])) return Consultation::TEMPLATE_403_ERROR; // Erreur du execute()

        // Prend le resultat de la reqête
        $consultation = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si aucune consultation n'a été trouvée (réponse à la requête vide)
        if (!$consultation) return Consultation::TEMPLATE_404_NOT_FOUND;
        
        // Setup le résultat de la requête et l'envoie
        $matchingData =  [
            "status_code"    => 200,
            "status_message" => "La consultation a été reçue.",
            "data"           => $consultation
        ];

        return $matchingData;
    }


    public static function create(PDO $pdo, $data) : array {

        // Vérifie que tous les champs ont été saisis et que le créneau est libre
        $possible = Consultation::isConsultationPossible($pdo, $data);
        if($possible['status_code'] == 403) return $possible;
        
        // Requête
        $stmt = $pdo->prepare("INSERT INTO consultation(date_consult, heure_consult, duree_consult, id_medecin, id_usager)
                               VALUES(:date_consult, :heure_consult, :duree_consult, :id_medecin, :id_usager)");
    
        // Arguments de la requête
        $args = ["date_consult"  => $data['date_consult'],
                 "heure_consult" => $data["heure_consult"],
                 "duree_consult" => $data["duree_consult"],
                 "id_medecin"    => $data["id_medecin"],
                 "id_usager"     => $data["id_usager"]];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare()
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_403_ERROR;        // Erreur du execute()

        // Fin de la transaction
        $newId = $pdo->lastInsertId();
        $pdo->commit();

        $matchingData = [
            "status_code"    => 201,
            "status_message" => "La consultation a bien été ajoutée.",
            "data"           => Consultation::getById($pdo, $newId)["data"]
        ];
    
        // Setup le résultat de la requête et l'envoie
        return $matchingData;
    }

    public static function partialEdit(PDO $pdo, $id, $data) : array {

        // Vérifie que l'id et qu'au moins un champ ont été saisis
        if (!isPatchValid($id, $data)) return Consultation::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        // Tableau avec les noms des arguments possibles
        $columns = ["date_consult", "heure_consult", "duree_consult", "id_medecin", "id_usager"];

        // Requête
        $requestContent = "UPDATE consultation SET ";
        $requestArray = [];

        // Pour chaque valeur dans $data correspondante au tableau des arguments possibles
        foreach ($columns as $key) {
            if (!empty($data[$key])) {
                $requestContent .= ($requestArray ? ", " : "") . "$key = :$key";
                $requestArray[$key] = $data[$key];
            }
        }
        
        // Ajoute le WHERE à la fin
        $requestContent .= " WHERE id_consult = :id_consult";
        $requestArray["id_consult"] = $id;

        // Prépare la requête
        $stmt = $pdo->prepare($requestContent);

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;        // Erreur du prepare()
        if (!$stmt->execute($requestArray)) return Medecin::TEMPLATE_403_ERROR;     // Erreur du execute()
        if ($stmt->rowcount() == 0) return Medecin::TEMPLATE_404_NOT_FOUND;         // Aucune ligne modifiée

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "La consultation a bien été modifiée partiellement.",
            "data"           => Consultation::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function completeEdit(PDO $pdo, $id, $data) : array {

        // Vérifie que tous les champs ont été renseignés (id + champs de la table)
        if (!isPutValid($id, $data)) return Medecin::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);
        
        // Requête
        $stmt = $pdo->prepare("UPDATE consultation
                               SET date_consult  = :date_consult,
                                   heure_consult = :heure_consult,
                                   duree_consult = :duree_consult,
                                   id_medecin    = :id_medecin,
                                   id_usager     = :id_usager
                               WHERE id_consult  = :id_consult");

        // Arguments
        $args = ["date_consult"  => $data['date_consult'],
                 "heure_consult" => $data["heure_consult"],
                 "duree_consult" => $data["duree_consult"],
                 "id_medecin"    => $data["id_medecin"],
                 "id_usager"     => $data["id_usager"],
                 "id_consult"    => $id];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare() 
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_403_ERROR;        // Erreur du execute()
        if ($stmt->rowcount() == 0) return Consultation::TEMPLATE_404_NOT_FOUND;    // Aucune ligne modifiée

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData =  [
            "status_code"    => 200,
            "status_message" => "La consultation a bien été modifiée entièrement.",
            "data"           => Consultation::getById($pdo, $id)["data"]
        ];

        return $matchingData;
    }

    public static function delete($pdo, $id) : array {

        // Vérifie que l'id a bien été saisi
        if (!isDeleteValid($id)) return Consultation::TEMPLATE_400_BAD_REQUEST;
        $id = htmlspecialchars($id);

        // Requête
        $stmt = $pdo->prepare("DELETE FROM consultation WHERE id_consult = :id_consult");
        
        // Début de la transaction
        $pdo->beginTransaction();
        
        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;               // Erreur du prepare()
        if (!$stmt->execute(['id_consult' => $id])) return Consultation::TEMPLATE_403_ERROR;    // Erreur du execute()
        if ($stmt->rowCount() == 0) return Consultation::TEMPLATE_404_NOT_FOUND;                // Aucune ligne supprimée

        // Fin de la transaction
        $pdo->commit();

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "La consultation a bien été supprimée.",
            "data"           => null
        ];

        return $matchingData;
    }


    public static function isConsultationPossible(PDO $pdo, $data) : array {

        // Vérifie que tous les champs ont bien été saisis
        if (!isPostValid($data)) return Consultation::TEMPLATE_400_BAD_REQUEST;

        // Requête qui vérifie si le créneau est libre
        // Pour ce faire il sélectionne:
        // - Les consultations qui ont lieu le même jour
        // - ET
        //   - Soit le début de la nouvelle consultation est entre son début et sa fin
        //   - Soit la fine de la nouvelle consultation est entre son début et sa fin
        //   - Soit que le début de la nouvelle est avant son début et que la fin de la nouvelle est après sa fin (en gros qui la recouvre totalement)
        // - ET que son médecin OU son patient soit dans la nouvelle consultation
        //
        // Si il y a au moins une ligne dans le résultat, ça veut dire qu'il existe une consultation qui correspond à ces critères
        // et que donc le créneau n'est pas libre
        $stmt = $pdo->prepare("SELECT * 
                               FROM consultation 
                               WHERE date_consult = :date_consult
                               AND ((:heure_consult BETWEEN heure_consult AND ADDTIME(heure_consult, SEC_TO_TIME(duree_consult*60)))
                               OR (ADDTIME(:heure_consult, SEC_TO_TIME(:duree_consult*60)) BETWEEN heure_consult AND ADDTIME(heure_consult, SEC_TO_TIME(duree_consult*60)))
                               OR (:heure_consult < heure_consult AND ADDTIME(:heure_consult, SEC_TO_TIME(:duree_consult*60)) > ADDTIME(heure_consult, SEC_TO_TIME(duree_consult*60))))
                               AND (id_medecin = :id_medecin OR id_usager = :id_usager)");

        // Arguments
        $args = ["date_consult"    => $data['date_consult'],
                 "heure_consult"   => $data["heure_consult"],
                 "duree_consult"   => $data["duree_consult"],
                 "id_medecin"      => $data["id_medecin"],
                 "id_usager"       => $data["id_usager"]];

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare()
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_403_ERROR;        // Erreur du execute()

        // Prend le resultat de la requête, si vide c'est que la voie est libre
        $consultation = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$consultation) {
            $matchingData =  [
                "status_code"    => 200,
                "status_message" => "Le créneau est libre.",
                "data"           => null
            ];
        } else {
            $matchingData = [
                "status_code"    => 403,
                "status_message" => "Le créneau n'est pas libre pour cet usager et/ou ce médecin.",
                "data"           => $consultation
            ];
        }

        return $matchingData;
    }
}
