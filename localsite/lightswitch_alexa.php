<?php
/**
* Arduino Smart Home Control Console - Light Switch Control File
*
*
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 1.1
*/

/* Require the database file */
require_once('database.php');

/* Check for House ID */
if(!isset($_REQUEST['house_id'])){
    $success = "false";
    $error_data['success'] = "false";
    $error_data['error']['code'] = "9552";
    $error_data['error']['message'] = "Error With House ID";
}

/* Get Data From URL Input */
if(isset($_REQUEST['relayset'])){ $relayset = $_REQUEST['relayset']; }
if(isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
if(isset($_REQUEST['action_data'])){ $action_data = $_REQUEST['action_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }
if(isset($_REQUEST['relay_id'])){ $relay_id_url = $_REQUEST['relay_id']; }

if($relayset == "single_light"){
    $relay_id = get_relay_id($house_id, $relay_id_url);
    if(isset($relay_id)){
        if($action_data == "0"){
            $relay_action = "LIGHT_OFF";
        }else if($action_data == "1"){
            $relay_action = "LIGHT_ON";
        }else{
            $success = "false";
            $error_data['success'] = "false";
            $error_data['error']['code'] = "2875";
            $error_data['error']['message'] = "Light Action is Not Correct";
        }
        if(isset($relay_action)){
            $update_results = update_relay($house_id, $relay_id, $action, $relay_action, $tkn, $db_house_token);
            if($update_results == "success"){
                $success_data['success'] = "true";
                $success = "true";
            }
        }
    }else{
        $success = "false";
        $error_data['success'] = "false";
        $error_data['error']['code'] = "2891";
        $error_data['error']['message'] = "Relay I D is Not Correct";
    }
}else{
    $success = "false";
    $error_data['success'] = "false";
    $error_data['error']['code'] = "2651";
    $error_data['error']['message'] = "Relay Set is Not Correct";
}

if($success == "true"){ // Check for Success
    header('Content-type: application/json');
    echo json_encode( $success_data );
}else{ // Check for errors
    header('Content-type: application/json');
    echo json_encode( $error_data );
}


function get_relay_id($house_id = null, $relay_alexa_name = null){
    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    // Get Relay ID based on Alexa name
    $stmt = $pdo->prepare('SELECT * FROM uap4_hc_relays WHERE house_id = :house_id AND relay_alexa_name = :relay_alexa_name LIMIT 1');
    $stmt->execute(['house_id' => $house_id, 'relay_alexa_name' => $relay_alexa_name]);
    $data = $stmt->fetch();

    // Data from webserver
    $relay_id = $data["relay_server_name"];

    return $relay_id;
}

function update_relay($house_id = null, $relay_id = null, $action = null, $action_data = null, $tkn = null, $db_house_token = null){

    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    // Get Relay Status from server to see if anything will change
    $stmt = $pdo->prepare('SELECT * FROM uap4_hc_relays WHERE house_id = :house_id AND relay_server_name = :relay_id LIMIT 1');
    $stmt->execute(['house_id' => $house_id, 'relay_id' => $relay_id]);
    $data = $stmt->fetch();

    // Data from webserver
    $website_last_updated = $data["last_updated_by"];

    if($action_data == "LIGHT_ON"){
        $update_relay_action = "LIGHT_ON";
    }
    if($action_data == "LIGHT_OFF"){
        $update_relay_action = "LIGHT_OFF";
    }

    if(isset($update_relay_action)){
        // Check to make sure token is valid before updating database
        if($tkn == $db_house_token){
            if($action == "update_relay"){
                // Check to see if Arduino needs to update anything
                if($website_last_updated == "Arduino"){}
                else if($website_last_updated == "WebSite"){}
                // Update the garage door sensor data (Open/Closed)
                if(isset($action_data)){
                    // Update Current Door Status in Database
                    $sql = "UPDATE uap4_hc_relays SET relay_action = ?, last_updated_by = ? WHERE house_id = ? AND relay_server_name = ?";
                    $pdo->prepare($sql)->execute([$update_relay_action, "Arduino", $house_id, $relay_id]);
                    return "success";
                }else{
                    return "error";
                }
            }else{
                return "error";
            }
        }else{
            return "error";
        }
    }else{
        return "error";
    }
}




?>
