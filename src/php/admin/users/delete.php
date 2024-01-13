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

    $email = $data['email'];
    $b_date = $data['birth_date'];
    $originalDate = DateTime::createFromFormat('Y/m/d', $b_date);
    $b_date = $originalDate->format('Y-m-d');
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

        $query = "SELECT * FROM `users` WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 0) {
            $response = array('success' => false, 'message' => 'Username not found.', 'data' => 'no data');
            echo json_encode($response);
            exit;
        }else{
            $user = mysqli_fetch_assoc($result);
            $id = $user['user_id'];
            $name = $user['name'];
            $surname = $user['surname'];
            $email = $user['email'];
            $hashedpassword = $user['user_password'];
            $gender = $user['gender'];
            $birth_date = $user['birth_date'];
            $phone = $user['phone'];
            $country = $user['country'];
            $register_date = $user['register_date'];
            $date = date("Y/m/d");

            if ($b_date === $birth_date) {
                $sql = "INSERT INTO `deleted_user` (`user_id`, `name`, `surname`, `birth_date`, `gender`, `country`, `phone`, `email`, `user_password`, `register_date`, `delete_date`) VALUES ($id, '$name', '$surname', '$birth_date', '$gender', '$country', '$phone', '$email', '$hashedpassword', '$register_date', '$date')";

                if(mysqli_query($conn, $sql)){
                    $sql = "DELETE FROM `users` WHERE email = '$email'";
                    if(mysqli_query($conn, $sql))
                    {
                        $response = array(['success' => true, 'message' => 'User Deleted successfully!']);
                        echo json_encode($response);
                    }else{
                        $response = array(['success' => false, 'message' => 'Error while deleting user! try again.']);
                        echo json_encode($response);
                    }
                }else{
                    $response = array(['success' => false, 'message' => 'Error while deleting user! try again.']);
                    echo json_encode($response);
                }
            }else {
                $response = array('success' => false, 'message' => 'Incorrect birth date.', 'data' => 'no data');
                echo json_encode($response);
            }
        }  
    }
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED']);
}
?>
