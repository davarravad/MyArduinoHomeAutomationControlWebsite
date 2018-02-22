<?php
/**
* Arduino Smart Home Control Console - Garage Control File
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

if(isset($_REQUEST['door_id'])){ $door_id = $_REQUEST['door_id']; }
if(isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
if(isset($_REQUEST['action_data'])){ $action_data = $_REQUEST['action_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }

// Check to make sure token is valid before updating database
if($tkn == $db_house_token){
    if($action == "update_sensor"){
        // Update the garage door sensor data (Open/Closed)
        if(isset($action_data)){
            // Update Current Door Status in Database
            $sql = "UPDATE uap4_hc_garage SET door_status = ? WHERE house_id = ? AND door_id = ?";
            $pdo->prepare($sql)->execute([$action_data, $house_id, $door_id]);
            echo "<UPDATED>";
        }else{
            echo "<ERROR485>";
        }
    }else if($action == "door_button"){
        // Check database to see if door button was pushed
        $stmt = $pdo->prepare('SELECT door_button FROM uap4_hc_garage WHERE house_id = :house_id AND door_id = :door_id');
        $stmt->execute(['house_id' => $house_id, 'door_id' => $door_id]);
        $data = $stmt->fetch();
        $output = $data["door_button"];
        if(!empty($output)){
            echo "<".$output.">";
            // Update Database to change button to do nothing
            $sql = "UPDATE uap4_hc_garage SET door_button = ? WHERE house_id = ? AND door_id = ?";
            $pdo->prepare($sql)->execute(["DO_NOTHING", $house_id, $door_id]);
        }else{
            echo "<DO_NOTHING1>";
        }
    }else if($action == "garage_data"){
        // Get Temp for requested location
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_garage WHERE house_id = :house_id');
        $stmt->execute(['house_id' => $house_id]);
        $data = $stmt->fetch();
        $count = count($data);
        // check to see if there was a result
        if($count > 0){
            $data[success] = "true";

            header('Content-type: application/json');
            echo json_encode( $data );
        }else{
            $error_data[success] = "false";

            $error_data[error][code] = "5868";
            $error_data[error][message] = "Garage Data Not Found";

            header('Content-type: application/json');
            echo json_encode( $error_data );
        }
    }else{
        echo "<DO_NOTHING2>";
    }
}else{
    echo "<ERROR56461>";
}





?>
