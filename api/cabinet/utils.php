
<?php
function get_authorization_header(){
	$headers = null;

	if (isset($_SERVER['Authorization'])) {
		$headers = trim($_SERVER["Authorization"]);
	} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
		$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
	} else if (function_exists('apache_request_headers')) {
		$requestHeaders = apache_request_headers();
		// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
		$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
		//print_r($requestHeaders);
		if (isset($requestHeaders['Authorization'])) {
			$headers = trim($requestHeaders['Authorization']);
		}
	}

	return $headers;
}

function get_bearer_token() {
    $headers = get_authorization_header();
    
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            if($matches[1]=='null') //$matches[1] est de type string et peut contenir 'null'
                return null;
            else
                return $matches[1];
        }
    }
    return null;
}

function isConnectionValid() {
    $token = get_bearer_token();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://xouxou.alwaysdata.net/doctors-office-api/auth/'.$token);
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

function verifTokenConnection() {
    if (!isConnectionValid()) {
        $matchingData = [
            "status_code"    => 401,
            "status_message" => "Unauthorized : Le token que vous utilisez est invalide",
            "data"           => null
        ];
        deliver_response($matchingData['status_code'], $matchingData['status_message'], $matchingData['data']);
        exit(0);
    }
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