<?php namespace App\Controllers;

/*
* Home Pages Controller
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/

use App\System\Controller,
    App\System\Load,
    App\Models\Trailers as TrailersModel,
    Libs\Assets,
    App\System\Error,
	Libs\Csrf,
	Libs\Request,
    Libs\Auth\Auth as AuthHelper,
    App\Models\Users as Users,
    App\Models\Members as MembersModel;

define('TRAILER_CHECK_PAGEINATOR_LIMIT', '20');  // Sets up listing page limit

class Trailers extends Controller {

  private $model;
  private $pages;

  public function __construct(){
    parent::__construct();
	$this->language->load('Welcome');
    $this->model = new TrailersModel();
    $this->pages = new \Libs\Paginator(TRAILER_CHECK_PAGEINATOR_LIMIT);  // How many rows per page
  }

    /* Home Method */
    public function TrailerChecks($current_page = '1', $yard_id_url = null){

        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            //** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You Must Be Logged In To View That Page!', 'Login');
        }
        if(isset($yard_id_url)){
            $yard_id = $yard_id_url;
        }else{
            $yard_id = Request::post('yard_id');
        }

        if(empty($yard_id)){
            $total_num_checks = $this->model->recentYardChecks();
            $search_url = "";
        }else{
            $total_num_checks = $this->model->recentYardChecks($yard_id);
            $search_url = "/$yard_id";
        }

        // Set total number of rows for paginator
        $total_num_checks = count($total_num_checks);
        $this->pages->setTotal($total_num_checks);

        // Send page links to view
        $pageFormat = SITE_URL."YardChecks/"; // URL page where pages are
        $data['pageLinks'] = $this->pages->pageLinks($pageFormat, $search_url, $current_page);
        $data['current_page_num'] = $current_page;

        // Get Yards List
        $data['all_yards'] = $this->model->getYards();

        if(filter_var($yard_id, FILTER_VALIDATE_INT)){
            // Get Previous 20 checks for listing
            $data['recent_yard_checks'] = $this->model->recentYardChecks($yard_id, $this->pages->getLimit($current_page, TRAILER_CHECK_PAGEINATOR_LIMIT));
            $data['yard_info'] = $this->model->getCurrentYard($yard_id);
        }else{
            // Get Previous 20 checks for listing
            $data['recent_yard_checks'] = $this->model->recentYardChecks(null, $this->pages->getLimit($current_page, TRAILER_CHECK_PAGEINATOR_LIMIT));
        }

        Load::View("Trailers::Home", $data, "Trailers::Trailers-Sidebar::Right");
    }


    /* Home Method */
    public function Yards($current_page = '1', $yard_id_url = null){

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
            \Libs\ErrorMessages::push('You Must Be Logged In To View That Page!', 'Login');
        }

        $total_num_checks = $this->model->getYards();

        // Set total number of rows for paginator
        $total_num_checks = count($total_num_checks);
        $this->pages->setTotal($total_num_checks);

        // Send page links to view
        $pageFormat = SITE_URL."Yards/"; // URL page where pages are
        $data['pageLinks'] = $this->pages->pageLinks($pageFormat, $search_url, $current_page);
        $data['current_page_num'] = $current_page;

        // Get Yards List
        $data['yards'] = $this->model->getYards($this->pages->getLimit($current_page, TRAILER_CHECK_PAGEINATOR_LIMIT));

        Load::View("Trailers::Yards", $data, "Trailers::Trailers-Sidebar::Right");
    }

    /* Home Method */
    public function NewYardCheck(){


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

		$data['csrfToken'] = Csrf::makeToken('yard_check');


		// Check to make sure admin is trying to create new yard check
		if(isset($_POST['submit'])){
				// Check to make sure the csrf token is good
				if (Csrf::isTokenValid('yard_check')) {
				//Check for create group
				if($_POST['create_yard_check'] == "true"){
                    $yard_id = Request::post('yard_id');
                    if(filter_var($yard_id, FILTER_VALIDATE_INT)){
    					// Run the update group script
    					$new_yard_check_id = $this->model->createYardCheck($u_id, $yard_id);
    					if($new_yard_check_id){
    					  /** Group Create Success **/
    					  \Libs\SuccessMessages::push('You Have Successfully Started a New Yard Check', 'YardCheck/'.$new_yard_check_id);
    					}else{
    					  /** Group Create Error. Show Error **/
    					  \Libs\ErrorMessages::push('Yard Check Creation Error!', 'NewYardCheck');
    					}
                    }else{
                      /** Group Create Error. Show Error **/
                      \Libs\ErrorMessages::push('You Must Select a Yard to Continue!', 'NewYardCheck');
                    }
				}
			}
		}

        $data['all_yards'] = $this->model->getYards();

        Load::View("Trailers::NewYardCheck", $data, "Trailers::Trailers-Sidebar::Right");
    }


    /* Home Method */
    public function YardCheck($count_id, $tc_id = null){


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

		$data['count_id'] = $count_id;

		$data['csrfToken'] = Csrf::makeToken('yard_check');

        $data['current_count'] = $this->model->getCurrentYardCheck($count_id);

        $data['yard_check_info'] = $this->model->getCurrentYardCheckInfo($count_id);

        if(empty($data['yard_check_info'])){
            /** Yard Check Id Does Not Exist **/
            \Libs\ErrorMessages::push('Yard Check Does Not Exist!', 'YardChecks');
        }

        $yard_id = $data['yard_check_info'][0]->yard_id;

        $data['yard_info'] = $this->model->getCurrentYard($yard_id);

		// Check to make sure admin is trying to create new yard check
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('yard_check')) {
				//Check for create group
				if($_POST['yard_check'] == "true"){

					$trailer = Request::post('trailer');
					$status = Request::post('status');
					$notes = Request::post('notes');

					if($_POST['new_update'] == "new"){
                        // Make sure there is a trailer number
                        if(empty($trailer)){
                            /** No Trailer Number Error **/
                            \Libs\ErrorMessages::push('You Must Enter a Trailer Number!', 'YardCheck/'.$count_id);
                        }
                        // Check to see if trailer has already been added to current list
                        if($this->model->checkForTrailer($count_id, $trailer)){
                            /** Group Create Error. Show Error **/
                            \Libs\ErrorMessages::push('Trailer '.$trailer.' Has Already Been Added To Current Yard Check!', 'YardCheck/'.$count_id);
                        }else{
    						// Run the add script
    						if($this->model->addTrailerToYardCheck($count_id, $u_id, $trailer, $status, $notes, $yard_id)){
    						  /** Group Create Success **/
    						  \Libs\SuccessMessages::push('You Have Successfully Added Trailer To Yard Check', 'YardCheck/'.$count_id);
    						}else{
    						  /** Group Create Error. Show Error **/
    						  \Libs\ErrorMessages::push('Yard Check Trailer Add Error!', 'YardCheck/'.$count_id);
    						}
                        }
					}
					else if($_POST['new_update'] == "update"){
                        // Make sure there is a trailer number
                        if(empty($trailer)){
                            /** No Trailer Number Error **/
                            \Libs\ErrorMessages::push('You Must Enter a Trailer Number!', 'YardCheck/'.$count_id);
                        }
						// Run the update script
						if($this->model->updateTrailerToYardCheck($tc_id, $count_id, $u_id, $trailer, $status, $notes)){
						  /** Group Create Success **/
						  \Libs\SuccessMessages::push('You Have Successfully Updated Trailer For Yard Check', 'YardCheck/'.$count_id);
						}else{
						  /** Group Create Error. Show Error **/
						  \Libs\ErrorMessages::push('Yard Check Trailer Update Error!', 'YardCheck/'.$count_id);
						}
					}
                    else if($_POST['new_update'] == "delete"){
                        $trailer_id = Request::post('trailer_id');
                        // Run the update script
                        if($this->model->deleteYardCheckTrailer($trailer_id)){
                          /** Group Create Success **/
                          \Libs\SuccessMessages::push('You Have Successfully Deleted Trailer From Yard Check', 'YardCheck/'.$count_id);
                        }else{
                          /** Group Create Error. Show Error **/
                          \Libs\ErrorMessages::push('Yard Check Trailer Delete Error!', 'YardCheck/'.$count_id);
                        }
                    }
                    else if($_POST['new_update'] == "delete_yard_check"){
                        $yc_id = Request::post('yc_id');
                        // Run the update script
                        if($this->model->deleteYardCheck($yc_id)){
                          /** Group Create Success **/
                          \Libs\SuccessMessages::push('You Have Successfully Deleted Yard Check', 'YardChecks');
                        }else{
                          /** Group Create Error. Show Error **/
                          \Libs\ErrorMessages::push('Yard Check Delete Error!', 'YardCheck/'.$count_id);
                        }
                    }
				}
                if($_POST['yard_check_email'] == "true"){
                    $yc_email = Request::post('yc_email');
                    // Run the email script
                    if($this->model->sendYardCheckEmail($count_id, $yc_email)){
                      /** Group Create Success **/
                      \Libs\SuccessMessages::push('You Have Successfully Emailed Yard Check to: '.$yc_email , 'YardCheck/'.$count_id);
                    }else{
                      /** Group Create Error. Show Error **/
                      \Libs\ErrorMessages::push('Yard Check Email Error!', 'YardCheck/'.$count_id);
                    }
                }
			}
		}

		if(isset($tc_id)){
			$data['edit_count_data'] = $this->model->getCurrentYardCheckEdit($count_id, $tc_id);
		}

        $data['count_empty'] = $this->model->getTotalCount($count_id, 'Empty');
        $data['count_pca'] = $this->model->getTotalCount($count_id, $data['yard_info'][0]->name.' Load');
        $data['count_carrier'] = $this->model->getTotalCount($count_id, 'Milan Load');

        Load::View("Trailers::YardCheck", $data, "Trailers::Trailers-Sidebar::Right");
    }



   /* Home Method */
    public function EmailYardCheck($count_id){


		$current_count = $this->model->getCurrentYardCheck($count_id);

        if(isset($current_count)){

            echo "
                <style>
                table {
                    border-collapse: collapse;
                }

                th, td {
                    text-align: left;
                    padding: 8px;
                }

                tr:nth-child(even){background-color: #f2f2f2}
                </style>
            ";

			echo "<table cellspacing='0' cellpadding='5' border='1'><tr>";
			echo "<th>Trailer #</th><th>Status</th><th>Notes</th>";
			echo "</tr>";
			foreach($current_count as $row) {
				echo "<tr><td>$row->trailer</td><td>$row->status</td><td>$row->notes</td>";
				echo "</tr>";
			}
			echo "</table>";

		}
    }


    /* Home Method */
    public function YardEdit($yard_id = null, $yard_name = null, $yard_address = null, $yard_city = null, $yard_state = null, $yard_zip = null){


        /** Check to see if user is logged in **/
        if($data['isLoggedIn'] = $this->auth->isLogged()){
            //** User is logged in - Get their data **/
            $u_id = $this->auth->user_info();
            $data['currentUserData'] = $this->user->getCurrentUserData($u_id);
            $data['isAdmin'] = $this->user->checkIsAdmin($u_id);
            $data['is_mod'] = $this->auth->checkIsMod($u_id);
            if(!$data['is_mod']){
              /** User Not logged in - kick them out **/
              \Libs\ErrorMessages::push("You do not have permission to view that page!", 'Login');
            }
        }else{
            /** User Not logged in - kick them out **/
            \Libs\ErrorMessages::push('You Must Be Logged In To View That Page!', 'Login');
        }

		$data['csrfToken'] = Csrf::makeToken('yard_edit');


		// Check to make sure admin is trying to create new yard check
		if(isset($_POST['submit'])){
			// Check to make sure the csrf token is good
			if (Csrf::isTokenValid('yard_edit')) {
				//Check for create group
				if($_POST['yard_edit'] == "true"){
					$name = Request::post('name');
					$address = Request::post('address');
                    $city = Request::post('city');
                    $state = Request::post('state');
                    $zip = Request::post('zip');

					if($_POST['new_update'] == "new"){
                        // Check to see if Yard Name Alread Exists
						if($this->model->checkYard($name)){
                            /** Group Create Error. Show Error **/
                            \Libs\ErrorMessages::push('There is Already a Yard With That Customer Name!', 'YardEdit/0/'.$name.'/'.$address.'/'.$city.'/'.$state.'/'.$zip);
                        }else{
    						// Run the add script
    						if($this->model->addNewYard($name, $address, $city, $state, $zip, $u_id)){
    						  /** Group Create Success **/
    						  \Libs\SuccessMessages::push('You Have Successfully Added New Yard', 'Yards');
    						}else{
    						  /** Group Create Error. Show Error **/
    						  \Libs\ErrorMessages::push('New Yard Add Error!', 'Yards/');
    						}
                        }
					}
					else if($_POST['new_update'] == "update"){
						// Run the update script
						if($this->model->updateYard($yard_id, $name, $address, $city, $state, $zip, $u_id)){
						  /** Group Create Success **/
						  \Libs\SuccessMessages::push('You Have Successfully Updated Yard - '.$name, 'Yards');
						}else{
						  /** Group Create Error. Show Error **/
						  \Libs\ErrorMessages::push('Yard Update Error!', 'Yards');
						}
					}
                    else if($_POST['new_update'] == "delete_yard"){
                        // Run the update script
                        if($this->model->deleteYard($yard_id)){
                          /** Group Create Success **/
                          \Libs\SuccessMessages::push('You Have Successfully Deleted Yard - '.$name, 'Yards');
                        }else{
                          /** Group Create Error. Show Error **/
                          \Libs\ErrorMessages::push('Yard Delete Error!', 'Yards');
                        }
                    }
				}
			}
		}else{
            if(isset($yard_name) || isset($yard_address) || isset($yard_city) || isset($yard_state) || isset($yard_zip)){
                $data['edit_yard_data_name'] = $yard_name;
                $data['edit_yard_data_address'] = $yard_address;
                $data['edit_yard_data_city'] = $yard_city;
                $data['edit_yard_data_state'] = $yard_state;
                $data['edit_yard_data_zip'] = $yard_zip;
            }
        }

        if(isset($yard_id) && $yard_id != 0){
            $data['yard_id'] = $yard_id;
            $data['edit_yard_data'] = $this->model->getCurrentYard($yard_id);
            $data['edit_yard_data_name'] = $data['edit_yard_data'][0]->name;
            $data['edit_yard_data_address'] = $data['edit_yard_data'][0]->address;
            $data['edit_yard_data_city'] = $data['edit_yard_data'][0]->city;
            $data['edit_yard_data_state'] = $data['edit_yard_data'][0]->state;
            $data['edit_yard_data_zip'] = $data['edit_yard_data'][0]->zip;
        }


        Load::View("Trailers::YardEdit", $data, "Trailers::Trailers-Sidebar::Right");
    }


}
