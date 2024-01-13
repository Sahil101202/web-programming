<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require_once("config.php");

$file_json = file_get_contents('php://input');
$data = json_decode($file_json, true);

if(isset($data)){

    $token = $data['token'];

    $key = "user101202";
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
        $response = array('success' => false, 'message' => 'Token is expired !!!  or invalid token!!', 'reason' => 'token');
        echo json_encode($response);
    }else{
        $u_data = json_decode($user_data, true);
        $user_id = $u_data['user_id'];

        $query = "SELECT cars.model, cars.brand, cars.price, car_image.img FROM `favorites` JOIN `car_image` ON favorites.model = car_image.model  JOIN `cars` ON favorites.model = cars.model WHERE favorites.user_id = '$user_id'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            $response = ['success' => false, 'message' => 'No Favourite Car', 'reason' => 'do not have'];
            echo json_encode($response);
            exit;
        }else{
            while ($row = mysqli_fetch_assoc($result)) {
                // Convert the BLOB data to base64 encoding
                $imageData = base64_encode($row['img']);
        
                // Build the data array with the base64-encoded image data
                $cardata[] = [
                    'model' => $row['model'],
                    'brand' => $row['brand'],
                    'price' => $row['price'],
                    'image' => $imageData,
                ];
            }
            $response = ['success' => true, 'message' => 'Cars got successfuly!', 'data' => $cardata];
            echo json_encode($response);
        }
    }
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED', 'reason' => 'data injection.']);
}
?>