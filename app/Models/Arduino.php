<?php
/**
* Home Models
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/


namespace App\Models;

use App\System\Models,
    Libs\Database;

class Arduino extends Models {

    // Get Current Temp Based on temp_id
    public function getUserHouse($user_id){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."hc_user_perm
			WHERE
				user_id = :user_id
			ORDER BY
			  id
			DESC
			",
			array(':user_id' => $user_id));
		return $data;
	}

    // Get Current Temp Based on temp_id
    public function getHouseDaTa($house_id){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."hc_house
			WHERE
				house_id = :house_id
			ORDER BY
			  house_id
			DESC
			",
			array(':house_id' => $house_id));
		return $data;
	}

    // Get Current Temp Based on temp_id
    public function getCurrentTemp($house_id){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."hc_temps
			WHERE
        house_id = :house_id
      AND
        enable = 1
			ORDER BY
			  id
			DESC
			",
			array('house_id' => $house_id));
		return $data;
	}

    // Get Current Relay data based on relay_server_name
    public function getCurrentRelayStatus($house_id, $relay_server_name){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."hc_relays
			WHERE
				relay_server_name = :relay_server_name
            AND
                house_id = :house_id
			ORDER BY
			  id
			DESC
			",
			array(':house_id' => $house_id, ':relay_server_name' => $relay_server_name));
		return $data;
	}

    // Check to see if all Relays are ON based on relay_count
    public function checkAllRelaysON($house_id, $relay_count){
		$data = $this->db->select("
			SELECT
			    *
			FROM
			  ".PREFIX."hc_relays
			WHERE NOT
				relay_server_name = :name
            AND
                relay_action = :action
            AND
                house_id = :house_id
			",
			array(':name' => "ALL_RELAYS", ':action' => "LIGHT_ON", ':house_id' => $house_id));
        $count = count($data);
        if($count == $relay_count){
            return true;
        }else{
            return false;
        }
	}

    // Update relay action based on relay_server_name
    public function updateRelay($house_id, $relay_server_name, $relay_action){
        // Update relay in db
        $query = $this->db->update(PREFIX.'hc_relays', array('relay_action' => $relay_action, 'last_updated_by' => "WebSite"), array('house_id' => $house_id, 'relay_server_name' => $relay_server_name));
        // Update all relays to on or off if not ALL_RELAYS
        if($relay_server_name == "ALL_RELAYS"){
            if($relay_action == "ALL_ON"){
                $lights_action = "LIGHT_ON";
            }else{
                $lights_action = "LIGHT_OFF";
            }
            $query2 = $this->db->updateWhereNot(PREFIX.'hc_relays', array('relay_action' => $lights_action, 'last_updated_by' => "WebSite"), array('house_id' => $house_id), array('relay_server_name' => "ALL_RELAYS"));
            // Make sure something was updated
            $count1 = count($query);
            $count2 = count($query2);
            $count = $count1 + $count2;
        }else{
            // Make sure something was updated
            $count = count($query);
        }
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }

    // Get Relay Information for all relays that are not ALL_RELAYS
    // Get Current Relay data based on relay_server_name
    public function getLightRelaysStatus($house_id){
        $data = $this->db->select("
            SELECT
                *
            FROM
              ".PREFIX."hc_relays
            WHERE
                house_id = :house_id
            AND
                enable = 1
            AND NOT
                relay_server_name = :relay_server_name
            ORDER BY
              id
            ASC
            ",
            array(':relay_server_name' => "ALL_RELAYS", ':house_id' => $house_id));
        return $data;
    }

    // Update garage door action based on door_id
    public function updateGarageStatus($house_id, $door_id = null, $action_data = null){
        $query = $this->db->update(PREFIX.'hc_garage', array('door_button' => $action_data), array('house_id' => $house_id, 'door_id' => $door_id));
        $count = count($query);
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }

    // Get Garage Door Status  OPEN / CLOSED
    // Get Current sensor data based on door_id
    public function getGarageDoorStatus($house_id){
        $data = $this->db->select("
            SELECT
                *
            FROM
              ".PREFIX."hc_garage
            WHERE
                house_id = :house_id
            AND
                enable = 1
            ORDER BY
              id
            ASC
            ",
            array('house_id' => $house_id));
        return $data;
    }


}
