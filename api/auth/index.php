<?php

include "jwt_utils.php";
include "connexionBD.php";

$http_method = $_SERVER['REQUEST_METHOD'];
$pdo = createConnection();
$secret = "GRRRRRRR";

switch ($http_method) {
    case 'GET':
        if(isset($_GET['token'])) {
            $token = $_GET['token'];
            $matchingData = isTokenCorrect($token);
        } else {
            $matchingData = [
                "status_code"    => 400,
                "status_message" => "Bad request",
                "data"           => "Le token n'a pas été renseigné."
            ];
        }
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;

    case 'POST':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        $matchingData = isUserCorrect($data);
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

function isUserCorrect($data) {
    global $pdo;
    global $secret;

    // Vérifie si un couple login/password a bien été donné
    if(empty($data['login']) || empty($data['mdp'])) {
        $matchingData = [
            "status_code"    => 400,
            "status_message" => "Bad request",
            "data"           => "Login ou mot de passe non renseigné."
        ];
    } else {
        $login = $data['login'];
        $pass  = $data['mdp'];

        // Requête dans la BD
        $stmt = $pdo->prepare("SELECT * FROM user_auth_v1 WHERE login = :login AND mdp = :mdp");
        if (!$stmt) { echo "Prepare error : " . $stmt->errorInfo(); exit(1); }
        $stmt->execute([":login" => $login, ":mdp" => $pass]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si il y a un résultat (si ce couple login/mdp existe), on créé le token
        if(!empty($user)) {
            $userLogin = $user['login'];

            $header  = array("alg" => "HS256", "typ" => "JWT");
            $payload = array("user_login" => $userLogin, "exp" => (time()+86400));

            $token = generate_jwt($header, $payload, $secret);

            $matchingData = [
                "status_code"    => 200,
                "status_message" => "OK",
                "data"           => $token
            ];
        } else {
            $matchingData = [
                "status_code"    => 404,
                "status_message" => "Not found",
                "data"           => "Aucun utilisateur ne correspond à ce couple login / mot de passe."
            ];
        }
    }

    return $matchingData;
}

function isTokenCorrect($token) {
    global $secret;

    if(is_jwt_valid($token, $secret)) {
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "OK",
            "data"           => ["message" => "Token valide.",
                                 "valid" => true]
        ];
    } else {
        $matchingData = [
            "status_code"    => 200,
            "status_message" => "OK",
            "data"           => ["message" => "Token invalide.",
                                 "valid" => false]
        ];
    }

    return $matchingData;
}