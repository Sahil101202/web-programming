<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require_once("../../config.php");

$file_json = file_get_contents('php://input');
$data = json_decode($file_json, true);

if(isset($data)){

    $token = $data['token'];

    $key = "admin101202";
    $tokenParts = explode('.', $token);
	$header = base64_decode($tokenParts[0]);
	$payload = base64_decode($tokenParts[1]);
    $json_payload = json_decode($payload, true);
    $user_data = $json_payload['data'];
	$signature_provided = $tokenParts[2];

	// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
	$expiration = json_decode($payload)->exp;
	$is_token_expired = ($expiration - time()) < 0;

	// build a signature based on the header and payload using the secret
	$base64_header = base64_encode($header);
	$base64_payload = base64_encode($payload);
	$signature = hash_hmac('SHA256', $base64_header . "." . $base64_payload, $key, true);
    $base64_signature = base64_encode($signature);

	// verify it matches the signature provided in the jwt
	$is_signature_valid = ($base64_signature === $signature_provided);
	
	if ($is_token_expired || !$is_signature_valid) {
        $response = array('success' => false, 'message' => 'Token is expired !!!  or invalid token!!', 'data' => 'no data');
        echo json_encode($response);
    }else{
        $model = $data['model'];
        $company = $data['company'];
        $model_year = $data['model_year'];
        $price = $data['price'];
        $max_speed = $data['max_speed'];
        $s0_100 = $data['s0_100'];
        $fuel_type = $data['fuel_type'];
        $drive_type = $data['drive_type'];
        $horse_power = $data['horse_power'];
        $power = $data['power'];
        $length = $data['length'];
        $width = $data['width'];
        $higth = $data['higth'];
        $cc = $data['cc'];
        $weight = $data['weight'];
        $date = date("Y/m/d");  

        $query = "SELECT * FROM `cars` WHERE model = '$model'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 1) {
            $response = ['success' => false, 'message' => 'Car Model already exists.', 'data' => 'no data'];
            echo json_encode($response);
            exit;
        }

        $sql1 = "INSERT INTO `cars` (`car_id`, `brand`, `model`, `price`, `year`) VALUES (NULL, '$company', '$model', '$price', '$model_year')";

        $sql2 = "INSERT INTO `car_details` (`model`, `top_speed`, `0-100`, `drive_type`, `horsepower`, `power`, `cc`, `fuel_type`, `length`, `width`, `higth`, `weight`, `registered_date`) VALUES ('$model', '$max_speed', '$s0_100', '$drive_type', '$horse_power', '$power', '$cc', '$fuel_type', '$length', '$width', '$higth', '$weight', '$date')";

        if(mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)){
            $response = ['success' => true, 'message' => 'Car Added successful!'];
            echo json_encode($response);
        }else{
            $response = ['success' => false, 'message' => 'Error!'];
            echo json_encode($response);
        }
    }
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED']);
}
?>
