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
    $model = $data['model'];

    if($model){
        $query = "SELECT cars.model, cars.brand, cars.price, car_image.img, car_details.top_speed, car_details.`0-100`, car_details.drive_type, car_details.horsepower, car_details.power, car_details.cc, car_details.fuel_type, car_details.length, car_details.width, car_details.higth, car_details.weight FROM `cars` JOIN `car_image` ON cars.model = car_image.model  JOIN `car_details` ON cars.model = car_details.model WHERE cars.model = '$model'";
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
                    'price' => $row['price'],
                    'top_speed' => $row['top_speed'],
                    '0-100' => $row['0-100'],
                    'drive_type' => $row['drive_type'],
                    'horsepower' => $row['horsepower'],
                    'power' => $row['power'],
                    'cc' => $row['cc'],
                    'fuel_type' => $row['fuel_type'],
                    'length' => $row['length'],
                    'width' => $row['width'],
                    'hight' => $row['higth'],
                    'weight' => $row['weight'],
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