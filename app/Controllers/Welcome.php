<?php namespace App\Controllers;

/*
* Welcome Pages Controller
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.2.1
*/

use App\System\Controller,
    App\System\Load,
    App\Models\Welcome as WelcomeModel,
    Libs\Assets,
    App\System\Error,
    Libs\Auth\Auth as AuthHelper,
    App\Models\Users as Users,
    App\Models\Members as MembersModel,
    App\Models\SmartHome as SmartHomeModel,
    Libs\ErrorMessages;

class Welcome extends Controller {

    private $model;

    /* Call the parent construct */
    public function __construct()
    {
        parent::__construct();
        $this->language->load('Welcome');
        $this->model = new SmartHomeModel();
    }

    /* Welcome Method */
    public function Welcome(){

        $data['title'] = $this->language->get('homeText');
        $data['bodyText'] = $this->language->get('homeMessage');
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
        /** Get Data For Member Totals Stats Sidebar **/
        $onlineUsers = new MembersModel();
        $data['activatedAccounts'] = count($onlineUsers->getActivatedAccounts());
        $data['onlineAccounts'] = count($onlineUsers->getOnlineAccounts());

        // Get Current User's house perms
        $data['user_house_perms'] = $this->model->getUserHouse($u_id);
        $data['user_house_data'] = $this->model->getHouseData($data['user_house_perms'][0]->house_id);
        $current_house_id = $data['user_house_data'][0]->house_id;

        // Check to see if user has setup a home automation profile yet
        if(!isset($current_house_id)){
          // Error Message Display
          ErrorMessages::push("You Need to Create a New House Profile to use this website!", 'MAHSettings');
        }

        // Get current temp for temp_1
        $data['temps_data'] = $this->model->getCurrentTemp($current_house_id);

        // Get All Lights Data
        $all_lights_data = $this->model->getCurrentRelayStatus($current_house_id, "ALL_RELAYS");
        $all_lights_status = $all_lights_data[0]->relay_action;
        $data['all_lights_title'] = $all_lights_data[0]->relay_title;
        $all_relays_on = $this->model->checkAllRelaysON($current_house_id, "8");  // Total Number of Relays

        // Check to see if all relays are on
        if($all_relays_on){
            $all_lights_status = "ALL_ON";
        }
        // Check to see if all relays are on or off and set links and color
        if($all_lights_status == "ALL_ON"){
            $data['all_lights_status'] = "Turn OFF";
            $data['all_lights_link'] = DIR."RelayControl/Update/ALL_RELAYS/ALL_OFF";
            $data['all_lights_btn'] = "success";
        }else{
            $data['all_lights_status'] = "Turn ON";
            $data['all_lights_link'] = DIR."RelayControl/Update/ALL_RELAYS/ALL_ON";
            $data['all_lights_btn'] = "danger";
        }

        // Get Garage Status Data
        $data['garage_doors_data'] = $this->model->getGarageDoorStatus($current_house_id, "1");

        // Get Light Relays Data
        $data['light_relays_data'] = $this->model->getLightRelaysStatus($current_house_id);

        Load::View("Welcome::Welcome", $data);
    }


    /* Relay Control Method */
    public function RelayControl($action = null, $relay_server_name = null, $relay_action = null){


        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            //** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
            $data['is_mod'] = $this->auth->checkIsMod($u_id);
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You Must Be Logged In To View That Page!', 'Login');
        }

        // Get Current User's house perms
        $data['user_house_perms'] = $this->model->getUserHouse($u_id);
        $data['user_house_data'] = $this->model->getHouseData($data['user_house_perms'][0]->house_id);
        $current_house_id = $data['user_house_data'][0]->house_id;

		//Check to see if user is updating a relay
		if(!empty($action)){

			if($action == "Update"){
                // Make sure there is a trailer number
                if(empty($relay_server_name) || empty($relay_action)){
                    /** No Trailer Number Error **/
                    \Libs\ErrorMessages::push('Error With Relay Data In URL!', '');
                }
				// Run the add script
				if($this->model->updateRelay($current_house_id, $relay_server_name, $relay_action)){
				  /** Group Create Success **/
				  \Libs\SuccessMessages::push('You Have Successfully Changed Relay Status!', '');
				}else{
				  /** Group Create Error. Show Error **/
				  \Libs\ErrorMessages::push('Relay Status Update Error 01!', '');
				}
			}
		}else{
          /** Group Create Error. Show Error **/
          \Libs\ErrorMessages::push('Relay Status Update Error 02!', '');
        }

        Load::View("Welcome::Welcome", $data, "Members::Member-Stats-Sidebar::Right");
    }


    /* Garage Control Method */
    public function GarageControl($action = null, $door_id = null, $action_data = null){

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            //** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
            $data['is_mod'] = $this->auth->checkIsMod($u_id);
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You Must Be Logged In To View That Page!', 'Login');
        }

        // Get Current User's house perms
        $data['user_house_perms'] = $this->model->getUserHouse($u_id);
        $data['user_house_data'] = $this->model->getHouseData($data['user_house_perms'][0]->house_id);
        $current_house_id = $data['user_house_data'][0]->house_id;

		//Check to see if user is updating a relay
		if(!empty($action)){

			if($action == "Update"){
        // Make sure there is a trailer number
        if(empty($door_id) || empty($action_data)){
            /** No Trailer Number Error **/
            \Libs\ErrorMessages::push('Error With Garage Data In URL!', '');
        }
				// Run the add script
				if($this->model->updateGarageStatus($current_house_id, $door_id, $action_data)){
				  /** Group Create Success **/
				  \Libs\SuccessMessages::push('You Have Successfully Pushed Garage Door Button!', '');
				}else{
				  /** Group Create Error. Show Error **/
				  \Libs\ErrorMessages::push('Garage Door Status Update Error 182-01!', '');
				}
			}
		}else{
          /** Group Create Error. Show Error **/
          \Libs\ErrorMessages::push('Garage Door Status Update Error 187-02!', '');
        }

        Load::View("Welcome::Welcome", $data, "Members::Member-Stats-Sidebar::Right");
    }


    /* Templates Method
    * Used to load files within the template assets folder
    */

    public function Templates(){
        $extRoutes = $this->routes;
        if(sizeof($extRoutes) == '5'){
            Assets::loadFile($extRoutes);
        }else{
            Error::show(404);
        }
    }

    /* Assets Method
    * Used to load files within the root assets folder
    */
    public function assets(){
        $extRoutes = $this->routes;
        if(sizeof($extRoutes) == '4' || sizeof($extRoutes) == '5'){
            Assets::loadFile($extRoutes, 'assets');
        }else{
            Error::show(404);
        }
    }

}
