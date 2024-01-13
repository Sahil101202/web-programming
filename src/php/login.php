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

    $email = $data['email'];
    $password = $data['password'];
    $date = date("Y/m/d");

    if (empty($email) || empty($password)) {
        $response = array('success' => false, 'message' => 'Please enter username and password.', 'data' => 'no data');
        echo json_encode($response);
        exit;
    }

    $query_admin = "SELECT * FROM `admin` WHERE email = '$email'";
    $result_admin = mysqli_query($conn, $query_admin);
    
    if(mysqli_num_rows($result_admin) > 0){
        $admin = mysqli_fetch_assoc($result_admin);

        if (password_verify($password, $admin['admin_password'])){
            $update_admin_time = "UPDATE `admin` SET last_login='$date' WHERE email = '$email'";

            if(mysqli_query($conn, $update_admin_time)){
                $admin_json = json_encode(['email'=>$admin['email']], true);

                $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
                $payload = json_encode([
                    "admin_email" => $admin['email'],
                    "data" => $admin_json,
                    "exp" => time() + ( 60 * 60 * 12), // Token expiration time (12 hour)
                ]);

                $jwt_key = "admin101202"; // secret key
                // Encode the components for the JWT token
                $base64_header = base64_encode($header);
                $base64_payload = base64_encode($payload);

                // Create the signature
                $signature = hash_hmac('sha256', "$base64_header.$base64_payload", $jwt_key, true);
                $base64_signature = base64_encode($signature);

                // Combine the components to create the JWT token
                $jwt_token = "$base64_header.$base64_payload.$base64_signature";

                $response_admin = array('success' => true, 'message' => 'Admin Login successful!', 'data' => 'no data', 'token' => $jwt_token);
                echo json_encode($response_admin);
            }else{
                $response = array('success' => false, 'message' => 'Something Went Wrong!');
                echo json_encode($response);
            }
        }else{
            $response = array('success' => false, 'message' => 'Wrong Password!');
            echo json_encode($response);
        }
    }else{
        $query_user = "SELECT * FROM `users` WHERE email = '$email'";
        $result_user = mysqli_query($conn, $query_user);

        if(mysqli_num_rows($result_user) > 0){
            $user = mysqli_fetch_assoc($result_user);

            if (password_verify($password, $user['user_password'])){
                $update_user_time = "UPDATE `users` SET last_login = '$date' WHERE email = '$email'";

                if(mysqli_query($conn, $update_user_time)){
                    $user_json = json_encode(['user_id'=>$user['user_id'], 'name'=>$user['name'],'surname'=>$user['surname'],'email'=>$user['email'],'gender'=>$user['gender'],'phone'=>$user['phone'],'country'=>$user['country'],'birth_date'=>$user['birth_date']], true);

                    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
                    $payload = json_encode([
                        "admin_email" => $user['email'],
                        "data" => $user_json,
                        "exp" => time() + ( 60 * 60 * 12), // Token expiration time (12 hour)
                    ]);

                    $jwt_key = "user101202"; // secret key
                    // Encode the components for the JWT token
                    $base64_header = base64_encode($header);
                    $base64_payload = base64_encode($payload);

                    // Create the signature
                    $signature = hash_hmac('sha256', "$base64_header.$base64_payload", $jwt_key, true);
                    $base64_signature = base64_encode($signature);

                    // Combine the components to create the JWT token
                    $jwt_token = "$base64_header.$base64_payload.$base64_signature";

                    $response_user = array('success' => true, 'message' => 'User Login successful!', 'data' => 'no data', 'token' => $jwt_token);
                    echo json_encode($response_user);
                }else{
                    $response = array('success' => false, 'message' => 'Something Went Wrong!');
                    echo json_encode($response);
                }
            }else{
                $response = array('success' => false, 'message' => 'Wrong Password!');
                echo json_encode($response);
            }
        }else{
            $response = array('success' => false, 'message' => 'User Not Found!');
            echo json_encode($response);
        }
    }
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED']);
}
?>