<?php
class Stats {

    public static function getStatsMedecin(PDO $pdo) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT medecin.id_medecin, medecin.civilite, medecin.nom, medecin.prenom , sum(duree) as dureeTotal 
        FROM consultation, medecin
        WHERE consultation.id_medecin = medecin.id_medecin 
        GROUP BY medecin.id_medecin 
        UNION 
        SELECT id_medecin, civilite, nom, prenom, 0 as dureeTotal 
        FROM medecin 
        WHERE id_medecin not in (SELECT id_medecin FROM consultation) 
        ORDER BY dureeTotal desc");

        // Gestion des erreurs
        if (!$stmt) return Medecin::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute()) return Medecin::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()

        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "La statistiques médecin a été reçue.",
            "data"           => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ];

        return $matchingData;
    }

    public static function getStatsPatient(PDO $pdo) : array {

        // Requête
        $stmt = $pdo->prepare("SELECT
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) < 25 THEN 1 ELSE 0 END) AS moins_de_25_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS entre_25_et_50_ans,
        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) > 50 THEN 1 ELSE 0 END) AS plus_de_50_ans
        FROM usager
        GROUP BY civilite;");

        // Gestion des erreurs
        if (!$stmt) return Patient::TEMPLATE_MATCHING_DATA_SYSTEM_500_ERROR;    // Erreur du prepare()
        if (!$stmt->execute()) return Patient::TEMPLATE_400_BAD_REQUEST;        // Erreur du execute()
        
        // Setup le résultat de la requête et l'envoie
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "La statistiques patient a été reçue.",
            "data"           => ["homme" => $stmt->fetch(PDO::FETCH_ASSOC), "femme" => $stmt->fetch(PDO::FETCH_ASSOC)]
        ];

        return $matchingData;
    }
}
