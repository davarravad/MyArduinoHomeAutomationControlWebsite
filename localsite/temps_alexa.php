<?php
/**
* Arduino Smart Home Control Console - Temp Alexa File
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
if(!isset($_REQUEST['house_id'])){
    $success = "false";
    $error_data['success'] = "false";
    $error_data['error']['code'] = "9552";
    $error_data['error']['message'] = "Error With House ID";
}

if(isset($_REQUEST['temp_server_name'])){ $temp_server_name = $_REQUEST['temp_server_name']; }
if(isset($_REQUEST['temp_data'])){ $temp_data = $_REQUEST['temp_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }
if(isset($_REQUEST['get_temp_for'])){ $get_temp_for = $_REQUEST['get_temp_for']; }

/* Check to make sure token is valid before updating database */
if($tkn == $db_house_token){
    if(!empty($get_temp_for)){
        /* Get Temp for requested location */
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_temps WHERE house_id = :house_id AND temp_alexa_name = :temp_alexa_name  LIMIT 1');
        $stmt->execute(['house_id' => $house_id, 'temp_alexa_name' => $get_temp_for]);
        $data = $stmt->fetch();
        $count = count($data);
        if($count > 1){
            $data['success'] = "true";
            $success = "true";
        }else{
            $success = "false";
            $error_data['success'] = "false";
            $error_data['error']['code'] = "2143";
            $error_data['error']['message'] = "Temperature Data Not Found";
        }
    }else{
        $success = "false";
        $error_data['success'] = "false";
        $error_data['error']['code'] = "4943";
        $error_data['error']['message'] = "Temperature Data Not Found";
    }
}else{
    $success = "false";
    $error_data['success'] = "false";
    $error_data['error']['code'] = "9528";
    $error_data['error']['message'] = "Error With Token or House ID";
}

/* Check for Success or Error and output data in json format */
if($success == "true"){
    header('Content-type: application/json');
    echo json_encode( $data );
}else{
    header('Content-type: application/json');
    echo json_encode( $error_data );
}

?>
