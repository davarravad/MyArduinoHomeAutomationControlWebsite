<?php
/**
* Arduino Smart Home Control Console - Temp Recorder File
*
*
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 1.1
*/

/* Let Browser Know This is a Text Output */
header("Content-Type: text/plain");

/* Require the database file */
require_once('database.php');

/* Get Data From URL Input */
if(isset($_REQUEST['temp_server_name'])){ $temp_server_name = $_REQUEST['temp_server_name']; }
if(isset($_REQUEST['temp_data'])){ $temp_data = $_REQUEST['temp_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }
if(isset($_REQUEST['get_temp_for'])){ $get_temp_for = $_REQUEST['get_temp_for']; }

/* Check to make sure token is valid before updating database */
if($tkn == $db_house_token){
    if(!empty($temp_server_name) && !empty($temp_data)){
        /* Update Current Temp In Database */
        $sql = "UPDATE uap4_hc_temps SET temp_data = ? WHERE house_id = ? AND temp_server_name = ?";
        $pdo->prepare($sql)->execute([$temp_data, $house_id, "temp_".$temp_server_name]);
        /* Add Current Temp to Temp History database */
        if(date('i', time())%15==0 && date('s', time())%15==0) {
          $stmt = $pdo->prepare('SELECT id FROM uap4_hc_temps WHERE house_id = :house_id AND temp_server_name = :temp_server_name LIMIT 1');
          $stmt->execute(['house_id' => $house_id, 'temp_server_name' => "temp_".$temp_server_name]);
          $data = $stmt->fetch();
          $temp_id = $data["id"];
          $sql = "INSERT INTO uap4_hc_temps_history SET temp_id = ?, temp_data = ?";
          $pdo->prepare($sql)->execute([$temp_id, $temp_data]);
        }
        echo "<TEMPUPDATED".$temp_data.">";
    }else if(!empty($get_temp_for)){
        /* Get Temp for requested location */
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_temps WHERE house_id = :house_id AND temp_alexa_name = :temp_alexa_name  LIMIT 1');
        $stmt->execute(['house_id' => $house_id, 'temp_alexa_name' => $get_temp_for]);
        $data = $stmt->fetch();
        /* Output data in json format */
        header('Content-type: application/json');
        echo json_encode( $data );
    }else{
        /* Temp server name or temp data missing */
        echo "<ERROR49435>";
    }
}else{
    /* Token Error */
    echo "<ERROR495273>";
}


?>
