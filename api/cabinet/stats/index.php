<?php
include "functions.php";
include "../connexionBD.php";
include "../getBearerToken.php";
$pdo = createConnection();
$http_method = $_SERVER['REQUEST_METHOD'];

function isConnectionValid() {
    $token = get_bearer_token();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/doctors-office-api/auth?token='.$token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);

    if (!$response['data']['valid']) {
        return false;
    } else {
        return true;
    }
}

if (!isConnectionValid()) {
    $matchingData = [
        "status_code"    => 401,
        "status_message" => "Unauthorized : Le token que vous utilisez est invalide",
        "data"           => null
    ];
    deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
    exit(0);
}



switch ($http_method) {
    case 'GET':
        if(isset($_GET['stat_name'])) {
            $statname = $_GET['stat_name'];
            if ($statname == "patient") {
                $matchingData = Stats::getStatsPatient($pdo);
            } else if ($statname == "medecin") {
                $matchingData = Stats::getStatsMedecin($pdo);
            } else {
                $matchingData = [
                    "status_code"    => 404,
                    "status_message" => "Not found",
                    "data"           => null
                ];
            }
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