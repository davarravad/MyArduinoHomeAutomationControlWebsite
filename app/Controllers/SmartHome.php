<?php
/**
 * SmartHome Controller
 *
 * UserApplePie
 * @author David (DaVaR) Sargent <davar@userapplepie.com>
 * @version 4.0.0
 */

namespace App\Controllers;

use App\System\Controller,
    App\System\Load,
    Libs\Session,
    Libs\Csrf,
    Libs\Request,
    Libs\Auth\Auth as AuthHelper,
    App\Models\Users as Users,
    App\Models\Members as MembersModel,
    App\Models\SmartHome as SmartHomeModel,
    Libs\ErrorMessages,
    Libs\SuccessMessages,
    Libs\SimpleImage,
    App\System\Error;

define('USERS_PAGEINATOR_LIMIT', '20');  // Sets up users listing page limit

class SmartHome extends Controller
{
    private $pages;

    public function __construct()
    {
        parent::__construct();
        //$this->language->load('SmartHome');
        $this->pages = new \Libs\Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page
    }


    /**
     * Page for MAH Account Settings Home
     */
    public function MAHSettings()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      $data['csrfToken'] = Csrf::makeToken('edithouse');

      $data['title'] = "MAH - Settings";

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      /** Setup Breadcrumbs **/
      $data['breadcrumbs'] = "
       <li><a href='".SITE_URL."MAHSettings'>My Arduino Home</a></li>
       <li class='active'>".$data['title']."</li>
      ";

      if(isset($user_name)){

         if (isset($_POST['submit'])) {
          if(Csrf::isTokenValid('edithouse')) {
            $new_house = strip_tags(Request::post('new_house'));
            $gen_new_house_token = strip_tags(Request::post('gen_new_house_token'));
            $email_enable_doors = strip_tags(Request::post('email_enable_doors'));
            $email_doors_minutes = strip_tags(Request::post('email_doors_minutes'));
            // Check to see if user is adding a new house profile
            if($new_house == "true"){
              $boards = strip_tags(Request::post('boards'));
              $temp_sensors = strip_tags(Request::post('temp_sensors'));
              $garage_doors = strip_tags(Request::post('garage_doors'));
              // Get new house token
              $house_token = bin2hex(openssl_random_pseudo_bytes(25));
              // Create new house profile
              $new_house_id = $SmartHome->createMAHProfileHouse($house_token);
              // Add current user to house perm list
              $SmartHome->createMAHProfileHousePerms($new_house_id, $u_id);
              // Create Relays Profile
              $SmartHome->createMAHProfileHouseRelays($new_house_id, $boards);
              // Create Grage Doors Profile
              $SmartHome->createMAHProfileHouseDoors($new_house_id, $garage_doors);
              // Create Temperature Sensors Profile
              $SmartHome->createMAHProfileHouseTemps($new_house_id, $temp_sensors);
              if(isset($new_house_id)){
                // Success Message Display
                SuccessMessages::push("MAH House Settings Created!", 'MAHSettings');
              }else{
                // Error Message Display
                ErrorMessages::push("Error Updating MAH House Settings!", 'MAHSettings');
              }
            }else{
              /* Check to see if user wants to generate a new house token */
              $gen_new_house_token = strip_tags(Request::post('gen_new_house_token'));
              if($gen_new_house_token == "true"){
                $house_token = bin2hex(openssl_random_pseudo_bytes(25));
              }else{
                $house_token = strip_tags(Request::post('house_token'));
              }

              /* Check to make sure house_token does not have any html char in it */
              if($house_token != strip_tags($house_token)){
                /* Error Message Display */
                ErrorMessages::push("Error With House Token!", 'MAHSettings');
              }
              if($email_enable_doors != "1"){ $email_enable_doors = "0"; }
              $SmartHome->updateMAHProfileHouse($house_id, $house_token, $email_enable_doors, $email_doors_minutes);
              // Success Message Display
              SuccessMessages::push("MAH House Settings Updated!", 'MAHSettings');
            }
          }
          else{
            // Error Message Display
            ErrorMessages::push("Error Updating MAH House Settings!", 'MAHSettings');
          }

         }
         // Check to see if user has setup a shc profile or not
          if(isset($house_id)){
            Load::View("SmartHome/MAHSettings", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");
          }else{
            Load::View("SmartHome/MAHSettingsNew", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");
          }
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }
    }

    /**
     * Page for MAH Temp Sensors
     */
    public function MAHTempSensors()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['temp_sensors'] = $SmartHome->getHouseTempSensors($data['MAHprofile']->house_id);

      $data['csrfToken'] = Csrf::makeToken('edithouse');

      $data['title'] = "MAH - Temperature Sensors";

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      /** Setup Breadcrumbs **/
      $data['breadcrumbs'] = "
       <li><a href='".SITE_URL."MAHSettings'>My Arduino Home</a></li>
       <li class='active'>".$data['title']."</li>
      ";

        if (isset($_POST['submit'])) {
          if(Csrf::isTokenValid('edithouse')) {
            $temp_server_name = Request::post('temp_server_name');
            $temp_title = Request::post('temp_title');
            $temp_alexa_name = Request::post('temp_alexa_name');
            $enable = Request::post('enable');

            foreach ($temp_server_name as $tsn) {
              $temp_title_new = $temp_title["$tsn"];
              $temp_alexa_name_new = $temp_alexa_name["$tsn"];
              $temp_enable = $enable["$tsn"];
              if($temp_enable != "1"){ $temp_enable = "0"; }
              $tsUpdate = $SmartHome->updateMAHTempSensors($house_id, $tsn, $temp_title_new, $temp_alexa_name_new, $temp_enable);
            }
            if(isset($tsUpdate) && $tsUpdate > 0){
              // Success Message Display
              SuccessMessages::push("MAH Temp Sensors Settings Updated!", 'MAHTempSensors');
            }else{
              // Error Message Display
              ErrorMessages::push("Error Updating MAH Temp Sensors Settings! 213", 'MAHTempSensors');
            }
          }
        }
         Load::View("SmartHome/MAHTempSensors", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Lights
     */
    public function MAHLights()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['lights'] = $SmartHome->getHouseLights($data['MAHprofile']->house_id);

      $data['csrfToken'] = Csrf::makeToken('edithouse');

      $data['title'] = "MAH - Lights";

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      /** Setup Breadcrumbs **/
      $data['breadcrumbs'] = "
       <li><a href='".SITE_URL."MAHSettings'>My Arduino Home</a></li>
       <li class='active'>".$data['title']."</li>
      ";

        if (isset($_POST['submit'])) {
          if(Csrf::isTokenValid('edithouse')) {
            $relay_server_name = Request::post('relay_server_name');
            $relay_title = Request::post('relay_title');
            $relay_alexa_name = Request::post('relay_alexa_name');
            $enable = Request::post('enable');

            foreach ($relay_server_name as $rsn) {
              $relay_title_new = $relay_title["$rsn"];
              $relay_alexa_name_new = $relay_alexa_name["$rsn"];
              $relay_enable = $enable["$rsn"];
              if($relay_enable != "1"){ $relay_enable = "0"; }
              $rsUpdate = $SmartHome->updateMAHLights($house_id, $rsn, $relay_title_new, $relay_alexa_name_new, $relay_enable);
            }
            $rsUpdateCount = count($rsUpdate);
            if($rsUpdateCount > 0){
              // Success Message Display
              SuccessMessages::push("MAH Temp Sensors Settings Updated!", 'MAHLights');
            }else{
              // Error Message Display
              ErrorMessages::push("Error Updating MAH Temp Sensors Settings!", 'MAHLights');
            }
          }
        }
         Load::View("SmartHome/MAHLights", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Inputs and Outputs
     */
    public function MAHGarageDoors()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['doors'] = $SmartHome->getHouseGarageDoors($data['MAHprofile']->house_id);

      $data['csrfToken'] = Csrf::makeToken('edithouse');

      $data['title'] = "MAH - Garage Doors";

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      /** Setup Breadcrumbs **/
      $data['breadcrumbs'] = "
       <li><a href='".SITE_URL."MAHSettings'>My Arduino Home</a></li>
       <li class='active'>".$data['title']."</li>
      ";

        if (isset($_POST['submit'])) {
          if(Csrf::isTokenValid('edithouse')) {
            $door_id = Request::post('door_id');
            $door_title = Request::post('door_title');
            $door_alexa_name = Request::post('door_alexa_name');
            $enable = Request::post('enable');

            foreach ($door_id as $dsn) {
              $door_title_new = $door_title["$dsn"];
              $door_alexa_name_new = $door_alexa_name["$dsn"];
              $door_enable = $enable["$dsn"];
              if($door_enable != "1"){ $door_enable = "0"; }
              $dsUpdate = $SmartHome->updateMAHGarageDoors($house_id, $dsn, $door_title_new, $door_alexa_name_new, $door_enable);
            }
            $dsUpdateCount = count($dsUpdate);
            if($dsUpdateCount > 0){
              // Success Message Display
              SuccessMessages::push("MAH Garage Door Settings Updated!", 'MAHGarageDoors');
            }else{
              // Error Message Display
              ErrorMessages::push("Error Updating MAH Garage Door Settings!", 'MAHGarageDoors');
            }
          }
        }
         Load::View("SmartHome/MAHGarageDoors", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");

    }


    /**
     * Page for MAH Account Settings Home
     */
    public function MAHArduinoCode()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      $data['csrfToken'] = Csrf::makeToken('edithouse');

      $data['title'] = "MAH - Arduino Code";

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      /** Setup Breadcrumbs **/
      $data['breadcrumbs'] = "
       <li><a href='".SITE_URL."MAHSettings'>My Arduino Home</a></li>
       <li class='active'>".$data['title']."</li>
      ";

      Load::View("SmartHome/MAHArduinoCode", $data, "SmartHome/MAH-Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Account Settings Home
     */
    public function MAHArduinoCodeDownload()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $SmartHome = new SmartHomeModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $SmartHome->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $SmartHome->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /** Check to see if user is logged in **/
      if($data['isLoggedIn'] = $this->auth->isLogged()){
       //** User is logged in - Get their data **/
       $u_id = $this->auth->user_info();
       $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
       $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
      }else{
       /** User Not logged in - kick them out **/
       \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
      }

      Load::View("SmartHome/MAHArduinoCodeDownload", $data, "", "", false);

    }

    /* MAH Temps Method */
    public function MAHTemps($temp_id = null){
        $SmartHome = new SmartHomeModel();

        $data['title'] = "Current House Temps";
        $data['bodyText'] = "Welcome to your current house temperatures.";
        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            //** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
        }

        // Get Current User's house perms
        $data['user_house_perms'] = $SmartHome->getUserHouse($u_id);
        $data['user_house_data'] = $SmartHome->getHouseData($data['user_house_perms'][0]->house_id);
        $current_house_id = $data['user_house_data'][0]->house_id;

        // Check to see if user has setup a home automation profile yet
        if(!isset($current_house_id)){
          // Error Message Display
          ErrorMessages::push("You Need to Create a New House Profile to use this website!", 'MAHSettings');
        }

        // Clean up temp history data that is more than two weeks old
        $data['clean_temp_history'] = $SmartHome->cleanTempsHistory();

        // Get current temp for temp_1
        $data['current_temps_data'] = $SmartHome->getCurrentTemp($current_house_id);

        if(isset($temp_id)){
          $data['temp_sensor_name'] = $SmartHome->getTempName($current_house_id, $temp_id);
          $data['temps_today'] = $SmartHome->getTempsHourly(date('Y-m-d', time()), $temp_id);
          $data['temps_yesterday'] = $SmartHome->getTempsHourly(date('Y-m-d', strtotime("-1 days")), $temp_id);
        }

        Load::View("SmartHome::Temps", $data);
    }


}
