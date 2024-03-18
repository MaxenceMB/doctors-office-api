<?php

include "functions.php";                    // Fonctions de l'API médecins
include "../connexionBD.php";               // Connexion à la BD du cabinet
$pdo = createConnection();                  // Création du lien de connexion à la BD
$http_method = $_SERVER['REQUEST_METHOD'];  // Récupération de la méthode HTTP

// En fonction de la méthode HTTP
switch ($http_method) {

    case 'GET':
        // S'il y a un id, on renvoie le médecin correspondant à ce dernier
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $matchingData = Medecin::getById($pdo, $id);

        // Sinon on renvoie tous les médecins
        } else {
            $matchingData = Medecin::getAll($pdo);
        }
        
        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'POST':
        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Création du médecin
        $matchingData = Medecin::create($pdo, $data);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
        

    case 'PATCH':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Patch vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Modification partielle du medecin
        $matchingData = Medecin::partialEdit($pdo, $id, $data);
        
        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'PUT':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Put vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Récupération des données du body (JSON)
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Modification totale du medecin
        $matchingData = Medecin::completeEdit($pdo, $id, $data);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;


    case 'DELETE':
        // Mets l'id à null s'il est set (Car ensuite la fonction du Delete vérifie si l'id est renseigné)
        $id = $_GET['id'] ?? null;

        // Supression du médecin
        $matchingData = Medecin::delete($pdo, $id);

        // Envoi de la réponse
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
}

function deliver_response($status_code, $status_message, $data = null) {
    http_response_code($status_code);
    header("Content-Type:application/json; charset=utf-8");

    $response['status_code']    = $status_code;
    $response['status_message'] = $status_message;
    $response['data']           = $data;

    $json_response = json_encode($response);
    if($json_response === false) die('JSON Encode ERROR : '.json_last_error_msg());
    echo $json_response;
}