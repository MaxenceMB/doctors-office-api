<?php
include "functions.php";
include "../connexionBD.php";
$pdo = createConnection();
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method) {
    case 'GET':
        if(isset($_GET['stat_name'])) {
            $statname = $_GET['stat_name'];
            if ($statname == "patient") {
                $matchingData = Stats::getStatsPatient($pdo);
            } else if ($statname == "medecin") {

            } else {
                deliver_response("404", "Not found", null);
                break;
            }

            deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
            break;
        }

        deliver_response("404", "Not found", null);
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