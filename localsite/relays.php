<?php
/**
* Arduino Smart Home Control Console - Relay Control File
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

if(isset($_REQUEST['relay'])){ $relay = $_REQUEST['relay']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }

// Check to make sure token is valid before updating database
if($tkn == $db_house_token){
    if($relay == "ALL"){
        // Get current action from database for ALL Relays
        $stmt = $pdo->prepare('SELECT relay_action FROM uap4_hc_relays WHERE house_id = :house_id AND relay_server_name = :relay_server_name');
        $stmt->execute(['house_id' => $house_id, 'relay_server_name' => 'ALL_RELAYS']);
        $data = $stmt->fetch();
        $action = $data["relay_action"];

        if($action == "ALL_ON"){
            // Command to turn all Relays on
            echo "<ALL_ON>";
            // Update database and change to all nothing
            $sql = "UPDATE uap4_hc_relays SET relay_action = ? WHERE house_id = ? AND relay_server_name = ?";
            $pdo->prepare($sql)->execute(["ALL_NOTHING", $house_id, "ALL_RELAYS"]);
        }
        else if($action == "ALL_OFF"){
            // Command to turn all Relays off
            echo "<ALL_OFF>";
            // Update database and change to all nothing
            $sql = "UPDATE uap4_hc_relays SET relay_action = ? WHERE house_id = ? AND relay_server_name = ?";
            $pdo->prepare($sql)->execute(["ALL_NOTHING", $house_id, "ALL_RELAYS"]);
        }
        else{
            // Do nothing
            echo "<ALL_NOTHING>";
        }
    }else if(isset($relay)){
        if($relay == "LIST"){
            // Get current action from database for ALL Relays in single string 0 off and 1 on
            $stmt = $pdo->prepare('SELECT relay_action FROM uap4_hc_relays WHERE house_id = :house_id AND relay_server_name LIKE "relay_%" ORDER BY relay_server_name ASC');
            $stmt->execute(['house_id' => $house_id]);
            $data = $stmt->fetchAll();

            echo "<";
            if(isset($data)){
                foreach ($data as $relay) {
                    // Check to see what user changed in db
                    if($relay['relay_action'] == "LIGHT_ON"){
                        // Command to turn Relay on
                        echo "1";
                    }else if($relay['relay_action'] == "LIGHT_OFF"){
                        // Command to turn Relay off
                        echo "0";
                    }else{
                        // Do nothing
                        echo "0";
                    }
                }
            }
            echo ">";
        }else{
            // Get current action from database based on relay name
            // Get current action from database for ALL Relays
            $stmt = $pdo->prepare('SELECT relay_action FROM uap4_hc_relays WHERE house_id = :house_id AND relay_server_name = :relay_name');
            $stmt->execute(['house_id' => $house_id, 'relay_name' => 'relay_'.$relay]);
            $data = $stmt->fetch();
            $action = $data["relay_action"];

            // Check to see what user changed in db
            if($action == "LIGHT_ON"){
                // Command to turn Relay on
                echo "<LIGHT_ON>";
            }else if($action == "LIGHT_OFF"){
                // Command to turn Relay off
                echo "<LIGHT_OFF>";
            }else{
                // Do nothing
                echo "<DO_NOTHING>";
            }
        }
    }else{
        // Nothing Requested
        echo "<NOTHING_REQUESTED>";
    }
}else{
    echo "<ERROR49543>";
}




?>
