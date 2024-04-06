<?php
include "functions.php";                    // Fonctions de l'API patients
include "../connexionBD.php";               // Connexion à la BD du cabinet
include "../utils.php";                     // Fonctions utilitaires pour la connection par token
$pdo = createConnection();                  // Création du lien de connexion à la BD
$http_method = $_SERVER['REQUEST_METHOD'];  // Récupération de la méthode HTTP


verifTokenConnection();

// En fonction de la méthode HTTP
switch ($http_method) {
    case 'GET':
        // si le paramètre d'entrée dans la requête GET stat_name est bien mis
        if(isset($_GET['stat_name'])) {
            $statname = $_GET['stat_name'];
            // si la stat cherché est patient, on renvoit la stat des patients
            if ($statname == "patient") {
                $matchingData = Stats::getStatsPatient($pdo);
            // sinon si la stat cherché est médecin, on renvoit la stat des médecins
            } else if ($statname == "medecin") {
                $matchingData = Stats::getStatsMedecin($pdo);
            } else {
                $matchingData = [
                    "status_code"    => 404,
                    "status_message" => "Not found",
                    "data"           => null
                ];
            }
        // sinon aucun résultat
        } else {
            $matchingData = [
                "status_code"    => 404,
                "status_message" => "Not found",
                "data"           => null
            ];
        }

        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
}