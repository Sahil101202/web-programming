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
    $brand = $data['brand'];

    if($brand){
        $query = "SELECT cars.model, cars.brand, car_image.img FROM `cars` JOIN `car_image` ON cars.model = car_image.model  WHERE cars.brand = '$brand'";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            $data = array();
            while ($row = mysqli_fetch_assoc($result)) {
                // Convert the BLOB data to base64 encoding
                $imageData = base64_encode($row['img']);

                // Build the data array with the base64-encoded image data
                $data[] = [
                    'model' => $row['model'],
                    'brand' => $row['brand'],
                    'image' => $imageData,
                ];
            }
            $response = ['success' => true, 'message' => 'Car got successfuly!', 'data' => $data];
            echo json_encode($response);
        }else{
            $response = ['success' => false, 'message' => 'Error!'];
            echo json_encode($response);
        }
    }else{
        $response = ['success' => false, 'message' => 'error in company name!!'];
        echo json_encode($response);
    } 
}else{
    echo json_encode(['success' => false, 'message' => 'ERROR !!!! DATA NOT RECIEVED']);
}
?>