<?php

class Stats {

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

    public static function getStatsMedecin(PDO $pdo):array {
        $stmt = $pdo->prepare("SELECT medecin.id_medecin, medecin.civilite, medecin.nom, medecin.prenom , sum(duree) as dureeTotal 
        FROM consultation, medecin
        WHERE consultation.id_medecin = medecin.id_medecin 
        GROUP BY medecin.id_medecin 
        UNION 
        SELECT id_medecin, civilite, nom, prenom, 0 as dureeTotal 
        FROM medecin 
        WHERE id_medecin not in (SELECT id_medecin FROM consultation) 
        ORDER BY dureeTotal desc");

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }

        if (!$stmt->execute()) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        return [
            "status_code"    => 200,
            "status_message" => "La statistiques médecin a été reçue.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public static function getStatsPatient(PDO $pdo):array {
        $stmt = $pdo->prepare("SELECT
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) < 25 THEN 1 ELSE 0 END) AS moins_de_25_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS entre_25_et_50_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) > 50 THEN 1 ELSE 0 END) AS plus_de_50_ans
        FROM usager
        GROUP BY civilite;");

        if (!$stmt) {
            return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;
        }

        if (!$stmt->execute()) {
            return Medecin::TEMPLATE_403_ERROR;
        }

        

        return [
            "status_code"    => 200,
            "status_message" => "La statistiques patient a été reçue.",
            "data"           => ["homme" => $stmt->fetch(PDO::FETCH_ASSOC), "femme" => $stmt->fetch(PDO::FETCH_ASSOC)]
        ];
    }
}
