<?php
/**
 * Members Controller
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
    Libs\ErrorMessages,
    Libs\SuccessMessages,
    Libs\SimpleImage,
    App\System\Error;

define('USERS_PAGEINATOR_LIMIT', '20');  // Sets up users listing page limit

class Members extends Controller
{
    private $pages;

    public function __construct()
    {
        parent::__construct();
        $this->language->load('Members');
        $this->pages = new \Libs\Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page
    }

    /**
     * Page for list of activated accounts
     */
    public function members($set_order_by = 'ID-ASC', $current_page = '1', $search = null)
    {
        // Load the members model
        $onlineUsers = new MembersModel();

        // Let sidebar Know we are on the members page
        $data['members_page'] = true;

        // Check for orderby selection
        $data['orderby'] = $set_order_by;

        // Check to see if member is searching for a user
        if(isset($search)){
            // Set total number of rows for paginator
            $total_num_users = $onlineUsers->getTotalMembersSearch($search);
            $this->pages->setTotal($total_num_users);
            $search_url = "/".$search;
        }else{
            // Set total number of rows for paginator
            $total_num_users = $onlineUsers->getTotalMembers();
            $this->pages->setTotal($total_num_users);
            $search_url = "";
        }

        // Send page links to view
        $pageFormat = SITE_URL."Members/$set_order_by/"; // URL page where pages are
        $data['pageLinks'] = $this->pages->pageLinks($pageFormat, $search_url, $current_page);
        $data['current_page_num'] = $current_page;

        // Check to see if member is searching for a user
        if(isset($search)){
            // Display Search Info
            $data['title'] = $this->language->get('members_search_title');
            $data['welcomeMessage'] = $this->language->get('search_found').' '.$total_num_users.' '.$this->language->get('matches_for').': '.$search;
            // Get list of members that match search criteria
            $data['members'] = $onlineUsers->getMembers($data['orderby'], $this->pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT), $search);
            // Let the view know user is searching
            $data['search'] = $search;
        }else{
            // Display all members
            $data['title'] = $this->language->get('members_title');
            $data['welcomeMessage'] = $this->language->get('members_welcomemessage');
            // Get list of members
            $data['members'] = $onlineUsers->getMembers($data['orderby'], $this->pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));
            // Let the view know user is searching
            $data['search'] = false;
        }

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
          /** User is logged in - Get their data **/
          $u_id = $this->auth->user_info();
          $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
          $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
        }

        /** Get Data For Member Totals Stats Sidebar **/
        $onlineUsers = new MembersModel();
        $data['activatedAccounts'] = count($onlineUsers->getActivatedAccounts());
        $data['onlineAccounts'] = count($onlineUsers->getOnlineAccounts());

        /** Setup Breadcrumbs **/
    		$data['breadcrumbs'] = "
    			<li class='active'>".$data['title']."</li>
        ";

        Load::View("Members/Members", $data, "Members/Member-Stats-Sidebar::Left");
    }

    /**
     * Page for list of online accounts
     */
    public function online($set_order_by = 'ID-ASC', $current_page = '1')
    {
        $onlineUsers = new MembersModel();
        $data['title'] = $this->language->get('members_online_title');
        $data['welcomeMessage'] = $this->language->get('members_online_welcomemessage');

        // Check for orderby selection
        $data['orderby'] = $set_order_by;

        // Set total number of rows for paginator
        $total_num_users = count($onlineUsers->getOnlineMembers());
        $this->pages->setTotal($total_num_users);

        // Send page links to view
        $pageFormat = SITE_URL."Members/$set_order_by/"; // URL page where pages are
        $data['pageLinks'] = $this->pages->pageLinks($pageFormat, null, $current_page);
        $data['current_page_num'] = $current_page;

        // Get list of online memebers
        $data['members'] = $onlineUsers->getOnlineMembers();

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
          //** User is logged in - Get their data **/
          $u_id = $this->auth->user_info();
          $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
          $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
        }

        /** Get Data For Member Totals Stats Sidebar **/
        $onlineUsers = new MembersModel();
        $data['activatedAccounts'] = count($onlineUsers->getActivatedAccounts());
        $data['onlineAccounts'] = count($onlineUsers->getOnlineAccounts());

        /** Setup Breadcrumbs **/
    		$data['breadcrumbs'] = "
    			<li class='active'>".$data['title']."</li>
        ";

        Load::View("Members/Members", $data, "Members/Member-Stats-Sidebar::Left");
    }

    /**
     * Get profile by username
     * @param $username
     */
    public function viewProfile($user)
    {
        $onlineUsers = new MembersModel();
        $profile = $onlineUsers->getUserProfile($user);
        if(sizeof($profile)>0){
            $data['title'] = $profile[0]->username . "'s ".$this->language->get('members_profile_title');
            $data['profile'] = $profile[0];

            /** Check to see if user is logged in **/
            if($data['isLoggedIn'] = $this->auth->isLogged()){
              //** User is logged in - Get their data **/
              $u_id = $this->auth->user_info();
              $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
              $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
            }

            /** Get User's Groups **/
            $data['user_groups'] = $this->user->getUserGroupName($profile[0]->userID);

            /** Setup Breadcrumbs **/
        		$data['breadcrumbs'] = "
        			<li class='active'>".$data['title']."</li>
            ";

            Load::View("Members/View-Profile", $data);
        }
        else
            Error::profileError();
    }

    public function editProfile()
    {
        $u_id = $this->auth->currentSessionInfo()['uid'];

        $onlineUsers = new MembersModel();
        $username = $onlineUsers->getUserName($u_id);
        if(sizeof($username) > 0){

            if (isset($_POST['submit'])) {
                if(Csrf::isTokenValid('editprofile')) {
                    $first_name = strip_tags(Request::post('first_name'));
                    $last_name = strip_tags(Request::post('last_name'));
                    $gender = Request::post('gender') == 'male' ? 'Male' : 'Female';
                    $website = strip_tags(preg_replace('#^https?://#', '', Request::post('website')));
                    $aboutMe = nl2br(strip_tags(Request::post('aboutMe')));
                    $signature = nl2br(strip_tags(Request::post('signature')));
                    $userImage = Request::post('oldImg');
                    // Check to see if an image is being uploaded
                    if(!empty($_FILES['profilePic']['tmp_name'])){
                        $picture = file_exists($_FILES['profilePic']['tmp_name']) || is_uploaded_file($_FILES['profilePic']['tmp_name']) ? $_FILES ['profilePic'] : array ();
                        if(sizeof($picture)>0){
                            // Set the User's Profile Image Directory
                            $img_dir_profile = IMG_DIR_PROFILE.$username[0]->username.'/';
    				        $check = getimagesize ( $picture['tmp_name'] );
                            // Check to make sure image is good
    						if($picture['size'] < 5000000 && $check && ($check['mime'] == "image/jpeg" || $check['mime'] == "image/png" || $check['mime'] == "image/gif")){
                                // Check to see if Img Upload Directory Exists, if not create it
    							if(!file_exists(ROOTDIR.$img_dir_profile))
    								mkdir(ROOTDIR.$img_dir_profile,0777,true);
                                // Format new image and upload it to server
    							$image = new SimpleImage($picture['tmp_name']);
                                $rand_string = substr(str_shuffle(md5(time())), 0, 10);
                                $img_name = $username[0]->username.'_PROFILE_'.$rand_string.'.jpg';
    							$dir = $img_dir_profile.$img_name;
    							$image->best_fit(400,300)->save(ROOTDIR.$dir);
                                if(file_exists(ROOTDIR.$dir) && (strpos($userImage, ".") !== false)){
                                    if($userImage == 'default-1.jpg' || $userImage == 'default-2.jpg' || $userImage == 'default-3.jpg' || $userImage == 'default-4.jpg' || $userImage == 'default-5.jpg'){
                                        // Do Nothing
                                    }else{
                                        if(file_exists(ROOTDIR.IMG_DIR_PROFILE.$userImage)) {
                                            unlink(ROOTDIR.IMG_DIR_PROFILE.$userImage);
                                        }
                                    }
                                }
    						}else{
                                // Error Message Display
                                ErrorMessages::push($this->language->get('edit_profile_photo_error'), 'Edit-Profile');
                            }
                        }
                    }
                    if(!empty($img_name)){
                        $db_image = $username[0]->username.'/'.$img_name;
                    }else{
                        $db_image = $userImage;
                    }
                    /* Check to make sure First Name does not have any html char in it */
                    if($first_name != strip_tags($first_name)){
                        /* Error Message Display */
                        ErrorMessages::push($this->language->get('edit_profile_firstname_error'), 'Edit-Profile');
                    }
                    /* Check to make sure Last Name does not have any html char in it */
                    if($last_name != strip_tags($last_name)){
                        /* Error Message Display */
                        ErrorMessages::push($this->language->get('edit_profile_lastname_error'), 'Edit-Profile');
                    }
                    /* Check to make sure Website url is valid */
                    if (!empty($website)){
                        if (filter_var('http://'.$website, FILTER_VALIDATE_URL) === FALSE) {
                            /* Error Message Display */
                            ErrorMessages::push($this->language->get('edit_profile_website_error'), 'Edit-Profile');
                        }
                    }
                    /* Clean Up Aboutme and Signature from using HTML */
                    $aboutMe = strip_tags($aboutMe, "<br>");
                    $signature = strip_tags($signature, "<br>");

                    $onlineUsers->updateProfile($u_id, $first_name, $last_name, $gender, $website, $db_image, $aboutMe, $signature);
                    // Success Message Display
                    SuccessMessages::push($this->language->get('edit_profile_success'), 'Edit-Profile');
                }
                else{
                    // Error Message Display
                    ErrorMessages::push($this->language->get('edit_profile_error'), 'Edit-Profile');
                }

            }

            $username = $username[0]->username;
            $profile = $onlineUsers->getUserProfile($username);

            $data['title'] = $username . "'s ".$this->language->get('edit_profile_title');
            $data['profile'] = $profile[0];
            $data['csrfToken'] = Csrf::makeToken('editprofile');

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
              <li><a href='".SITE_URL."Account-Settings'>".$this->language->get('mem_act_settings_title')."</a></li>
        			<li class='active'>".$data['title']."</li>
            ";

            Load::View("Members/Edit-Profile", $data, "Members/Member-Account-Sidebar::Left");

        }else{
          /** User Not logged in - kick them out **/
          \Libs\ErrorMessages::push($this->language->get('user_not_logged_in'), 'Login');
        }
    }

    /**
     * Page for Account Settings Home
     */
    public function account()
    {
        $data['title'] = $this->language->get('mem_act_settings_title');
        $data['welcomeMessage'] = $this->language->get('mem_act_settings_welcomemessage');

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
    			<li class='active'>".$data['title']."</li>
        ";

        Load::View("Members/Account-Settings", $data, "Members/Member-Account-Sidebar::Left");
    }

    /**
     * Page for Privacy Settings Home
     */
    public function privacy()
    {
        $onlineUsers = new MembersModel();

        $data['title'] = $this->language->get('ps_title');
        $data['welcomeMessage'] = $this->language->get('ps_welcomemessage');
        $data['csrfToken'] = Csrf::makeToken('editprivacy');

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

        if (isset($_POST['submit'])) {
            if(Csrf::isTokenValid('editprivacy')) {
                $privacy_massemail = Request::post('privacy_massemail');
                $privacy_pm = Request::post('privacy_pm');

                if($privacy_massemail != "true"){$privacy_massemail = "false";}
                if($privacy_pm != "true"){$privacy_pm = "false";}

                if($onlineUsers->updateUPrivacy($u_id, $privacy_massemail, $privacy_pm)){
                  SuccessMessages::push($this->language->get('ps_success'), 'Privacy-Settings');
                }else{
                  ErrorMessages::push($this->language->get('ps_error'), 'Privacy-Settings');
                }
            }
        }

        /** Check users settings to see if privacy mass email is enabled or not **/
        if($data['currentUserData'][0]->privacy_massemail == "true"){
          $data['pme_checked'] = "checked";
        }
        /** Check users settings to see if privacy private message is enabled or not **/
        if($data['currentUserData'][0]->privacy_pm == "true"){
          $data['ppm_checked'] = "checked";
        }

        /** Setup Breadcrumbs **/
    		$data['breadcrumbs'] = "
          <li><a href='".SITE_URL."Account-Settings'>".$this->language->get('mem_act_settings_title')."</a></li>
    			<li class='active'>".$data['title']."</li>
        ";

        Load::View("Members/Privacy-Settings", $data, "Members/Member-Account-Sidebar::Left");
    }

    /**
     * Page for MAH Account Settings Home
     */
    public function MAHSettings()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
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

      if(sizeof($user_name) > 0){

         if (isset($_POST['submit'])) {
          if(Csrf::isTokenValid('edithouse')) {
            $new_house = strip_tags(Request::post('new_house'));
            $gen_new_house_token = strip_tags(Request::post('gen_new_house_token'));
            // Check to see if user is adding a new house profile
            if($new_house == "true"){
              $boards = strip_tags(Request::post('boards'));
              $temp_sensors = strip_tags(Request::post('temp_sensors'));
              $garage_doors = strip_tags(Request::post('garage_doors'));
              // Get new house token
              $house_token = bin2hex(openssl_random_pseudo_bytes(25));
              // Create new house profile
              $new_house_id = $onlineUsers->createMAHProfileHouse($house_token);
              // Add current user to house perm list
              $onlineUsers->createMAHProfileHousePerms($new_house_id, $u_id);
              // Create Relays Profile
              $onlineUsers->createMAHProfileHouseRelays($new_house_id, $boards);
              // Create Grage Doors Profile
              $onlineUsers->createMAHProfileHouseDoors($new_house_id, $garage_doors);
              // Create Temperature Sensors Profile
              $onlineUsers->createMAHProfileHouseTemps($new_house_id, $temp_sensors);
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
              $onlineUsers->updateMAHProfileHouse($house_id, $house_token);
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
            Load::View("Members/MAHSettings", $data, "Members/Member-Account-Sidebar::Left");
          }else{
            Load::View("Members/MAHSettingsNew", $data, "Members/Member-Account-Sidebar::Left");
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
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['temp_sensors'] = $onlineUsers->getHouseTempSensors($data['MAHprofile']->house_id);

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
              $tsUpdate = $onlineUsers->updateMAHTempSensors($house_id, $tsn, $temp_title_new, $temp_alexa_name_new, $temp_enable);
            }
            $tsUpdateCount = count($tsUpdate);
            if($tsUpdateCount > 0){
              // Success Message Display
              SuccessMessages::push("MAH Temp Sensors Settings Updated!", 'MAHTempSensors');
            }else{
              // Error Message Display
              ErrorMessages::push("Error Updating MAH Temp Sensors Settings!", 'MAHTempSensors');
            }
          }
        }
         Load::View("Members/MAHTempSensors", $data, "Members/Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Lights
     */
    public function MAHLights()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['lights'] = $onlineUsers->getHouseLights($data['MAHprofile']->house_id);

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
              $rsUpdate = $onlineUsers->updateMAHLights($house_id, $rsn, $relay_title_new, $relay_alexa_name_new, $relay_enable);
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
         Load::View("Members/MAHLights", $data, "Members/Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Inputs and Outputs
     */
    public function MAHGarageDoors()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
      $data['MAHprofileHouse'] = $MAHprofileHouse[0];

      /* Get Temp Sensors Information */
      $data['doors'] = $onlineUsers->getHouseGarageDoors($data['MAHprofile']->house_id);

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
              $dsUpdate = $onlineUsers->updateMAHGarageDoors($house_id, $dsn, $door_title_new, $door_alexa_name_new, $door_enable);
            }
            $dsUpdateCount = count($dsUpdate);
            if($dsUpdateCount > 0){
              // Success Message Display
              SuccessMessages::push("MAH Temp Sensors Settings Updated!", 'MAHGarageDoors');
            }else{
              // Error Message Display
              ErrorMessages::push("Error Updating MAH Temp Sensors Settings!", 'MAHGarageDoors');
            }
          }
        }
         Load::View("Members/MAHGarageDoors", $data, "Members/Member-Account-Sidebar::Left");

    }


    /**
     * Page for MAH Account Settings Home
     */
    public function MAHArduinoCode()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
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

      Load::View("Members/MAHArduinoCode", $data, "Members/Member-Account-Sidebar::Left");

    }

    /**
     * Page for MAH Account Settings Home
     */
    public function MAHArduinoCodeDownload()
    {
      $u_id = $this->auth->currentSessionInfo()['uid'];

      $onlineUsers = new MembersModel();
      $username = $onlineUsers->getUserName($u_id);

      $user_name = $username[0]->username;

      $MAHprofile = $onlineUsers->getMAHProfile($u_id);
      $data['MAHprofile'] = $MAHprofile[0];

      $house_id = $data['MAHprofile']->house_id;

      $MAHprofileHouse = $onlineUsers->getMAHProfileHouse($data['MAHprofile']->house_id);
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

      Load::View("Members/MAHArduinoCodeDownload", $data, "", "", false);

    }

}
