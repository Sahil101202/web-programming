<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require_once("config.php");

$table = "users";
$file_json = file_get_contents('php://input');
$data = json_decode($file_json, true);

if(isset($data)){   

    $first_name = $data['name'];
    $last_name = $data['surname'];
    $email = $data['email'];
    $password = $data['password'];
    $c_password = $data['repeat_password'];
    $gender = $data['gender'];
    $birth_date = $data['birth_date'];
    $phone = $data['phone'];
    $country = $data['country'];
    $date = date("Y/m/d");

    if ($password == $c_password){

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `$table` (`user_id`, `name`, `surname`, `birth_date`, `gender`, `country`, `phone`, `email`, `user_password`, `register_date`, `last_login`) VALUES (NULL, '$first_name', '$last_name', '$birth_date', '$gender', '$country', '$phone', '$email', '$hashedPassword', '$date', '$date')";
    
        $query = "SELECT * FROM `$table` WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $response = ['success' => false, 'message' => 'Username or email already exists or use different email.', 'data' => 'no data'];
            echo json_encode($response);
            exit;
        }
        if(mysqli_query($conn, $sql)){
            $response = ['success' => true, 'message' => 'Registration successful!'];
            echo json_encode($response);
        }else{
            $response = ['success' => false, 'message' => 'Error!'];
            echo json_encode($response);
        }
    }else{
        echo json_encode(['success' => false, 'message' => 'ERROR !!!! PASSWORDS ARE NOT SAME']);
    }
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED']);
}
?>
