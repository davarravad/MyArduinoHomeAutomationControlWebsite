<?php
/**
 * Members Models
 *
 * UserApplePie
 * @author David (DaVaR) Sargent <davar@userapplepie.com>
 * @version 4.0.0
 */

 namespace App\Models;

 use App\System\Models,
 Libs\Database;

class Members extends Models
{
    /**
     * Get all accounts that were activated
     * @return array
     */
    public function getActivatedAccounts()
    {
        return $this->db->select('SELECT * FROM oauth_users WHERE email_verified = true');
    }

    /**
     * Get all accounts that are on the Online table
     * @return array
     */
    public function getOnlineAccounts()
    {
        return $this->db->select('SELECT * FROM '.PREFIX.'users_online ');
    }

    /**
     * Get all members that are activated with info
     * @return array
     */
    public function getMembers($orderby, $limit = null, $search = null)
    {
        // Set default orderby if one is not set
        if($orderby == "UG-DESC"){
          $run_order = "g.groupName DESC";
        }else if($orderby == "UG-ASC"){
          $run_order = "g.groupName ASC";
        }else if($orderby == "UN-DESC"){
          $run_order = "u.username DESC";
        }else if($orderby == "UN-ASC"){
          $run_order = "u.username ASC";
        }else{
          // Default order
          $run_order = "u.userID ASC";
        }

        if(isset($search)){
            // Load users that match search criteria and are active
            $users = $this->db->select("
				SELECT
					u.userID,
					u.username,
					u.first_name,
                    u.last_name,
                    u.userImage,
					u.email_verified,
					ug.userID,
					ug.groupID,
					g.groupID,
					g.groupName,
					g.groupFontColor,
					g.groupFontWeight
				FROM
					oauth_users u
				LEFT JOIN
					oauth_users_groups ug
					ON u.userID = ug.userID
				LEFT JOIN
					".PREFIX."groups g
					ON ug.groupID = g.groupID
				WHERE
                    (u.username LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)
                AND
					u.email_verified = true
				GROUP BY
					u.userID
                ORDER BY
                    $run_order
                    $limit
            ", array(':search' => '%'.$search.'%'));
        }else{
            // Load all active site members
            $users = $this->db->select("
                SELECT
                    u.userID,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.userImage,
                    u.email_verified,
                    ug.userID,
                    ug.groupID,
                    g.groupID,
                    g.groupName,
                    g.groupFontColor,
                    g.groupFontWeight
                FROM
                    oauth_users u
                LEFT JOIN
                    oauth_users_groups ug
                    ON u.userID = ug.userID
                LEFT JOIN
                    ".PREFIX."groups g
                    ON ug.groupID = g.groupID
                WHERE
                    u.email_verified = true
                GROUP BY
                    u.userID
                ORDER BY
                    $run_order
                $limit
            ");
        }

        return $users;
    }

    /**
    * getTotalMembers
    *
    * Gets total count of users that are active
    *
    * @return int count
    */
    public function getTotalMembers(){
      $data = $this->db->select("
          SELECT
            *
          FROM
            oauth_users
          WHERE
  					email_verified = true
          ");
      return count($data);
    }

    /**
    * getTotalMembersSearch
    *
    * Gets total count of users found in search
    *
    * @return int count
    */
    public function getTotalMembersSearch($search = null){
      $data = $this->db->select("
            SELECT
                username,
                first_name,
                last_name
            FROM
                oauth_users
            WHERE
                (username LIKE :search OR first_name LIKE :search OR last_name LIKE :search)
            AND
  			    email_verified = true
            GROUP BY
                userID
          ", array(':search' => '%'.$search.'%'));
      return count($data);
    }

    /**
     * Get all info on members that are online
     * @return array
     */
    public function getOnlineMembers()
    {
        return $this->db->select("
				SELECT
					u.userID,
					u.username,
					u.first_name,
                    u.last_name,
                    u.userImage,
					uo.userID,
					ug.userID,
					ug.groupID,
					g.groupID,
					g.groupName,
					g.groupFontColor,
					g.groupFontWeight
				FROM
					oauth_users_online uo
				LEFT JOIN
					oauth_users u
					ON u.userID = uo.userID
				LEFT JOIN
					oauth_users_groups ug
					ON uo.userID = ug.userID
				LEFT JOIN
					".PREFIX."groups g
					ON ug.groupID = g.groupID
				GROUP BY
					u.userID
				ORDER BY
					u.userID ASC, g.groupID DESC");
    }

    /**
     * Get specific user's info
     * @param $username
     * @return array
     */
    public function getUserProfile($user)
    {
      // Check to see if profile is being requeted by userID
      if(ctype_digit($user)){
        // Requeted profile information based on ID
        return $this->db->select("
          SELECT
            u.userID,
            u.username,
            u.first_name,
            u.last_name,
            u.gender,
            u.userImage,
            u.LastLogin,
            u.SignUp,
            u.website,
            u.aboutme,
            u.signature
          FROM oauth_users u
          WHERE u.userID = :userID",
            array(':userID' => $user));
      }else{
        // Requested profile information based on Name
          return $this->db->select("
  					SELECT
  						u.userID,
  						u.username,
  						u.first_name,
              u.last_name,
  						u.gender,
  						u.userImage,
  						u.LastLogin,
  						u.SignUp,
  						u.website,
  						u.aboutme,
              u.signature
  					FROM oauth_users u
  					WHERE u.username = :username",
              array(':username' => $user));
      }
    }

    public function getUserName($id)
    {
        return $this->db->select("SELECT userID,username FROM oauth_users WHERE userID=:id",array(":id"=>$id));
    }

    public function updateProfile($u_id, $first_name, $last_name, $gender, $website, $userImage, $aboutme, $signature)
    {
        return $this->db->update('oauth_users', array('first_name' => $first_name, 'last_name' => $last_name, 'gender' => $gender, 'userImage' => $userImage, 'website' => $website, 'aboutme' => $aboutme, 'signature' => $signature), array('userID' => $u_id));
    }

    public function updateUPrivacy($u_id, $privacy_massemail, $privacy_pm)
    {
        $data = $this->db->update('oauth_users', array('privacy_massemail' => $privacy_massemail, 'privacy_pm' => $privacy_pm), array('userID' => $u_id));
        if(count($data) > 0){
          return true;
        }else{
          return false;
        }
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
    public function updateMAHProfileHouse($house_id, $house_token)
    {
        return $this->db->update(PREFIX.'hc_house', array('house_token' => $house_token), array('house_id' => $house_id));
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
      $count = count($data);
      if($count > 0){
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


}
