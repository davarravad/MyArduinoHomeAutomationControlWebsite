<?php
/**
* Arduino Smart Home Control Console - Temp Recorder File
*
*
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 1.0
*/

// Require the database file
require_once('database.php');

if(isset($_REQUEST['house_id'])){
    $house_id = $_REQUEST['house_id'];

    // Get current action from database for ALL Relays
    $stmt = $pdo->prepare('SELECT house_token FROM uap4_hc_house WHERE house_id = :house_id LIMIT 1');
    $stmt->execute(['house_id' => $house_id]);
    $data = $stmt->fetch();

    $db_house_token = $data["house_token"];
}

if(isset($_REQUEST['temp_server_name'])){ $temp_server_name = $_REQUEST['temp_server_name']; }
if(isset($_REQUEST['temp_data'])){ $temp_data = $_REQUEST['temp_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }
if(isset($_REQUEST['get_temp_for'])){ $get_temp_for = $_REQUEST['get_temp_for']; }

// Check to make sure token is valid before updating database
if($tkn == $db_house_token){
    if(!empty($temp_server_name) && !empty($temp_data)){
        // Update Current Temp In Database
        $sql = "UPDATE uap4_hc_temps SET temp_data = ? WHERE house_id = ? AND temp_server_name = ?";
        $pdo->prepare($sql)->execute([$temp_data, $house_id, "temp_".$temp_server_name]);
    }else if(!empty($get_temp_for)){
        // Get Temp for requested location
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_temps WHERE house_id = :house_id AND temp_alexa_name = :temp_alexa_name  LIMIT 1');
        $stmt->execute(['house_id' => $house_id, 'temp_alexa_name' => $get_temp_for]);
        $data = $stmt->fetch();

        header('Content-type: application/json');
        echo json_encode( $data );

    }else{
        echo "<ERROR49435>";
    }
}else{
    echo "<ERROR495273>";
}


?>
