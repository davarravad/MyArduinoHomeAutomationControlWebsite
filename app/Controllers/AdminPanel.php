<?php
/**
 * Admin Panel Controller
 *
 * UserApplePie
 * @author David (DaVaR) Sargent <davar@userapplepie.com>
 * @version 4.2.1
 */

namespace App\Controllers;

use App\System\Controller,
    App\System\Load,
    Libs\Auth\Auth,
    Libs\Csrf,
    Libs\Request,
    App\Models\AdminPanel as AdminPanelModel,
    App\System\Error,
    App\Models\Members as MembersModel,
    App\Routes;

define('USERS_PAGEINATOR_LIMIT', '20');  // Sets up users listing page limit

class AdminPanel extends Controller{

  private $model;
  private $pages;

  public function __construct(){
    parent::__construct();
    $this->model = new AdminPanelModel();
    $this->pages = new \Libs\Paginator(USERS_PAGEINATOR_LIMIT);  // How many rows per page
  }

  public function Dashboard(){
    // Get data for dashboard
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Dashboard";
    $data['welcomeMessage'] = "Welcom to the Admin Panel Dashboard!";

    /** Get Data For Member Totals Stats Sidebar **/
    $onlineUsers = new MembersModel();
    $data['activatedAccounts'] = count($onlineUsers->getActivatedAccounts());
    $data['onlineAccounts'] = count($onlineUsers->getOnlineAccounts());

    /** Get Count Data For Groups **/
    $data['usergroups'] = count($this->model->getAllGroups());

    /** Get Count of Members that Have Logged In Past Days **/
    $data['mem_login_past_1'] = count($this->model->getPastUsersData('LastLogin', '1'));
    $data['mem_login_past_7'] = count($this->model->getPastUsersData('LastLogin', '7'));
    $data['mem_login_past_30'] = count($this->model->getPastUsersData('LastLogin', '30'));
    $data['mem_login_past_90'] = count($this->model->getPastUsersData('LastLogin', '90'));
    $data['mem_login_past_365'] = count($this->model->getPastUsersData('LastLogin', '365'));

    /** Get Count of Members that Have Signed Up In Past Days **/
    $data['mem_signup_past_1'] = count($this->model->getPastUsersData('SignUp', '1'));
    $data['mem_signup_past_7'] = count($this->model->getPastUsersData('SignUp', '7'));
    $data['mem_signup_past_30'] = count($this->model->getPastUsersData('SignUp', '30'));
    $data['mem_signup_past_90'] = count($this->model->getPastUsersData('SignUp', '90'));
    $data['mem_signup_past_365'] = count($this->model->getPastUsersData('SignUp', '365'));

    /** Get total page views count **/
    $data['totalPageViews'] = \Libs\SiteStats::getTotalViews();

    /** Function to check if the files exist (prevent errors when mother server is down) **/
    function UR_exists($url){
      $headers=get_headers($url);
      return stripos($headers[0],"200 OK")?true:false;
    }

    /** Get Current UAP Version Data From UserApplePie.com **/
    $check_url = 'https://www.userapplepie.com/uapversion.php?getversion=UAP';
    if(UR_exists($check_url)){
      $html = file_get_contents($check_url);
      preg_match("/UAP v(.*) UAP/i", $html, $match);
      $cur_uap_version = UAPVersion;
      if($cur_uap_version < $match[1]){ $data['cur_uap_version'] = $match[1]; }
    }

    /** Check to see if Forum Plugin is Installed  **/
    if(file_exists(ROOTDIR.'app/Plugins/Forum/Controllers/Forum.php')){
      $forum_status = "Installed";
      /** Get Current UAP Version Data From UserApplePie.com **/
      $check_url = 'https://www.userapplepie.com/uapversion.php?getversion=Forum';
      if(UR_exists($check_url)){
        $html = file_get_contents($check_url);
        preg_match("/UAP-Forum v(.*) UAP-Forum/i", $html, $match);
        require_once(ROOTDIR.'app/Plugins/Forum/ForumVersion.php');
        $cur_uap_forum_version = UAPForumVersion;
        if($cur_uap_forum_version < $match[1]){ $data['cur_uap_forum_version'] = $match[1]; }
      }
    }else{
      $forum_status = "NOT Installed";
    }
    $data['apd_plugin_forum'] = $forum_status;

    /** Check to see if Private Messages Plugin is Installed **/
    if(file_exists(ROOTDIR.'app/Plugins/Messages/Controllers/Messages.php')){
      $msg_status = "Installed";
      /** Get Current UAP Version Data From UserApplePie.com **/
      $check_url = 'https://www.userapplepie.com/uapversion.php?getversion=Messages';
      if(UR_exists($check_url)){
        $html = file_get_contents($check_url);
        preg_match("/UAP-Messages v(.*) UAP-Messages/i", $html, $match);
        require_once(ROOTDIR.'app/Plugins/Messages/MessagesVersion.php');
        $cur_uap_messages_version = UAPMessagesVersion;
        if($cur_uap_messages_version < $match[1]){ $data['cur_uap_messages_version'] = $match[1]; }
      }
    }else{
      $msg_status = "NOT Installed";
    }
    $data['apd_plugin_message'] = $msg_status;

    /** Check to see if Friends Plugin is Installed **/
    if(file_exists(ROOTDIR.'app/Plugins/Friends/Controllers/Friends.php')){
      $friends_status = "Installed";
      /** Get Current UAP Version Data From UserApplePie.com **/
      $check_url = 'https://www.userapplepie.com/uapversion.php?getversion=Friends';
      if(UR_exists($check_url)){
        $html = file_get_contents($check_url);
        preg_match("/UAP-Friends v(.*) UAP-Friends/i", $html, $match);
        require_once(ROOTDIR.'app/Plugins/Friends/FriendsVersion.php');
        $cur_uap_friends_version = UAPFriendsVersion;
        if($cur_uap_friends_version < $match[1]){ $data['cur_uap_friends_version'] = $match[1]; }
      }
    }else{
      $friends_status = "NOT Installed";
    }
    $data['apd_plugin_friends'] = $friends_status;

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-dashboard'></i> ".$data['title']."</li>
    ";

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    Load::View("AdminPanel/AdminPanel", $data, "", "AdminPanel");
  }

    /*
    ** Admin Panel Site Settings
    ** Allows admins to change all site settings except database
    */
    public function Settings(){
        /* Get data for dashboard */
        $data['current_page'] = $_SERVER['REQUEST_URI'];
        $data['title'] = "Settings";
        $data['welcomeMessage'] = "Welcom to the Admin Panel Site Settings!";

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            /** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
                /** User Not Admin - kick them out **/
                \Libs\ErrorMessages::push('You are Not Admin', '');
            }
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
        }

        /* Check to see if Admin is submiting form data */
        if(isset($_POST['submit'])){
            /* Check to make sure the csrf token is good */
            if (Csrf::isTokenValid('settings')) {
                /* Check to make sure Admin is updating settings */
                if($_POST['update_settings'] == "true"){
                    /* Get data sbmitted by form */
                    $site_title = Request::post('site_title');
                    $site_description = Request::post('site_description');
                    $site_keywords = Request::post('site_keywords');
                    $site_user_activation = Request::post('site_user_activation');
                    $site_email_username = Request::post('site_email_username');
                    $site_email_password = Request::post('site_email_password');
                    $site_email_fromname = Request::post('site_email_fromname');
                    $site_email_host = Request::post('site_email_host');
                    $site_email_port = Request::post('site_email_port');
                    $site_email_smtp = Request::post('site_email_smtp');
                    $site_email_site = Request::post('site_email_site');
                    $site_recapcha_public = Request::post('site_recapcha_public');
                    $site_recapcha_private = Request::post('site_recapcha_private');
                    $site_theme = Request::post('site_theme');

                    if($this->model->updateSetting('site_title', $site_title)){}else{ $errors[] = 'Site Title Error'; }
                    if($this->model->updateSetting('site_description', $site_description)){}else{ $errors[] = 'Site Description Error'; }
                    if($this->model->updateSetting('site_keywords', $site_keywords)){}else{ $errors[] = 'Site Keywords Error'; }
                    if($this->model->updateSetting('site_user_activation', $site_user_activation)){}else{ $errors[] = 'Site User Activation Error'; }
                    if($this->model->updateSetting('site_email_username', $site_email_username)){}else{ $errors[] = 'Site Email Username Error'; }
                    if($this->model->updateSetting('site_email_password', $site_email_password)){}else{ $errors[] = 'Site Email Password Error'; }
                    if($this->model->updateSetting('site_email_fromname', $site_email_fromname)){}else{ $errors[] = 'Site Email From Name Error'; }
                    if($this->model->updateSetting('site_email_host', $site_email_host)){}else{ $errors[] = 'Site Email Host Error'; }
                    if($this->model->updateSetting('site_email_port', $site_email_port)){}else{ $errors[] = 'Site Email Port Error'; }
                    if($this->model->updateSetting('site_email_smtp', $site_email_smtp)){}else{ $errors[] = 'Site Email SMTP Auth Error'; }
                    if($this->model->updateSetting('site_email_site', $site_email_site)){}else{ $errors[] = 'Site Email Error'; }
                    if($this->model->updateSetting('site_recapcha_public', $site_recapcha_public)){}else{ $errors[] = 'Site reCAPCHA Public Error'; }
                    if($this->model->updateSetting('site_recapcha_private', $site_recapcha_private)){}else{ $errors[] = 'Site reCAPCHA Private Error'; }
                    if($this->model->updateSetting('site_theme', $site_theme)){}else{ $errors[] = 'Site Theme Error'; }

                    // Run the update profile script
                    if(!isset($errors) || count($errors) == 0){
                        // Success
                        \Libs\SuccessMessages::push('You Have Successfully Updated Site Settings', 'AdminPanel-Settings');
                    }else{
                        // Error
                        if(isset($errors)){
                            $error_data = "<hr>";
                            foreach($errors as $row){
                                $error_data .= " - ".$row."<br>";
                            }
                        }else{
                            $error_data = "";
                        }
                        /* Error Message Display */
                        \Libs\ErrorMessages::push('Error Updating Site Settings'.$error_data, 'AdminPanel-Settings');
                    }
                }else{
                    /* Error Message Display */
                    \Libs\ErrorMessages::push('Error Updating Site Settings', 'AdminPanel-Settings');
                }
            }else{
                /* Error Message Display */
                \Libs\ErrorMessages::push('Error Updating Site Settings', 'AdminPanel-Settings');
            }
        }

        /* Get Settings Data */
        $data['site_title'] = $this->model->getSettings('site_title');
        $data['site_description'] = $this->model->getSettings('site_description');
        $data['site_keywords'] = $this->model->getSettings('site_keywords');
        $data['site_user_activation'] = $this->model->getSettings('site_user_activation');
        $data['site_email_username'] = $this->model->getSettings('site_email_username');
        $data['site_email_password'] = $this->model->getSettings('site_email_password');
        $data['site_email_fromname'] = $this->model->getSettings('site_email_fromname');
        $data['site_email_host'] = $this->model->getSettings('site_email_host');
        $data['site_email_port'] = $this->model->getSettings('site_email_port');
        $data['site_email_smtp'] = $this->model->getSettings('site_email_smtp');
        $data['site_email_site'] = $this->model->getSettings('site_email_site');
        $data['site_recapcha_public'] = $this->model->getSettings('site_recapcha_public');
        $data['site_recapcha_private'] = $this->model->getSettings('site_recapcha_private');
        $data['site_theme'] = $this->model->getSettings('site_theme');

        /* Setup Token for Form */
        $data['csrfToken'] = Csrf::makeToken('settings');

        /* Setup Breadcrumbs */
        $data['breadcrumbs'] = "
          <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
          <li class='breadcrumb-item active'><i class='fa fa-fw fa-dashboard'></i> ".$data['title']."</li>
        ";

        Load::View("AdminPanel/Settings", $data, "", "AdminPanel");
    }



  public function Users($set_order_by = 'ID-ASC', $current_page = '1'){

    // Check for orderby selection
    $data['orderby'] = $set_order_by;

    // Set total number of rows for paginator
    $total_num_users = $this->model->getTotalUsers();
    $this->pages->setTotal($total_num_users);

    // Send page links to view
    $pageFormat = DIR."AdminPanel-Users/$set_order_by/"; // URL page where pages are
    $data['pageLinks'] = $this->pages->pageLinks($pageFormat, null, $current_page);
    $data['current_page_num'] = $current_page;

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Users";
    $data['welcomeMessage'] = "Welcome to the Users Admin Panel";
    $data['users_list'] = $this->model->getUsers($data['orderby'], $this->pages->getLimit($current_page, USERS_PAGEINATOR_LIMIT));

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    Load::View("AdminPanel/Users", $data, "", "AdminPanel");
  }

  public function User($id){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "User";
    $data['welcomeMessage'] = "Welcome to the User Admin Panel";
    $data['csrfToken'] = Csrf::makeToken('user');

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    // Get groups user is and is not member of
    foreach ($data_groups as $value) {
      $data_user_groups = $this->model->checkUserGroup($id, $value->groupID);
      if($data_user_groups){
        $group_member[] = $value->groupID;
      }else{
        $group_not_member[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($group_member)){
      foreach ($group_member as $value) {
        $group_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_member_groups'] = $group_member_data;
    // Gether group data for group user is not member of
    if(isset($group_not_member)){
      foreach ($group_not_member as $value) {
        $group_notmember_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_notmember_groups'] = $group_notmember_data;

    // Check to make sure admin is trying to update user profile
		if(isset($_POST['submit'])){

			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('user')) {
                if($_POST['update_profile'] == "true"){
                    // Catch password inputs using the Request helper
                    $au_id = Request::post('au_id');
                    $au_username = Request::post('au_username');
                    $au_email = Request::post('au_email');
                    $au_firstName = Request::post('au_firstName');
                    $au_lastName = Request::post('au_lastName');
                    $au_gender = Request::post('au_gender');
                    $au_website = Request::post('au_website');
                    $au_userImage = Request::post('au_userImage');
                    $au_aboutme = Request::post('au_aboutme');
                    $au_signature = Request::post('au_signature');

                    // Run the update profile script
                    if($this->model->updateProfile($au_id, $au_username, $au_firstName, $au_lastName, $au_email, $au_gender, $au_website, $au_userImage, $au_aboutme, $au_signature)){
                        // Success
                        \Libs\SuccessMessages::push('You Have Successfully Updated User Profile', 'AdminPanel-User/'.$au_id);
                    }else{
                        /** User Update Fail. Show Error **/
                        \Libs\ErrorMessages::push('Profile Update Failed!', 'AdminPanel-User/'.$au_id);
                    }
                }

                // Check to see if admin is removing user from group
                if($_POST['remove_group'] == "true"){
                    // Get data from post
                    $au_userID = Request::post('au_userID');
                    $au_groupID = Request::post('au_groupID');
                    // Check to make sure Admin is not trying to remove user's last group
                    if($this->model->checkUserGroupsCount($au_userID)){
                        // Updates current user's group
                        if($this->model->removeFromGroup($au_userID, $au_groupID)){
                        	// Success
                            \Libs\SuccessMessages::push('You Have Successfully Removed User From Group', 'AdminPanel-User/'.$au_userID);
                        }else{
                            /** User Update Fail. Show Error **/
                            \Libs\ErrorMessages::push('Remove From Group Failed!', 'AdminPanel-User/'.$au_userID);
                        }
                    }else{
                        /** User Update Fail. Show Error **/
                        \Libs\ErrorMessages::push('User Must Be a Member of at least ONE Group!', 'AdminPanel-User/'.$au_userID);
                    }
                }

        // Check to see if admin is adding user to group
        if($_POST['add_group'] == "true"){
          // Get data from post
          $au_userID = Request::post('au_userID');
          $au_groupID = Request::post('au_groupID');
          // Updates current user's group
  				if($this->model->addToGroup($au_userID, $au_groupID)){
  					// Success
            \Libs\SuccessMessages::push('You Have Successfully Added User to Group', 'AdminPanel-User/'.$au_userID);
  				}else{
                    /** User Update Fail. Show Error **/
                    \Libs\ErrorMessages::push('Add to Group Failed!', 'AdminPanel-User/'.$au_id);
  				}
        }

        // Check to see if admin wants to activate user
        if($_POST['activate_user'] == "true"){
          $au_id = Request::post('au_id');
          // Run the Activation script
  				if($this->model->activateUser($au_id)){
  					// Success
            \Libs\SuccessMessages::push('You Have Successfully Activated User', 'AdminPanel-User/'.$au_id);
  				}else{
                    /** User Update Fail. Show Error **/
                    \Libs\ErrorMessages::push('Activate User Failed!', 'AdminPanel-User/'.$au_id);
  				}
        }

        // Check to see if admin wants to deactivate user
        if($_POST['deactivate_user'] == "true"){
          $au_id = Request::post('au_id');
          // Run the Activation script
  				if($this->model->deactivateUser($au_id)){
  					// Success
            \Libs\SuccessMessages::push('You Have Successfully Deactivated User', 'AdminPanel-User/'.$au_id);
  				}else{
                    /** User Update Fail. Show Error **/
                    \Libs\ErrorMessages::push('Deactivate User Failed!', 'AdminPanel-User/'.$au_id);
  				}
        }

      }
		}

    // Setup Current User data
		// Get user data from user's database
		$data['user_data'] = $this->model->getUser($id);

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel-Users'><i class='fa fa-fw fa-user'></i> Users </a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>User - ".$data['user_data'][0]->username."</li>
    ";

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    Load::View("AdminPanel/User", $data, "", "AdminPanel");
  }

  // Setup Groups Page
  public function Groups(){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Groups";
    $data['welcomeMessage'] = "Welcome to the Groups Admin Panel";
    $data['groups_list'] = $this->model->getGroups($data['orderby']);
    $data['csrfToken'] = Csrf::makeToken('groups');

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-group'></i> ".$data['title']."</li>
    ";

    // Check to make sure admin is trying to create group
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('groups')) {
        //Check for create group
        if($_POST['create_group'] == "true"){
          // Catch password inputs using the Request helper
          $ag_groupName = Request::post('ag_groupName');
          if(!empty($ag_groupName)){
            // Run the update group script
            $new_group_id = $this->model->createGroup($ag_groupName);
            if($new_group_id){
              /** Group Create Success **/
              \Libs\SuccessMessages::push('You Have Successfully Created a New Group', 'AdminPanel-Group/'.$new_group_id);
            }else{
              /** Group Create Error. Show Error **/
              \Libs\ErrorMessages::push('Group Creation Error!', 'AdminPanel-Groups');
            }
          }else{
            /** Group Name Field Empty. Show Error **/
            \Libs\ErrorMessages::push('Group Creation Error: Group Name Field Empty!', 'AdminPanel-Groups');
          }
        }
      }
    }

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    Load::View("AdminPanel/Groups", $data, "", "AdminPanel");
  }

  // Setup Group Page
  public function Group($id){

    // Check for orderby selection
    $data['orderby'] = Request::post('orderby');

    // Get data for users
    $data['current_page'] = $_SERVER['REQUEST_URI'];
    $data['title'] = "Group";
    $data['welcomeMessage'] = "Welcome to the Group Admin Panel";
    $data['csrfToken'] = Csrf::makeToken('group');

    // Get user groups data
    $data_groups = $this->model->getAllGroups();
    // Get groups user is and is not member of
    foreach ($data_groups as $value) {
      $data_user_groups = $this->model->checkUserGroup($id, $value->groupID);
      if($data_user_groups){
        $group_member[] = $value->groupID;
      }else{
        $group_not_member[] = $value->groupID;
      }
    }
    // Gether group data for group user is member of
    if(isset($group_member)){
      foreach ($group_member as $value) {
        $group_member_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_member_groups'] = $group_member_data;
    // Gether group data for group user is not member of
    if(isset($group_not_member)){
      foreach ($group_not_member as $value) {
        $group_notmember_data[] = $this->model->getGroupData($value);
      }
    }
    // Push group data to view
    $data['user_notmember_groups'] = $group_notmember_data;

    // Check to make sure admin is trying to update group data
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('group')) {
        // Check for update group
        if($_POST['update_group'] == "true"){
  				// Catch password inputs using the Request helper
          $ag_groupID = Request::post('ag_groupID');
          $ag_groupName = Request::post('ag_groupName');
          $ag_groupDescription = Request::post('ag_groupDescription');
  				$ag_groupFontColor = Request::post('ag_groupFontColor');
  				$ag_groupFontWeight = Request::post('ag_groupFontWeight');

  				// Run the update group script
  				if($this->model->updateGroup($ag_groupID, $ag_groupName, $ag_groupDescription, $ag_groupFontColor, $ag_groupFontWeight)){
  					// Success
            \Libs\SuccessMessages::push('You Have Successfully Updated a Group', 'AdminPanel-Group/'.$ag_groupID);
  				}else{
  					// Fail
  					$error[] = "Group Update Failed";
  				}
        }
        //Check for delete group
        if($_POST['delete_group'] == "true"){
          // Catch password inputs using the Request helper
          $ag_groupID = Request::post('ag_groupID');

          // Run the update group script
          if($this->model->deleteGroup($ag_groupID)){
            // Success
            \Libs\SuccessMessages::push('You Have Successfully Deleted a Group', 'AdminPanel-Groups');
          }else{
            // Fail
            $error[] = "Group Delete Failed";
          }
        }
      }
		}

    // Setup Current User data
		// Get user data from user's database
		$current_group_data = $this->model->getGroup($id);
		foreach($current_group_data as $group_data){
      $data['g_groupID'] = $group_data->groupID;
			$data['g_groupName'] = $group_data->groupName;
			$data['g_groupDescription'] = $group_data->groupDescription;
			$data['g_groupFontColor'] = $group_data->groupFontColor;
			$data['g_groupFontWeight'] = $group_data->groupFontWeight;
		}

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel-Groups'><i class='fa fa-fw fa-group'></i> Groups </a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-group'></i> Group - ".$data['g_groupName']."</li>
    ";

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    Load::View("AdminPanel/Group", $data, "", "AdminPanel");
  }

  /**
  * Mass Email Function
  * Allows Admin to Send an Email to All Members
  **/
  public function MassEmail(){

    /** Check to see if user is logged in **/
    if($data['isLoggedIn'] = $this->auth->isLogged()){
      /** User is logged in - Get their data **/
      $u_id = $this->auth->user_info();
      $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
      if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
        /** User Not Admin - kick them out **/
        \Libs\ErrorMessages::push('You are Not Admin', '');
      }
    }else{
      /** User Not logged in - kick them out **/
      \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
    }

    /** Setup Title and Welcome Message **/
    $data['title'] = "Mass Email";
    $data['welcomeMessage'] = "Welcome to the Mass Email Admin Feature.  This feature will send an email to All site members that have not disabled the feature.";

    $data['get_users_massemail_allow'] = $this->model->getUsersMassEmail();
    $data['csrfToken'] = Csrf::makeToken('massemail');

    // Setup Breadcrumbs
    $data['breadcrumbs'] = "
      <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
      <li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
    ";

    (isset($_SESSION['subject'])) ? $data['subject'] = $_SESSION['subject'] : $data['subject'] = "";
    unset($_SESSION['subject']);
    (isset($_SESSION['content'])) ? $data['content'] = $_SESSION['content'] : $data['content'] = "";
    unset($_SESSION['content']);

    // Check to make sure admin is trying to create group
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('massemail')) {
        // Catch password inputs using the Request helper
        $subject = Request::post('subject');
        $content = Request::post('content');
        if(empty($subject)){ $errormsg[] = "Subject Field Blank!"; }
        if(empty($content)){ $errormsg[] = "Content Field Blank!"; }
        $error_count = count($errormsg);
        if($error_count == 0){
          // Run the mass email script
          foreach ($data['get_users_massemail_allow'] as $row) {
            if($this->model->sendMassEmail($row->userID, $u_id, $subject, $content, $row->username, $row->email)){
              $count = $count + 1;
            }
          }
          if($count > 0){
            /** Success **/
            \Libs\SuccessMessages::push('You Have Successfully Sent Mass Email to '.$count.' Users', 'AdminPanel-MassEmail');
          }else{
            /** Fail **/
            \Libs\ErrorMessages::push('Mass Email Error', 'AdminPanel-MassEmail');
          }
        }else{
          $me_errors = "<hr>";
          foreach ($errormsg as $row) {
            $me_errors .= $row."<Br>";
          }
          /** Fail **/
          $_SESSION['subject'] = $subject;
          $_SESSION['content'] = $content;
          \Libs\ErrorMessages::push('Mass Email Error'.$me_errors, 'AdminPanel-MassEmail');
        }
      }
    }

    Load::View("AdminPanel/MassEmail", $data, "", "AdminPanel");
  }

    /**
    * System Routes Function
    * Allows Admin Quickly Find and Add new routes
    * Searches for all Classes and Methods within
    * Controller folders
    **/
    public function SystemRoutes(){

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            /** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
                /** User Not Admin - kick them out **/
                \Libs\ErrorMessages::push('You are Not Admin', '');
            }
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
        }

        /** Setup Title and Welcome Message **/
        $data['title'] = "System Routes";
        $data['welcomeMessage'] = "Welcome to the System Routes.  In order for any page to work on this system, it must be setup here.";

        /** Setup Breadcrumbs **/
        $data['breadcrumbs'] = "
          <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
          <li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
        ";


        function checkCoreRoutes($class, $method){
            $auto_cm = array("controller" => $class,"method" => $method);

            /** Get Core Routes **/
            $core_routes = Routes::all();
            foreach ($core_routes as $cr) {
                if($class == $cr['controller'] && $method == $cr['method']){
                    $match[] = true;
                }
            }
            $match_count = count($match);
            if($match_count > 0){
                return false;
            }else{
                return true;
            }
        }

        /** Check the following Directory for classes and methods **/
        $directory = APPDIR.'Controllers';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        /** Extract the methods from the classes **/
        foreach ($scanned_directory as $filename) {
            /** Remove the .php from the files names to get Class Names **/
            $class = str_replace('.php', '', str_replace('-', ' ', $filename));
            /** Get array of class methods **/
            $class_methods = get_class_methods('App\\Controllers\\'.$class);
            /** Remove blank and __construct methods from array **/
            if($class_methods[0] == ""){
                unset($class_methods[0]);
            }
            if($class_methods[0] == "__construct"){
                unset($class_methods[0]);
            }
            if($class_methods[1] == "__construct"){
                unset($class_methods[1]);
            }

            foreach ($class_methods as $method) {
                if(checkCoreRoutes($class, $method)){
                    $routes[] = array(
                        "controller" => $class,
                        "method" => $method
                    );
                }
            }
        }

        /** Check all plugin folders for Controllers **/
        $plugins_directory = APPDIR.'Plugins';
        foreach(glob($plugins_directory.'/*', GLOB_ONLYDIR) as $dir) {
            $dirname = basename($dir);
            $directory = $plugins_directory.'/'.$dirname.'/Controllers';
            $scanned_directory = array_diff(scandir($directory), array('..', '.'));

            /** Extract the methods from the classes **/
            foreach ($scanned_directory as $filename) {
                /** Remove the .php from the files names to get Class Names **/
                $class = str_replace('.php', '', str_replace('-', ' ', $filename));
                /** Get array of class methods **/
                $class_methods = get_class_methods('App\\Plugins\\'.$dirname.'\\Controllers\\'.$class);
                /** Remove blank and __construct methods from array **/
                if($class_methods[0] == ""){
                    unset($class_methods[0]);
                }
                if($class_methods[0] == "__construct"){
                    unset($class_methods[0]);
                }
                if($class_methods[1] == "__construct"){
                    unset($class_methods[1]);
                }

                foreach ($class_methods as $method) {
                    $plugin_class = "Plugins\\".$dirname."\\Controllers\\".$class;
                    if(checkCoreRoutes($plugin_class, $method)){
                        $routes[] = array(
                            "controller" => $plugin_class,
                            "method" => $method
                        );
                    }
                }
            }
        }


        /** Set new_routes default blank **/
        $new_routes = null;

        /** Check database to see if all routes are included. **/
        if(isset($routes)){
            foreach ($routes as $single_route) {
                /** Check to see if route exist in database **/
                if($this->model->checkForRoute($single_route['controller'], $single_route['method'])){
                    /** Controller and Modthod Already Exist **/
                    /** Might have it do soemthing later... **/
                }else{
                    /** Controller and Method Do Not Exist in Database **/
                    /** Add Controller and Method to Database **/
                    if($this->model->addRoute($single_route['controller'], $single_route['method'])){
                        $new_routes[] = $single_route['controller']." - ".$single_route['method']."<Br>";
                    }
                }
            }
        }

        /** Check to see if any new routes were added to database **/
        $new_routes_count = count($new_routes);
        if($new_routes_count > 0){
            /** Format New Rutes for Success Message **/
            $new_routes_display = implode(" ", $new_routes);
            /** Success **/
            \Libs\SuccessMessages::push('New Routes Have Been Added to Database!<Br><br>'.$new_routes_display, 'AdminPanel-SystemRoutes');
        }

        $data['all_routes'] = $routes;

        /** Get list of System Routes from Database **/
        $data['system_routes'] = $this->model->getAllRoutes();

        /** Load The View **/
        Load::View("AdminPanel/SystemRoutes", $data, "", "AdminPanel");

    }



    /**
    * System Route Function
    * Allows Admin Edit System Route
    **/
    public function SystemRoute($id){

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            /** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            if($data['isAdmin'] = $this->user->checkIsAdmin($u_id) == 'false'){
                /** User Not Admin - kick them out **/
                \Libs\ErrorMessages::push('You are Not Admin', '');
            }
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You are Not Logged In', 'Login');
        }

        /** Setup Title and Welcome Message **/
        $data['title'] = "System Route";
        $data['welcomeMessage'] = "Welcome to the System Route.  Use Caustion when Editing System Route, it can break your site.";
        $data['csrfToken'] = Csrf::makeToken('route');

        /** Setup Breadcrumbs **/
        $data['breadcrumbs'] = "
          <li class='breadcrumb-item'><a href='".DIR."AdminPanel'><i class='fa fa-fw fa-cog'></i> Admin Panel</a></li>
          <li class='breadcrumb-item active'><i class='fa fa-fw fa-user'></i>".$data['title']."</li>
        ";

        /** Check to see if Admin is updating System Route **/
    	if(isset($_POST['submit'])){
    		// Check to make sure the csrf token is good
    		if (Csrf::isTokenValid('route')) {
                // Check for update group
                if($_POST['update_route'] == "true"){
      				// Catch password inputs using the Request helper
                    $id = Request::post('id');
                    $controller = Request::post('controller');
                    $method = Request::post('method');
                    $url = Request::post('url');
                    $arguments = Request::post('arguments');
                    $enable = Request::post('enable');

      				// Run the update group script
      				if($this->model->updateRoute($id, $controller, $method, $url, $arguments, $enable)){
      					// Success
                        \Libs\SuccessMessages::push('You Have Successfully Updated Controller '.$controller, 'AdminPanel-SystemRoute/'.$id);
      				}else{
      					// Fail
      					$error[] = "Route Update Failed";
      				}
                }
                //Check for delete route
                if($_POST['delete_route'] == "true"){
                    // Check to see what Route Admin is going to delete
                    $id = Request::post('id');

                    // Delete the Route
                    if($this->model->deleteRoute($id)){
                        // Success
                        \Libs\SuccessMessages::push('You Have Successfully Deleted a Route', 'AdminPanel-SystemRoutes');
                    }else{
                        // Fail
                        $error[] = "Route Delete Failed";
                    }
                }
            }
    	}



        /** Get System Route from Database **/
        $data['system_route'] = $this->model->getRoute($id);

        /** Load The View **/
        Load::View("AdminPanel/SystemRoute", $data, "", "AdminPanel");

    }
}
