<?php
include "../formats.php";
class Consultation {

    // Templates de retour JSON pour les erreurs de PREPARE/EXECUTE qui sont forcément entièrement notre faute
    public const TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR = [
        "status_code"    => 500,
        "status_message" => "Internal Server Error : Un problème interne s'est produit.",
        "data"           => null
    ];

    public const TEMPLATE_400_BAD_REQUEST = [
        "status_code"    => 400,
        "status_message" => "Bad request : Tous les champs n'ont pas été correctement saisis.",
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
        if (!$stmt->execute()) return Consultation::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()
    
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
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                   // Erreur du prepare()
        if (!$stmt->execute(['id_consult' => $id])) return Consultation::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()

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
        if($possible['status_code'] != 200) return $possible;

        $ferie = Consultation::isJourFerie($data);
        if($ferie['status_code'] != 200) return $ferie;
        
        // Requête
        $stmt = $pdo->prepare("INSERT INTO consultation(date_consult, heure_consult, duree_consult, id_medecin, id_usager)
                               VALUES(:date_consult, :heure_consult, :duree_consult, :id_medecin, :id_usager)");

        $newDate = convertDate($data['date_consult']);
    
        // Arguments de la requête
        $args = ["date_consult"  => $newDate,
                 "heure_consult" => $data["heure_consult"],
                 "duree_consult" => $data["duree_consult"],
                 "id_medecin"    => $data["id_medecin"],
                 "id_usager"     => $data["id_usager"]];

        // Début de la transaction
        $pdo->beginTransaction();

        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;   // Erreur du prepare()
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_404_NOT_FOUND;    // Erreur du execute()

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
        $valide = isPatchValid($pdo, $id, $data);
        if(is_array($valide)) return $valide;
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
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;           // Erreur du prepare()
        if (!$stmt->execute($requestArray)) return Consultation::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()
        if ($stmt->rowcount() == 0) return Consultation::TEMPLATE_404_NOT_FOUND;            // Aucune ligne modifiée

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
        $valide = isPutValid($id, $data);
        if(is_array($valide)) return $valide;
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
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()
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
        $valide = isDeleteValid($id);
        if(is_array($valide)) return $valide;
        $id = htmlspecialchars($id);

        // Requête
        $stmt = $pdo->prepare("DELETE FROM consultation WHERE id_consult = :id_consult");
        
        // Début de la transaction
        $pdo->beginTransaction();
        
        // Gestion des erreurs
        if (!$stmt) return Consultation::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;                   // Erreur du prepare()
        if (!$stmt->execute(['id_consult' => $id])) return Consultation::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()
        if ($stmt->rowCount() == 0) return Consultation::TEMPLATE_404_NOT_FOUND;                    // Aucune ligne supprimée

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
        $valide = isPostValid($pdo, $data);
        if(is_array($valide)) return $valide;
        
        $anneeStr = substr($data["date_consult"], 0, 4);
        $annee = intval($anneeStr, 10);
        $anneeMax = intval(date("Y"), 10)+5;

        if($annee > $anneeMax) {
            return [
                "status_code"    => 403,
                "status_message" => "L'année de la consultation est trop élevée.",
                "data"           => $annee." > ".$anneeMax
            ];
        }

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
        if (!$stmt->execute($args)) return Consultation::TEMPLATE_400_BAD_REQUEST;  // Erreur du execute()

        // Prend le resultat de la requête, si vide c'est que la voie est libre
        $consultation = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$consultation) {
            $matchingData = [
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


    public static function isJourFerie($data) {
        $anneeStr = substr($data["date_consult"], 0, 4);
        $annee = intval($anneeStr, 10);

        $ch = curl_init();
        $url = "https://calendrier.api.gouv.fr/jours-feries/metropole/".$annee.".json";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        $response = json_decode(curl_exec($ch), true);
        $dates = array_keys($response);
        $ferie = false;

        foreach($dates as $date) {
            if ($date == $data["date_consult"]) {
                $ferie = true;
                break;
            }
        }

        if($ferie) {
            $matchingData = [
                "status_code"    => 403,
                "status_message" => "Le jour selectionné est ferié.",
                "data"           => $response[$data["date_consult"]]
            ];
        } else {
            $matchingData = [
                "status_code"    => 200,
                "status_message" => "Le jour selectionné est libre.",
                "data"           => $data["date_consult"]
            ];
        }

        curl_close($ch);

        return $matchingData;
    }
}

function isPostValid($pdo, $data) {
    $err = ((isset($data["date_consult"])) ? checkDateConsultation($data["date_consult"]) : ["status_code" => 400, "status_message" => "DATE_INV.", "data" => "La date n'a pas été définie."]) ?:
           ((isset($data["heure_consult"]) && isset($data["duree_consult"])) ? checkHeure($data["heure_consult"], $data["duree_consult"]) : ["status_code" => 400, "status_message" => "HR_INV.", "data" => "L'heure et/ou la durée n'ont pas été définies."]) ?:
           ((isset($data["id_medecin"])) ? checkId($pdo, $data["id_medecin"], "medecin")        : ["status_code" => 400, "status_message" => "MED_INV.", "data" => "Le médecin n'a pas été défini."]) ?:
           ((isset($data["id_usager"])) ? checkId($pdo, $data["id_usager"], "usager")           : ["status_code" => 400, "status_message" => "USA_INV.", "data" => "L'usager n'a pas été défini."]);
    
    return ($err == "") ? true : $err;
}

function isPatchValid($pdo, $id, $data) {

    if(empty($data["date_consult"]) && empty($data["heure_consult"]) && empty($data["duree_consult"]) && empty($data["id_medecin"]) && empty($data["id_usager"])) {
        $err = ["status_code" => 400, "status_message" => "PATCH_INV.", "data" => "Aucun champ n'a été défini."];
    } else {
        $err = ((isset($data["date_consult"])) ? checkDateConsultation($data["date_consult"]) : ["status_code" => 400, "status_message" => "DATE_INV.", "data" => "La date n'a pas été définie."]) ?:
               ((isset($data["heure_consult"]) && isset($data["duree_consult"])) ? checkHeure($data["heure_consult"], $data["duree_consult"]) : ["status_code" => 400, "status_message" => "HR_INV.", "data" => "L'heure et/ou la durée n'ont pas été définies."]) ?:
               ((isset($data["id_medecin"]))   ? checkId($pdo, $data["id_medecin"], "medecin")      : ["status_code" => 400, "status_message" => "MED_INV.", "data" => "Le médecin n'a pas été défini."]) ?:
               ((isset($data["id_usager"]))    ? checkId($pdo, $data["id_usager"], "usager")        : ["status_code" => 400, "status_message" => "USA_INV.", "data" => "L'usager n'a pas été défini."]);
               ((empty($id))                   ? ["status_code" => 400, "status_message" => "ID_INV.", "data" => "L'id n'a pas été défini'."] : "");
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

function convertDate($oldDate) {
    $values = explode($oldDate, '/');
    $newDate = $values[2]."-".$values[1]."-".$values[0];
    return $newDate;
}