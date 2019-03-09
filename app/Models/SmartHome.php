<?php
/**
* SmartHome Models
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/


namespace App\Models;

use App\System\Models,
    Libs\Database;

class SmartHome extends Models {

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
            $count1 = $query;
            $count2 = $query2;
            $count = $count1 + $count2;
        }else{
            // Make sure something was updated
            $count = $query;
            // Insert data to Relay History Database
            $data_rh = $this->db->select("SELECT id, relay_action FROM ".PREFIX."hc_relays WHERE house_id = :house_id AND relay_server_name = :relay_server_name LIMIT 1",
            array(':house_id' => $house_id, ':relay_server_name' => $relay_server_name));
            $relay_id_hr = $data_rh[0]->id;
            $relay_action_hr = $date_rh[0]->relay_action;
            if($relay_action != $relay_action_hr){
              $this->db->insert(PREFIX."hc_relays_history", array('relay_id' => $relay_id_hr, 'relay_data' => $relay_action));
            }
        }
        if(isset($count) && $count > 0){
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
        if(isset($query) && $query > 0){
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

    /**
     * Get specific user's MAH info
     * @param $user_id
     * @return array
     */
    public function getMAHProfile($user)
    {
        // Requeted profile information based on ID
        return $this->db->select("SELECT * FROM " . PREFIX . "hc_user_perm WHERE user_id = :userID", array(':userID' => $user));

    }
    public function getMAHProfileHouse($house_id)
    {
        // Requeted profile information based on ID
        return $this->db->select("SELECT * FROM " . PREFIX . "hc_house WHERE house_id = :house_id", array(':house_id' => $house_id));
    }
    public function updateMAHProfileHouse($house_id, $house_token, $email_enable_doors, $email_doors_minutes)
    {
        return $this->db->update(PREFIX.'hc_house', array('house_token' => $house_token, 'email_enable_doors' => $email_enable_doors, 'email_doors_minutes' => $email_doors_minutes), array('house_id' => $house_id));
    }
    public function getHouseTempSensors($house_id)
    {
        return $this->db->select("SELECT * FROM " . PREFIX . "hc_temps WHERE house_id = :house_id", array(':house_id' => $house_id));
    }
    public function updateMAHTempSensors($house_id, $temp_server_name, $temp_title, $temp_alexa_name, $temp_enable)
    {
        return $this->db->update(PREFIX.'hc_temps', array('temp_title' => $temp_title, 'temp_alexa_name' => $temp_alexa_name, 'enable' => $temp_enable), array('house_id' => $house_id, 'temp_server_name' => $temp_server_name));
    }
    public function getHouseLights($house_id)
    {
        return $this->db->select("SELECT * FROM " . PREFIX . "hc_relays WHERE house_id = :house_id", array(':house_id' => $house_id));
    }
    public function updateMAHLights($house_id, $relay_server_name, $relay_title, $relay_alexa_name, $relay_enable)
    {
        return $this->db->update(PREFIX.'hc_relays', array('relay_title' => $relay_title, 'relay_alexa_name' => $relay_alexa_name, 'enable' => $relay_enable), array('house_id' => $house_id, 'relay_server_name' => $relay_server_name));
    }
    public function getHouseGarageDoors($house_id)
    {
        return $this->db->select("SELECT * FROM " . PREFIX . "hc_garage WHERE house_id = :house_id", array(':house_id' => $house_id));
    }
    public function updateMAHGarageDoors($house_id, $door_id, $door_title, $door_alexa_name, $door_enable)
    {
        return $this->db->update(PREFIX.'hc_garage', array('door_title' => $door_title, 'door_alexa_name' => $door_alexa_name, 'enable' => $door_enable), array('house_id' => $house_id, 'door_id' => $door_id));
    }
    // Create new house profile and return house id
    public function createMAHProfileHouse($house_token){
      $data = $this->db->insert(PREFIX.'hc_house', array('house_token' => $house_token));
      $new_house_id = $this->db->lastInsertId('house_id');
      if($data > 0){
        return $new_house_id;
      }else{
        return false;
      }
    }
    // Create new house profile permissions
    public function createMAHProfileHousePerms($new_house_id, $u_id){
      return $this->db->insert(PREFIX.'hc_user_perm', array('house_id' => $new_house_id, 'user_id' => $u_id));
    }
    // Create new house relays profile
    public function createMAHProfileHouseRelays($new_house_id, $boards){
      // Add All Lights To House Relays
      $this->db->insert(PREFIX.'hc_relays', array('house_id' => $new_house_id, 'relay_server_name' => 'ALL_RELAYS', 'relay_title' => 'All Lights', 'relay_action' => 'ALL_OFF'));
      // Get total number of relays based on boards count
      $relay_count = $boards * 16;
      for ($x = 1; $x <= $relay_count; $x++) {
        $x = sprintf("%02d", $x);
        $this->db->insert(PREFIX.'hc_relays', array('house_id' => $new_house_id, 'relay_server_name' => 'relay_'.$x, 'relay_title' => 'Light '.$x, 'relay_action' => 'LIGHT_OFF'));
      }
      return true;
    }
    // Create new house Garage Doors profile
    public function createMAHProfileHouseDoors($new_house_id, $doors){
      // Get total number of relays based on boards count
      for ($x = 1; $x <= $doors; $x++) {
        $this->db->insert(PREFIX.'hc_garage', array('house_id' => $new_house_id, 'door_id' => $x, 'door_title' => 'Garage Door '.$x, 'door_button' => 'DO_NOTHING', 'door_status' => 'CLOSED'));
      }
      return true;
    }
    // Create new house Temp Sensors profile
    public function createMAHProfileHouseTemps($new_house_id, $temp_sensors){
      // Get total number of relays based on boards count
      for ($x = 1; $x <= $temp_sensors; $x++) {
        $this->db->insert(PREFIX.'hc_temps', array('house_id' => $new_house_id, 'temp_title' => 'Temp '.$x, 'temp_server_name' => 'temp_'.$x));
      }
      return true;
    }


    // Get Temps for day by hour based on date
    public function getTempsHourly($date, $temp_id){
		$data = $this->db->select("
      SELECT
        ROUND(AVG(temp_data)) as temp,HOUR(timestamp) as hour
      FROM
        ".PREFIX."hc_temps_history
      WHERE
        DATE(timestamp) = :date
      AND
        temp_id = :temp_id
      GROUP BY
        hour
			ORDER BY
			  hour
			ASC
			",
			array('date' => $date, 'temp_id' => $temp_id));
		return $data;
	}

  // Get Current Temp Based on temp_id
  public function getTempName($house_id, $temp_id){
    $data = $this->db->select("
      SELECT
        temp_title
      FROM
        ".PREFIX."hc_temps
      WHERE
        house_id = :house_id
      AND
        id = :temp_id
      AND
        enable = 1
      ORDER BY
        id
      DESC
      ",
      array('house_id' => $house_id, 'temp_id' => $temp_id));
    return $data[0]->temp_title;
  }

  // Clean temps history that is more than 2 weeks old.
  public function cleanTempsHistory(){
    $data = $this->db->raw("delete from ".PREFIX."hc_temps_history WHERE datediff(now(), timestamp) > 14");
    return $data;
  }

}
