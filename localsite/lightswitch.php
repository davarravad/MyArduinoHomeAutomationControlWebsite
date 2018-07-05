<?php
/**
* Arduino Smart Home Control Console - Light Switch Control File
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
if(isset($_REQUEST['relayset'])){ $relayset = $_REQUEST['relayset']; }
if(isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
if(isset($_REQUEST['action_data'])){ $action_data = $_REQUEST['action_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }
if(isset($_REQUEST['relay_id'])){ $relay_id_url = $_REQUEST['relay_id']; }

/* Check to see if first relay set */
if($relayset == "1"){
    $relayset_array = str_split($action_data);
    $i = 0;
    foreach ($relayset_array as $relay) {
        $i++;
        $relay_id = sprintf("%02d", $i);
        $relay_id = "relay_".$relay_id;
        if($relay == "0"){
            $relay_action = "LIGHT_OFF";
        }else if($relay == "1"){
            $relay_action = "LIGHT_ON";
        }
        echo $relay_id;
        echo " - ";
        echo $relay_action;
        echo "<br>";
        if(isset($relay_action)){
            update_relay($house_id, $relay_id, $action, $relay_action, $tkn, $db_house_token);
        }
    }
/* Check to see if second relay set */
}else if($relayset == "2"){
    $relayset_array = str_split($action_data);
    $i = 16;
    foreach ($relayset_array as $relay) {
        $i++;
        $relay_id = sprintf("%02d", $i);
        $relay_id = "relay_".$relay_id;
        if($relay == "0"){
            $relay_action = "LIGHT_OFF";
        }else if($relay == "1"){
            $relay_action = "LIGHT_ON";
        }
        echo $relay_id;
        echo " - ";
        echo $relay_action;
        echo "<br>";
        if(isset($relay_action)){
            update_relay($house_id, $relay_id, $action, $relay_action, $tkn, $db_house_token);
        }
    }
/* Check to see if single relay */
}else if($relayset == "single_light"){
    $relay_id = get_relay_id($house_id, $relay_id_url);
    if(isset($relay_id)){
        if($action_data == "0"){
            $relay_action = "LIGHT_OFF";
        }else if($action_data == "1"){
            $relay_action = "LIGHT_ON";
        }else{
            echo "error";
        }
        echo $relay_id;
        echo " - ";
        echo $relay_action;
        echo "<br>";
        if(isset($relay_action)){
            update_relay($house_id, $relay_id, $action, $relay_action, $tkn, $db_house_token);
        }
    }else{
        echo "error";
    }
}else{
    echo "error";
}

/* Function that gets relay id based on house id and alexa name */
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

/* Function that updates relay in database */
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
        /* Check to make sure token is valid before updating database */
        if($tkn == $db_house_token){
            if($action == "update_relay"){
                /* Check to see if Arduino needs to update anything */
                if($website_last_updated == "Arduino"){}
                else if($website_last_updated == "WebSite"){}
                /* Update the garage door sensor data (Open/Closed) */
                if(isset($action_data)){
                    /* Get relay status before update */
                    $stmt = $pdo->prepare('SELECT id, relay_action FROM uap4_hc_relays WHERE house_id = :house_id AND relay_server_name = :relay_server_name LIMIT 1');
                    $stmt->execute(['house_id' => $house_id, 'relay_server_name' => $relay_id]);
                    $data_rh = $stmt->fetch();
                    $relay_id_hr = $data_rh["id"];
                    $relay_action_hr = $data_rh["relay_action"];
                    /* Update Current Relay Status in Database */
                    $sql = "UPDATE uap4_hc_relays SET relay_action = ?, last_updated_by = ? WHERE house_id = ? AND relay_server_name = ?";
                    $pdo->prepare($sql)->execute([$update_relay_action, "Arduino", $house_id, $relay_id]);
                    /* Insert data to Relay History Database */
                    if(isset($relay_id_hr)){
                      if($update_relay_action != $relay_action_hr){
                        $sql = "INSERT INTO uap4_hc_relays_history SET relay_id = ?, relay_data = ?";
                        $pdo->prepare($sql)->execute([$relay_id_hr, $update_relay_action]);
                      }
                    }
                    echo "<UPDATED>";
                }else{
                    echo "<ERROR485>";
                }
            }else{
                echo "<DO_NOTHING>";
            }
        }else{
            echo "<ERROR56461>";
        }
    }else{
        echo "<DO_NOTHING>";
    }
}




?>
