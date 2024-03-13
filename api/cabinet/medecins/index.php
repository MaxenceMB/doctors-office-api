<?php
include "functions.php";
include "../connexionBD.php";
$pdo = createConnection();
$http_method = $_SERVER['REQUEST_METHOD'];

switch ($http_method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $matchingData = Medecin::getById($pdo, $id);
        } else {
            $matchingData = Medecin::getAll($pdo);
        }
        
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;

    case 'POST':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);
        $matchingData = Medecin::create($pdo, $data);

        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;
        
    case 'PATCH':
        $id = $_GET['id'] ?? null;
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        $matchingData = Medecin::partialEdit($pdo, $id, $data);
        
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        $matchingData = Medecin::completeEdit($pdo, $id, $data);

        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $matchingData = Medecin::delete($pdo, $id);

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