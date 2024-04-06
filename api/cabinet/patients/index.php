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
        // S'il y a un id, on renvoie le patient correspondante à ce dernier
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $matchingData = Patient::getById($pdo, $id);

        // Sinon on renvoie tous les patients
        } else {
            $matchingData = Patient::getAll($pdo);
        }
        
        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'POST':
        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Création du patient
        $matchingData = Patient::create($pdo, $data);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
        

    case 'PATCH':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Patch vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Modification partielle du patient
        $matchingData = Patient::partialEdit($pdo, $id, $data);
        
        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'PUT':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Put vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Modification totale du patient
        $matchingData = Patient::completeEdit($pdo, $id, $data);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'DELETE':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Delete vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Supression du patient
        $matchingData = Patient::delete($pdo, $id);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
}